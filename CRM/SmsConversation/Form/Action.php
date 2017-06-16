<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_SmsConversation_Form_Action extends CRM_Core_Form {

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
  $this->conversation = civicrm_api3('SmsConversation', 'getsingle', ['id' => $this->conversationId]);

  $this->assign('action', $this->action);
  $session = CRM_Core_Session::singleton();
  $this->context = CRM_Utils_System::url('civicrm/sms/conversation/view', "id={$this->conversationId}");
  $session->pushUserContext($this->context);
  $this->controller->_destination = $this->context;
}

  public function buildQuickForm() {

    // add form elements
    $this->add( 'text', 'text', ts('Question'), ['size' => 40], TRUE);
    $this->add( 'text', 'text_invalid', ts('Invalid text'), ['size' => 40]);

    // when adding a conversation, we ask for the text of the first question
    if($this->action == CRM_Core_Action::ADD){
      CRM_Utils_System::setTitle(ts("Add a question to '{$this->conversation['name']}'"));
      $this->addButtons([
        array('type' => 'cancel', 'name' => 'Cancel'),
        array('type' => 'submit', 'name' => 'Add', 'isDefault' => TRUE)
      ]);
    }elseif($this->action == CRM_Core_Action::UPDATE){
      CRM_Utils_System::setTitle(ts('Update an SMS conversation question'));
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
    $params['text'] = $values['text'];
    $params['text_invalid'] = $values['text_invalid'];
    $params['conversation_id'] = $this->conversationId;
    if($this->action == CRM_Core_Action::UPDATE){
      $params['id'] = $this->questionId;
    }
    $question = civicrm_api3('SmsConversationQuestion', 'create', $params);
    var_dump($question);
    parent::postProcess();
  }
}
