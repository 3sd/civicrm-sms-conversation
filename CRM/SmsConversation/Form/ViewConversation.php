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
    $convRecord = json_decode($convContact['values'][0]['conversation_record'], TRUE);

    $conversationRecord = array();
    $prevValid = TRUE;
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
