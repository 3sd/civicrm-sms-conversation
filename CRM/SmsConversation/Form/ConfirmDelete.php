<?php

class CRM_SmsConversation_Form_ConfirmDelete extends CRM_Core_Form {

  public function preProcess(){
    $this->action = CRM_Utils_Request::retrieve('action', 'String', $this);
    $this->assign('action', $this->action);

    $this->id = CRM_Utils_Request::retrieve('id', 'Integer', $this);
    $this->entityType = CRM_Utils_Request::retrieve('entity', 'String', $this);
    if(!in_array($this->entityType, ['SmsConversation', 'SmsConversationAction', 'SmsConversationQuestion'])){
      throw new Exception('Bad entity');
    }
    $this->entityNames = [
      'SmsConversation' => 'conversation',
      'SmsConversationQuestion' => 'question',
      'SmsConversationAction' => 'action'
    ];

    $title = ts("Delete %1", [1=>$this->entityNames[$this->entityType]]);
    CRM_Utils_System::setTitle(ts($title));

    $this->entity = civicrm_api3($this->entityType, 'getsingle', ['id'=> $this->id]);

    $this->assign('question', ts("Are you sure you want to delete this %1?", [1=>$this->entityNames[$this->entityType]]));

    if($this->entityType == 'SmsConversation'){
      $description = $this->entity['name'];
      $this->assign('description', "'$description'");
      $this->conversationId = $this->entity['id'];
    }elseif($this->entityType == 'SmsConversationQuestion'){
      $description = $this->entity['text'];
      $this->conversationId = $this->entity['conversation_id'];
      $this->assign('description', "'$description'");
    }elseif($this->entityType == 'SmsConversationAction'){
      $question = civicrm_api3('SmsConversationQuestion', 'getsingle', ['id'=> $this->entity['question_id']]);
      $this->conversationId = $question['conversation_id'];
    }
    $session = CRM_Core_Session::singleton();
    $this->context = CRM_Utils_System::url('civicrm/sms/conversation/view', "id={$this->conversationId}");
    $this->controller->_destination = $this->context;
    $session->pushUserContext($this->context);
  }

  public function buildQuickForm() {

    $this->addButtons([
      array('type' => 'cancel', 'name' => 'Cancel'),
      array('type' => 'submit', 'name' => 'Delete', 'isDefault' => TRUE)
    ]);
    parent::buildQuickForm();
  }

  public function postProcess() {
    $session = CRM_Core_Session::singleton();
    civicrm_api3($this->entityType, 'delete', ['id' => $this->id]);
    $session->setStatus(ts('%1 deleted', [1 => ucfirst($this->entityNames[$this->entityType])]), '', 'success');
  }
}
