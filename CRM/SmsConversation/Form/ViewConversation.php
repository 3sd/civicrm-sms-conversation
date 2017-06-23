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

    // Add next question
    if (empty($convRecord) && ($convContact['status_id'] == $scheduledId)) {
      // Not "In Progress" and no conversation history so the conversation hasn't started.
      $conv = civicrm_api3('SmsConversation', 'getsingle', ['id' => $convContact['conversation_id']]);
      $convQuestion = civicrm_api3('SmsConversationQuestion', 'getsingle', ['id' => $conv['start_question_id']]);
      $conv['question'] = $convQuestion['text'];
      $conv['v'] = TRUE;
      $conv['vPrev'] = TRUE;
      $conv['a'] = '<em style="color:grey">- Not sent yet -</em>';
      $conversationRecord[] = $conv;
    }
    elseif (!empty($convContact['current_question_id'])) {
      // Add next question to conversation detail view as we are awaiting a response (it's not stored in conversation record until we get a reply)
      $convQuestion = civicrm_api3('SmsConversationQuestion', 'getsingle', ['id' => $convContact['current_question_id']]);
      $conv['question'] = $convQuestion['text'];
      $conv['v'] = TRUE;
      $conv['vPrev'] = TRUE;
      $conv['a'] = '<em style="color:grey">- Sent -</em>';
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
