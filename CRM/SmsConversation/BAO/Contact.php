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
    $inProgressId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact', 'status_id', 'In Progress');
    $convContact = civicrm_api3('SmsConversationContact', 'get', array(
      'sequential' => 1,
      'contact_id' => $contactId,
      'status_id' => $inProgressId,
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
   * Get the next scheduled conversation for a contact
   * @param $contactId
   *
   * @return array|bool
   */
  static function getNextScheduledConversation($contactId) {
    // Get next "Scheduled" conversation for contact
    // We select the earliest by scheduled date, then earliest by Id and select the first one
    $scheduledId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact', 'status_id', 'Scheduled');
    $convContact = civicrm_api3('SmsConversationContact', 'get', array(
      'sequential' => 1,
      'contact_id' => $contactId,
      'status_id' => $scheduledId,
      'options' => array('limit' => 1, 'sort' => "scheduled_date ASC,id ASC"),
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
  static function startConversation($contactId, $id = NULL) {
    // Don't allow another conversation to start
    if (CRM_SmsConversation_BAO_Contact::getCurrentConversation($contactId)) {
      throw new CRM_Core_Exception('This contact already has a conversation in progress');
    }

    $convContact = CRM_SmsConversation_BAO_Contact::getNextScheduledConversation($contactId);

    CRM_SmsConversation_BAO_Contact::updateStatus($convContact['id'], 'In Progress');

    if (empty($convContact)) {
      throw new CRM_Core_Exception('No scheduled conversations found');
    }

    if (!empty($convContact['is_error'])) {
      return FALSE;
    }

    // Ask the first question
    $conversation = CRM_SmsConversation_BAO_Conversation::getConversation($convContact['conversation_id']);
    if (!$conversation['is_active']) {
      return FALSE;
    }

    // Get the question
    $question = CRM_SmsConversation_BAO_Question::getQuestion($conversation['start_question_id']);
    // Ask the question
    return CRM_SmsConversation_BAO_Question::ask($question['id'], $contactId, $convContact['source_contact_id']);
  }

  /**
   * End the conversation
   * @param $id
   * @param string $status
   *
   * @return bool
   */
  static function endConversation($id, $status = 'Completed') {
    return CRM_SmsConversation_BAO_Contact::updateStatus($id, $status);
  }

  /**
   * Update the conversation status
   * @param $id
   * @param $status (crm_smsconversation_status_type)
   *
   * @return bool
   */
  static function updateStatus($id, $status) {
    // Mark the conversation as $status
    $statusId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact', 'status_id', $status);
    if (empty($statusId)) {
      return FALSE;
    }
    $convContact = civicrm_api3('SmsConversationContact', 'create', array(
      'id' => $id,
      'status_id' => $statusId,
    ));
    if (empty($convContact['is_error'])) {
      return TRUE;
    }
    return FALSE;
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
