<?php

class CRM_SmsConversation {

  static function start($contactId, $conversationId) {
    // Don't allow another conversation to start
    if (CRM_SmsConversation_Contact::getCurrentConversation($contactId)) {
      throw Exception('SmsConversation Conversation already in progress');
    }

    // Create a new conversation
    CRM_SmsConversation_Contact::startConversation($contactId, $conversationId);
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
    return CRM_SmsConversation::updateStatus($contactId, $conversationId, $status);
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
  static function get($conversationId) {
    $conversation = civicrm_api3('SmsConversation', 'get', array(
      'id' => $conversationId,
    ));
    if (empty($conversation['is_error'])) {
      return $conversation;
    }
    return FALSE;
  }
}