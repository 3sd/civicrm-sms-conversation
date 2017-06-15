<?php

class CRM_SmsConversation_BAO_Question extends CRM_SmsConversation_DAO_Question {

  /**
   * Create a new SmsConversationQuestion based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Conversations_DAO_SmsConversationQuestion|NULL
   */
  public static function create($params) {
    $className = 'CRM_SmsConversation_DAO_Question';
    $entityName = 'SmsConversationQuestion';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  /**
   * Get the question specified by questionId
   *
   * @param $questionId
   *
   * return array|bool
   */
  static function getQuestion($questionId) {
    $question = civicrm_api3('SmsConversationQuestion', 'get', [
      'id' => $questionId,
    ]);

    if (!empty($question['is_error']) || empty($question['count'])) {
      return FALSE;
    }
    else {
      return $question['values'][$questionId];
    }
  }

  /**
   * Ask a question to contactId
   * @param $questionId
   * @param $contactId
   *
   * @return bool
   */
  static function ask($questionId, $contactId, $convContact) {
    $question = CRM_SmsConversation_BAO_Question::getQuestion($questionId);
    if ($question) {
      // Record the new question id
      $convContact = civicrm_api3('SmsConversationContact', 'create', array(
        'id' => $convContact['id'],
        'status_id' => $convContact['status_id'],
        'current_question_id' => $questionId,
      ));
      if (!empty($convContact['is_error'])) {
        return FALSE;
      }

      return CRM_SmsConversation_Processor::sendSMS($contactId, $question['text'], $convContact['source_contact_id']);
    }
    return FALSE;
  }


}
