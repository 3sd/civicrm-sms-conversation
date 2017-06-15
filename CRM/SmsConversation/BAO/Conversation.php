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
      return $conversation['values'][$conversationId];
    }
    return FALSE;
  }
}
