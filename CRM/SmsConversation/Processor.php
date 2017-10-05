<?php

class CRM_SmsConversation_Processor {

  function __construct($activity) {
    if (!isset($activity['source_contact_id'])) {
      Civi::log('SmsConversation_Question::_construct: Missing parameters!');
      return FALSE;
    }
    $this->sourceContactId = $activity['source_contact_id'];
    $this->sms = trim($activity['details']);
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
    // We end the conversation if there are no "Ask another question" actions
    $endConversation = TRUE;
    foreach ($convActions as $action) {
      if ($action['action_type'] == 1) {
        $endConversation = FALSE;
        break;
      }
    }

    // Check each action to see if 1 or more match the answer.
    $validActions = array();
    // If we have no actions, we shouldn't be here.  The conversation should have ended when the last SMS was sent
    if (!isset($convActions) || count($convActions) == 0) {
      return FALSE;
    }
    // Get an array of valid actions
    $nextQuestionWeight = 9999; // Set to a very high value
    foreach ($convActions as $action) {
      $validAction = CRM_SmsConversation_BAO_Action::validAnswer($action, $this->sms);
      if ($validAction) {
        if ($validAction['action_type'] == 1) {
          // Only allow one valid next question action, choose the one with lowest (numerical) weighting if there is more than one.
          CRM_SmsConversation_BAO_Action::processNextQuestionActionData($validAction);
          if ($validAction['weight'] < $nextQuestionWeight) {
            $validActions[] = $validAction;
            $nextQuestionWeight = $validAction['weight'];
          }
        }
        else {
          // Not a next question action, so add it as a valid action
          $validActions[] = $validAction;
        }
      }
    }

    $isValidAnswer = count($validActions);

    // Record the conversation
    CRM_SmsConversation_BAO_Contact::recordConversation($convContact, $convQuestion, $this->sms, $isValidAnswer);

    if (!$isValidAnswer) {
      // Send the invalid message
      CRM_SmsConversation_Processor::sendSMS($this->sourceContactId, $convQuestion['text_invalid'], $convContact['source_contact_id']);
    }
    else {
      // Got valid actions, process them
      foreach ($validActions as $action) {
        CRM_SmsConversation_BAO_Action::processAction($action, $this->sourceContactId, $this->conversationId, $this->sms);
      }
    }

    if ($endConversation) {
      // End the conversation
      CRM_SmsConversation_BAO_Contact::endConversation($convContact['id']);
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Send an SMS to a contact
   * @param $contactId
   * @param $text
   *
   * @return bool
   */
  static function sendSMS($contactId, $text, $sourceContactId = NULL) {
    $result = civicrm_api3('Contact', 'sms', array(
      'contact_id' => $contactId,
      'source_contact_id' => $sourceContactId,
      'text' => $text,
    ));

    if (!empty($result['is_error'])) {
      return FALSE;
    }
    return TRUE;
  }
}
