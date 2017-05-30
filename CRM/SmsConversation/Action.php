<?php

class CRM_SmsConversation_Action {

  function __construct($activity) {
    if (!isset($activity['source_contact_id'])) {
      Civi::log('SmsConversation_Action::_construct: Missing parameters!');
      return FALSE;
    }
    $this->inboundSMSActivityTypeId = CRM_Core_OptionGroup::getValue('activity_type', 'Inbound SMS', 'name');
    $this->sourceContactId = $activity['source_contact_id'];
    $this->activityDateTime = $activity['activity_date_time'];
    $this->sms = $activity['details'];
  }

  function process() {
    // Is contact in a conversation? Get the question ID
    try {
      $convContact = civicrm_api3('SmsConversationContact', 'getsingle', array(
        'contact_id' => $this->sourceContactId,
      ));
    }
    catch (Exception $e) {
      return FALSE;
    }

    // Store conversation Id
    $this->conversationId = $convContact['conversation_id'];

    // Contact is having a conversation, get the action for question ID
    try {
      $convAction = civicrm_api3('SmsConversationAction', 'getsingle', array(
        'question_id' => $convContact->question_id,
      ));
    }
    catch (Exception $e) {
      Civi::log('CRM_SmsConversation_Action::process No action found for question id: '. $convContact->question_id);
      return FALSE;
    }

    // parse Answer
    $answer = $this->parseAnswer();
    if ($answer) {
      // Valid answer
      $this->doAction();
    }
    else {
      // Invalid answer
      $this->invalidAnswer($convAction);
    }

  }

  function parseAnswer() {
    $answer = $this->answer;
    if (!empty($answer)) {
      // Valid answer, return parsed answer.
      // TODO
      return $answer;
    }
    else {
      // Invalid answer
      return FALSE;
    }
  }

  function invalidAnswer($convAction) {
    // Send invalid_text as new SMS to contact
    // TODO Send SMS
    if (!empty($convAction['invalid_text'])) {
      Civi::log('Send invalid answer SMS'); // FIXME: debug log
    }
    else {
      Civi::log('End conversation'); // FIXME: debug log
      // TODO: End conversation
    }
  }

  function doAction() {
    // Perform action based on action_type, action_data and answer
    // TODO doAction
    Civi::log('doAction'); // FIXME: debug log
  }

}
