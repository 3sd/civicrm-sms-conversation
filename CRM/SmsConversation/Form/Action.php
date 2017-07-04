<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_SmsConversation_Form_Action extends CRM_Core_Form {

  public function preProcess(){
    $this->action = CRM_Utils_Request::retrieve('action', 'String', $this);
    $this->assign('action', $this->action);

    $this->smsActionId = CRM_Utils_Request::retrieve('id', 'String', $this);
    $this->smsActionTypeId = CRM_Utils_Request::retrieve('action_type', 'Integer', $this);
    $this->questionId = CRM_Utils_Request::retrieve('question_id', 'String', $this);

    // We should always be able to determine the question_id and action_type.
    // Either from the URLduring creation or via the retrieved entity during
    // update.
    if($this->smsActionId){
      $this->smsAction = civicrm_api3('SmsConversationAction', 'getsingle', ['id' => $this->smsActionId]);
      $this->questionId = $this->smsAction['question_id'];
      $this->smsActionTypeId = $this->smsAction['action_type'];
    }

    $this->smsActionType = civicrm_api3('OptionValue', 'getsingle', [ 'option_group_id' => 'sms_conversation_action_type', 'value' => $this->smsActionTypeId ]);
    $this->question = civicrm_api3('SmsConversationQuestion', 'getsingle', ['id' => $this->questionId]);
    $this->assign('question', $this->question);

    // Ensure that form rediection happens as one would expect
    $this->conversationId = $this->question['conversation_id'];
    $session = CRM_Core_Session::singleton();
    $this->context = CRM_Utils_System::url('civicrm/sms/conversation/view', "id={$this->conversationId}");
    $session->pushUserContext($this->context);
    $this->controller->_destination = $this->context;

    // Set title
    if($this->action == CRM_Core_Action::ADD){
      $title = ts("Create '%1' action", [1 => $this->smsActionType['label']]);
    }else{
      $title = ts("Update '%1' action", [1 => $this->smsActionType['label']]);
    }
    CRM_Utils_System::setTitle(ts($title));

  }

  public function buildQuickForm() {

    CRM_Core_Resources::singleton()->addScriptFile('civicrm.sms.conversations', 'templates/CRM/SmsConversation/Form/Action.js');

    // The type of pattern matching
    $this->add('select', 'answer_pattern_type', ts('Match'), [
      'anything' => 'Anything',
      'exact' => 'Exact match',
      'contains' => 'Text contains',
      'list-exact' => 'List of exact matches',
      'list-contains' => 'List of \'text contains\'',
      'regexp' => 'Advanced mode'
    ], FALSE, ['class' => 'crm-select2']);

    // The parameters of the match
    $this->add( 'text', 'answer_pattern_raw', ts('Match text'), ['size' => 40], TRUE);
    if($this->smsActionType['name'] == 'question'){
      $this->addEntityRef('next_question_id', ts('Next question'), [
        'entity' => 'SmsConversationQuestion',
        'api' => [
          'params' => ['conversation_id' => $this->conversationId],
          'label_field' => 'text'
        ],
        'placeholder' => ts('- select question -'),
        'select' => ['minimumInputLength' => 0]
      ], TRUE);
      // Add integer rule for question weighting
      $this->add('text', 'weight', ts('Weight'));
      $this->registerRule('weight', 'callback', 'integer', 'CRM_Utils_Rule');
      $this->addRule('weight', ts('must be an integer'), 'integer');

    }elseif($this->smsActionType['name'] == 'add_to_group'){
      $this->addEntityRef('action_data', ts('Add to group'), [
        'entity' => 'Group',
        'api' => [
          'label_field' => 'title',
          'value_field' => 'name'
        ],
        'placeholder' => ts('- select group -'),
        'select' => ['minimumInputLength' => 0]
      ], TRUE);
    }elseif($this->smsActionType['name'] == 'record_field'){
      $this->add('select', 'action_data', ts('Record in field'), array_column(civicrm_api3('Contact', 'getfields', ['action' => 'get'])['values'], 'title', 'name'), TRUE, ['class' => 'crm-select2']);
    }
    // when adding a conversation, we ask for the text of the first question
    if($this->action == CRM_Core_Action::ADD){
      $this->addButtons([
        array('type' => 'cancel', 'name' => 'Cancel'),
        array('type' => 'submit', 'name' => 'Create', 'isDefault' => TRUE)
      ]);
    }elseif($this->action == CRM_Core_Action::UPDATE){
      $this->addButtons([
        array('type' => 'cancel', 'name' => 'Cancel'),
        array('type' => 'submit', 'name' => 'Update', 'isDefault' => TRUE)
      ]);
    }
    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    if($this->smsActionType['name'] == 'question') {
      CRM_SmsConversation_BAO_Action::processNextQuestionActionData($this->smsAction);
    }
    if($this->action == CRM_Core_Action::UPDATE){
      $defaults = $this->smsAction;
      $match = CRM_SmsConversation_Match::decipherPatternType($this->smsAction['answer_pattern']);
      $defaults['answer_pattern_type'] = $match['pattern_type'];
      $defaults['answer_pattern_raw'] = $match['pattern_raw'];
      return $defaults;
    }
  }

  public function postProcess() {

    $values = $this->exportValues();
    switch($values['answer_pattern_type']){
      case 'anything':
        $params['answer_pattern'] = '/.*/';
        break;
      case 'exact':
        $params['answer_pattern'] = '/^'.$values['answer_pattern_raw'].'$/i';
        break;
      case 'contains':
        $params['answer_pattern'] = $params['answer_pattern'] = '/'.$values['answer_pattern_raw'].'/i';
        break;
      case 'list-exact':
        $terms = explode(',', $values['answer_pattern_raw']);
        foreach($terms as &$term){
          $term = trim($term);
          $term = "^$term$";
        }
        $pattern = implode ('|', $terms);
        $params['answer_pattern'] = '/'.$pattern.'/i';
        break;
      case 'list-contains':
        $terms = explode(',', $values['answer_pattern_raw']);
        foreach($terms as &$term){
          $term = trim($term);
        }
        $pattern = implode ('|', $terms);
        $params['answer_pattern'] = '/'.$pattern.'/i';
        break;
      case 'regexp':
        $params['answer_pattern'] = $values['answer_pattern_raw'];
        break;
    }
    $params['question_id'] = $this->questionId;
    $params['action_type'] = $this->smsActionTypeId;
    if (isset($values['question_id']) && isset($values['weight'])) {
      // Save next question action data
      if ($values['weight'] == '') { $values['weight'] = 0; } // '' is treated as valid int
      $params['action_data'] = $values['weight'] .':' . $values['question_id'];
    }
    else {
      $params['action_data'] = $values['action_data'];
    }
    if($this->action == CRM_Core_Action::UPDATE){
      $params['id'] = $this->smsActionId;
    }
    $action = civicrm_api3('SmsConversationAction', 'create', $params);
    parent::postProcess();
  }
}
