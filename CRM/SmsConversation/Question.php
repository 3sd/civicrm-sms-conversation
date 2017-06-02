<?php

/**
 * Class CRM_SmsConversation_Question
 */
class CRM_SmsConversation_Question {

  /**
   * Get the question specified by questionId
   *
   * @param $questionId
   *
   * return array|bool
   */
  static function get($questionId) {
    $question = civicrm_api3('SmsConversationQuestion', 'get', [
      'sequential' => 1,
      'id' => $questionId,
    ]);

    if (!empty($question['is_error']) || empty($question['count'])) {
      return FALSE;
    }
    else {
      return $question['values'][0];
    }
  }

  /**
   * Ask a question to contactId
   * @param $questionId
   * @param $contactId
   *
   * @return bool
   */
  static function ask($questionId, $contactId) {
    $question = CRM_SmsConversation_Question::get($questionId);
    $actions = CRM_SmsConversation_Action::get($questionId);
    if ($question) {
      CRM_SmsConversation_Processor::sendSMS($contactId, $question['text']);
    }
    return FALSE;
  }
}
