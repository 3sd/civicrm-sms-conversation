<?php

class CRM_SmsConversation_BAO_Contact extends CRM_SmsConversation_DAO_Contact {

  /**
   * Create a new SmsConversationContact based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_SmsConversation_DAO_Contact|NULL
   */
  public static function create($params) {
    $className = 'CRM_SmsConversation_DAO_Contact';
    $entityName = 'SmsConversationContact';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  /**
   * Get the current conversation for the contact
   * @param $contactId
   *
   * @return array|bool
   */
  static function getCurrentConversation($contactId) {
    // Get "In Progress" conversation for contact
    // FIXME: There should not be more than one, if there is we are getting the oldest one.  This behaviour may need to change.
    $convContact = civicrm_api3('SmsConversationContact', 'get', array(
      'sequential' => 1,
      'contact_id' => $contactId,
      'status_id' => 2, // In Progress
      'options' => array('limit' => 1, 'sort' => "id ASC"),
    ));

    if (empty($convContact['is_error']) && !empty($convContact['count'])) {
      return $convContact['values'][0];
    }
    else {
      return FALSE;
    }
  }

  /**
   * Start a conversation with contact
   * @param $contactId
   * @param $conversationId
   *
   * @return bool
   */
  static function startConversation($contactId, $conversationId) {
    $convContact = civicrm_api3('SmsConversationContact', 'create', array(
      'conversation_id' => $conversationId,
      'contact_id' => $contactId,
      'status_id' => 'In Progress',
    ));
    if (!empty($convContact['is_error'])) {
      return FALSE;
    }

    // Ask the first question
    $conversation = CRM_SmsConversation_BAO_Conversation::get($conversationId);
    if (!$conversation['is_active']) {
      return FALSE;
    }

    // Get the question
    $question = CRM_SmsConversation_BAO_Question::get($conversation['start_question_id']);
    // Ask the question
    return CRM_SmsConversation_BAO_Question::ask($question['id'], $contactId);
  }

  /**
   * Record the conversation progress in conversation_record
   *
   * @param $convContact
   * @param $convQuestion
   * @param $sms
   * @param $validAnswer
   *
   * @return bool
   */
  static function recordConversation($convContact, $convQuestion, $sms, $validAnswer) {
    // TODO: Test this function, do we use serialize or something else?
    // Also record sms and validanswer
    $records = unserialize($convContact['conversation_record']);
    $records[] = array('q' => $convQuestion['id'], 'a' => $sms, 'v' => (boolean)$validAnswer);

    $convContact['conversation_record'] = serialize($records);
    $convContactResult = civicrm_api3('SmsConversationContact', 'create', $convContact);

    if (empty($convContactResult['is_error'])) {
      return TRUE;
    }
    return FALSE;
  }
}
