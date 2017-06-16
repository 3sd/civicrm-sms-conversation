<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_SmsConversation_Form_ViewConversation extends CRM_Core_Form {
  public function buildQuickForm() {
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $this->_conversation = CRM_Utils_Request::retrieve('conversation', 'Positive', $this, TRUE);

    $convContact = civicrm_api3('SmsConversationContact', 'get', ['id' => $this->_conversation, 'contact_id' => $this->_contactId, 'sequential' => 1]);
    $convContact = $convContact['values'][0];
    $convRecord = json_decode($convContact['conversation_record'], TRUE);

    $conversationRecord = array();
    $prevValid = TRUE;
    if (empty($convRecord)) {
      $conv = civicrm_api3('SmsConversation', 'getsingle', ['id' => $convContact['conversation_id']]);
      $convQuestion = civicrm_api3('SmsConversationQuestion', 'getsingle', ['id' => $conv['start_question_id']]);
      $conv['question'] = $convQuestion['text'];
      $conv['v'] = TRUE;
      $conv['vPrev'] = TRUE;
      $conv['a'] = '<em style="color:grey">- Not sent yet -</em>';
      $conversationRecord[] = $conv;
    }
    else {
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
    }
    $inProgressId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact', 'status_id', 'In Progress');
    if ($convContact['status_id'] == $inProgressId) {
      // Add next question to conversation detail view as we are awaiting a response
      $convQuestion = civicrm_api3('SmsConversationQuestion', 'getsingle', ['id' => $convContact['current_question_id']]);
      $conv['question'] = $convQuestion['text'];
      $conv['v'] = TRUE;
      $conv['vPrev'] = TRUE;
      $conv['a'] = '<em style="color:grey">- Awaiting reply -</em>';
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
    CRM_Utils_System::setTitle('Sms Conversation '.$this->_conversation);

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    parent::postProcess();
  }

}
