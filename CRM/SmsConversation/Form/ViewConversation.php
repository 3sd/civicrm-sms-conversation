<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_SmsConversation_Form_ViewConversation extends CRM_Core_Form {
  public function buildQuickForm() {
    $this->contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $this->conversation = CRM_Utils_Request::retrieve('conversation', 'Positive', $this, TRUE);
    $this->action = CRM_Utils_Request::retrieve('action', 'String', $this, TRUE);
    $this->assign('action', $this->action);
    $this->assign('conversation', $this->conversation);

    $convContact = civicrm_api3('SmsConversationContact', 'get', ['id' => $this->conversation, 'contact_id' => $this->contactId, 'sequential' => 1]);

    if ($this->action == CRM_Core_Action::DELETE) {
      CRM_Utils_System::setTitle('Delete Conversation');
      $this->addButtons(array(
        array(
          'type' => 'cancel',
          'name' => ts('Cancel'),
          'isDefault' => TRUE,
        ),
        array(
          'type' => 'submit',
          'name' => ts('Delete'),
          'isDefault' => FALSE,
        ),
      ));
      return;
    }
    elseif ($this->action == CRM_Core_Action::UPDATE) {
      CRM_Utils_System::setTitle('Cancel Conversation');
      $this->addButtons(array(
        array(
          'type' => 'cancel',
          'name' => ts('No'),
          'isDefault' => TRUE,
        ),
        array(
          'type' => 'submit',
          'name' => ts('Yes'),
          'isDefault' => FALSE,
        ),
      ));
      return;
    }

    $inProgressId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact', 'status_id', 'In Progress');
    $scheduledId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact', 'status_id', 'Scheduled');
    $completedId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact', 'status_id', 'Completed');

    $convContact = $convContact['values'][0];
    $convRecord = json_decode($convContact['conversation_record'], TRUE);

    $conversationRecord = array();
    $prevValid = TRUE;

    // Add each conversation record
    foreach ($convRecord as $conv) {
      $convQuestion = civicrm_api3('SmsConversationQuestion', 'getsingle', ['id' => $conv['q']]);
      if (!$prevValid) {
        $conv['question'] = $convQuestion['text_invalid'];
      }
      else {
        $conv['question'] = $convQuestion['text'];
      }
      $conv['vPrev'] = $prevValid;
      $conversationRecord[] = $conv;
      $prevValid = $conv['v'];
    }

    // Add next question to view as it's not stored in conversation record until a response is received.
    // If the conversation is completed we have everything recorded so only display that.
    // If there is no conversation record the conversation hasn't started (in state scheduled)
    // If there is no current_question_id then we look for the start_question_id.  The conversation has started, but we don't have a reply yet.
    if ((empty($convRecord) || !empty($convContact['current_question_id'])) && $convContact['status_id'] != $completedId)
    {

      $conv = array(); // Reset array so we don't use previous values
      if (empty($convContact['current_question_id'])) {
        $conversation = civicrm_api3('SmsConversation', 'getsingle', ['id' => $convContact['conversation_id']]);
        $convContact['current_question_id'] = $conversation['start_question_id'];
      }
      if (empty($convContact['current_question_id'])) return;
      $convQuestion = civicrm_api3('SmsConversationQuestion', 'getsingle', ['id' => $convContact['current_question_id']]);

      $conv['question'] = $convQuestion['text'];
      $conv['v'] = TRUE;
      $conv['vPrev'] = TRUE;
      if ($convContact['status_id'] == $scheduledId) {
        $conv['a'] = '<em style="color:grey">- Not sent yet -</em>';
      }
      else {
        $conv['a'] = '<em style="color:grey">- Sent -</em>';
      }
      if (isset(end($conversationRecord)['q'])) {
        $conv['q'] = end($conversationRecord)['q']+1;
      }
      $conversationRecord[] = $conv;
    }

    $this->assign('conversationRecord', $conversationRecord);
    $this->addButtons(array(
      array(
        'type' => 'cancel',
        'name' => ts('Close'),
        'isDefault' => TRUE,
      ),
    ));
    CRM_Utils_System::setTitle('Sms Conversation '.$this->conversation);

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    if ($this->action == CRM_Core_Action::DELETE) {
      if (!empty($this->conversation)) {
        $result = civicrm_api3('SmsConversationContact', 'delete', ['id' => $this->conversation]);
        return;
      }
    }
    elseif ($this->action == CRM_Core_Action::UPDATE) {
      if (!empty($this->conversation)) {
        CRM_SmsConversation_BAO_Contact::updateStatus($this->conversation, 'Cancelled');
        return;
      }
    }
    parent::postProcess();
  }

}
