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
    $question = CRM_SmsConversation_BAO_Question::get($questionId);
    $actions = CRM_SmsConversation_BAO_Action::get($questionId);
    if ($question) {
      CRM_SmsConversation_Processor::sendSMS($contactId, $question['text']);
    }
    return FALSE;
  }


}
