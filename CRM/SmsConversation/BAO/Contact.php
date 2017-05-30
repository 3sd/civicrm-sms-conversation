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

}
