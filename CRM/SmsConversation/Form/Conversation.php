<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_SmsConversation_Form_Conversation extends CRM_Core_Form {

public function preProcess(){
  $this->action = CRM_Utils_Request::retrieve('action', 'String', $this);
  $this->conversationId = CRM_Utils_Request::retrieve('id', 'String', $this);
  if($this->conversationId){
    $this->conversation = civicrm_api3('SmsConversation', 'getsingle', ['id' => $this->conversationId]);
  }
  $this->assign('action', $this->action);
  $session = CRM_Core_Session::singleton();
  $session->pushUserContext(CRM_Utils_System::url('civicrm/sms/conversations'));

}

  public function buildQuickForm() {

    // add form elements
    $this->add( 'text', 'name', ts('Name'), ['size' => 40], TRUE);

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
      CRM_Utils_System::setTitle(ts('Update SMS conversation'));
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
      return $this->conversation;
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
