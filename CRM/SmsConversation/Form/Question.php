<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_SmsConversation_Form_Question extends CRM_Core_Form {

public function preProcess(){
  $this->action = CRM_Utils_Request::retrieve('action', 'String', $this);
  $this->questionId = CRM_Utils_Request::retrieve('id', 'String', $this);

  //We should always be able to determine a convesation ID. When adding a
  //Wquestion, it should be in the URL. When editing a question, we should be
  //able to rertrieve it from the SmsConversationQuestion being edited
  $this->conversationId = CRM_Utils_Request::retrieve('conversation_id', 'String', $this);
  if($this->questionId){
    $this->question = civicrm_api3('SmsConversationQuestion', 'getsingle', ['id' => $this->questionId]);
    $this->conversationId = $this->question['conversation_id'];
  }
  $this->assign('action', $this->action);
  $session = CRM_Core_Session::singleton();
  $session->pushUserContext(CRM_Utils_System::url('civicrm/sms/conversation/view', "id={$this->conversationId}"));
}

  public function buildQuickForm() {

    // add form elements
    $this->add( 'text', 'text', ts('Question'), ['size' => 40], TRUE);
    $this->add( 'text', 'text_invalid', ts('Invalid text'), ['size' => 40], TRUE);

    // when adding a conversation, we ask for the text of the first question
    if($this->action == CRM_Core_Action::ADD){
      CRM_Utils_System::setTitle(ts('Add an SMS conversation'));
      $this->add( 'text', 'start_question_text', ts('First question'), ['size' => 40], TRUE);
      $this->addButtons([
        array('type' => 'cancel', 'name' => 'Cancel'),
        array('type' => 'submit', 'name' => 'Add', 'isDefault' => TRUE)
      ]);
    }elseif($this->action == CRM_Core_Action::UPDATE){
      $session = CRM_Core_Session::singleton();
      $session->pushUserContext(CRM_Utils_System::url('civicrm/sms/conversation/view', "id={$this->conversationId}"));
      CRM_Utils_System::setTitle(ts('Update an SMS conversation question'));
      $this->addEntityRef('start_question_id', ts('First question'), [
        'entity' => 'SmsConversationQuestion',
        'api' => [
          'params' => ['conversation_id' => $this->conversationId],
          'label_field' => 'text'
      ],
        'placeholder' => ts('- Select question -'),
        'select' => ['minimumInputLength' => 0]
      ], TRUE);
      $this->addButtons([
        array('type' => 'cancel', 'name' => 'Cancel'),
        array('type' => 'submit', 'name' => 'Update', 'isDefault' => TRUE)
      ]);
    }
    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    if($this->action == CRM_Core_Action::UPDATE){
      return $this->question;
    }
  }


  public function postProcess() {
    $values = $this->exportValues();
    if($this->action == CRM_Core_Action::ADD){
      $conversation = civicrm_api3('SmsConversation', 'create', [
        'name' => $values['name']
      ]);
      $question = civicrm_api3('SmsConversationQuestion', 'create', [
        'text' => $values['start_question_text'],
        'conversation_id' => $conversation['id']
      ]);
      $conversation = civicrm_api3('SmsConversation', 'create', [
        'id' => $conversation['id'],
        'start_question_id' => $question['id']
      ]);
    }else{
      $conversation = civicrm_api3('SmsConversation', 'create', [
        'id' => $this->conversationId,
        'name' => $values['name'],
        'start_question_id' => $values['start_question_id']
      ]);
    }
    $this->controller->_destination = CRM_Utils_System::url('civicrm/sms/conversation/view', "&id={$conversation['id']}");
    parent::postProcess();
  }
}
