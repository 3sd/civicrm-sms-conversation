<?php

class CRM_SmsConversation_BAO_Action extends CRM_SmsConversation_DAO_Action {

  /**
   * Create a new SmsConversationAction based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Conversations_DAO_SmsConversationAction|NULL
   *
  public static function create($params) {
    $className = 'CRM_Conversations_DAO_SmsConversationAction';
    $entityName = 'SmsConversationAction';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

}
