<?php

class CRM_SmsConversation_Action {

  /**
   * Check if the SMS is a valid answer for the action
   * @param $action
   * @param $sms
   *
   * @return bool
   */
  static function validAnswer($action, $sms) {
    // answer_pattern will be in regex format: eg /abc/i
    // If this is extended to support alternative matching (eg. optionvalue) we would add a switch statement matching on the first character.
    if (preg_match($action['answer_pattern'], $sms)) {
      return $action;
    }
    return FALSE;
  }

  /**
   * Get all actions for question ID
   * @param $questionId
   *
   * @return bool
   */
  static function get($questionId) {
    // Contact is having a conversation, get the possible actions for the question
    $convActions = civicrm_api3('SmsConversationAction', 'get', array(
        'question_id' => $questionId,
      )
    );

    if (!empty($convActions['is_error']) || !isset($convActions['values'])) {
      // Something went wrong getting actions
      return FALSE;
    }
    return $convActions['values'];
  }

  /**
   * Process action
   * @param $action
   *
   * @return bool
   */
  static function processAction($action, $contactId, $conversationId) {
    // Perform action based on action_type, action_data and answer
    // TODO processAction
    Civi::log('processAction');
    if (!isset($action['action_type'])) {
      return FALSE;
    }

    switch ($action['action_type']) {
      case 1: // Ask another question
        return CRM_SmsConversation_Action::actionAskQuestion($action, $contactId);
        Civi::log('Ask another question not implemented');
        break;
      case 2: // Add contact to group
        Civi::log('Add contact to group not implemented');
        break;
      case 3: // Record answer in custom field
        Civi::log('Record answer in custom field not implemented');
        break;
      case 4: // Trigger CiviRule
        Civi::log('Trigger CiviRule not implemented');
        break;
      default:
        Civi::log('SmsConversationAction::processAction: '.$action['action_type'].' not implemented');
        CRM_SmsConversation::end($contactId, $conversationId);

    }
  }

  /**
   * Process the ask question action
   * @param $action
   */
  static function actionAskQuestion($action, $contactId) {
    // Trigger another question
    // action_date = next question_id
    // Trigger the question
    CRM_SmsConversation_Question::ask($action['question_id'], $contactId);
  }

}
