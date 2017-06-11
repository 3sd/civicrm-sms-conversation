<?php

class CRM_SmsConversation_Processor {

  function __construct($activity) {
    if (!isset($activity['source_contact_id'])) {
      Civi::log('SmsConversation_Question::_construct: Missing parameters!');
      return FALSE;
    }
    $this->sourceContactId = $activity['source_contact_id'];
    $this->sms = $activity['details'];
  }

  /**
   * Process the inbound SMS
   * @return bool
   */
  function inbound() {
    // Is contact in a conversation? Get the question ID

    $convContact = CRM_SmsConversation_BAO_Contact::getCurrentConversation($this->sourceContactId);
    if (!$convContact) {
      // No conversation
      return FALSE;
    }

    // Get the question
    $convQuestion = CRM_SmsConversation_BAO_Question::getQuestion($convContact['current_question_id']);

    // Store conversation Id
    $this->conversationId = $convContact['conversation_id'];

    // Get actions for current question
    $convActions = CRM_SmsConversation_BAO_Action::getAction($convContact['current_question_id']);
    if (!$convActions) {
      return FALSE;
    }

    // Check each action to see if 1 or more match the answer.
    $validActions = array();
    // If we have no actions, we shouldn't be here.  The conversation should have ended when the last SMS was sent
    if (!isset($convActions) || count($convActions) == 0) {
      return FALSE;
    }
    // Get an array of valid actions
    foreach ($convActions as $action) {
      $validAction = CRM_SmsConversation_BAO_Action::validAnswer($action, $this->sms);
      if ($validAction) {
        $validActions[] = $validAction;
      }
    }

    $isValidAnswer = count($validActions);

    // Record the conversation
    CRM_SmsConversation_BAO_Contact::recordConversation($convContact, $convQuestion, $this->sms, $isValidAnswer);

    if (!$isValidAnswer) {
      // Send the invalid message
      CRM_SmsConversation_Processor::sendSMS($this->sourceContactId, $convQuestion['text_invalid']);
    }
    else {
      // Got valid actions, process them
      foreach ($validActions as $action) {
        CRM_SmsConversation_BAO_Action::processAction($action, $this->sourceContactId, $this->conversationId);
      }
    }
  }

  /**
   * Send an SMS to a contact
   * @param $contactId
   * @param $text
   *
   * @return bool
   */
  static function sendSMS($contactId, $text) {
    $result = civicrm_api3('Contact', 'sms', array(
      'id' => $contactId,
      'text' => $text,
    ));

    if (!empty($result['is_error'])) {
      return FALSE;
    }
    return TRUE;
  }
}