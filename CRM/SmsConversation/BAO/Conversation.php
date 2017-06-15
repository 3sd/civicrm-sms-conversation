<?php

class CRM_SmsConversation_BAO_Conversation extends CRM_SmsConversation_DAO_Conversation {

  /**
   * Create a new SmsConversation based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_SmsConversation_DAO_Conversation|NULL
   */
  public static function create($params) {
    $className = 'CRM_SmsConversation_DAO_Conversation';
    $entityName = 'SmsConversation';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  static function start($contactId, $conversationId, $sourceContactId) {
    // Don't allow another conversation to start
    if (CRM_SmsConversation_BAO_Contact::getCurrentConversation($contactId)) {
      throw Exception('SmsConversation Conversation already in progress');
    }

    // Create a new conversation
    CRM_SmsConversation_BAO_Contact::startConversation($contactId, $conversationId, $sourceContactId);
  }

  /**
   * End the conversation
   * @param $contactId
   * @param $conversationId
   * @param string $status
   *
   * @return bool
   */
  static function end($contactId, $conversationId, $status = 'Completed') {
    return CRM_SmsConversation_BAO_Conversation::updateStatus($contactId, $conversationId, $status);
  }

  /**
   * Update the conversation status
   * @param $contactId
   * @param $conversationId
   * @param $status (crm_smsconversation_status_type)
   *
   * @return bool
   */
  static function updateStatus($contactId, $conversationId, $status) {
    // Mark the conversation as $status
    $convContact = civicrm_api3('SmsConversationContact', 'create', array(
      'conversation_id' => $conversationId,
      'contact_id' => $contactId,
      'status_id' => $status,
    ));
    if (empty($convContact['is_error'])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Get the conversation
   * @param $conversationId
   *
   * @return array|bool
   */
  static function getConversation($conversationId) {
    $conversation = civicrm_api3('SmsConversation', 'get', array(
      'id' => $conversationId,
    ));
    if (empty($conversation['is_error'])) {
      return $conversation;
    }
    return FALSE;
  }
}
