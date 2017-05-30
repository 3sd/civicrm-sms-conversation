<?php

/**
 * SmsConversation.create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_sms_conversation_create_spec(&$spec) {
  $spec['is_active']['title'] = 'Enabled?';
  $spec['is_active']['type'] = CRM_Utils_Type::T_BOOLEAN;
  $spec['start_question_id']['api_required'] = 1;
  $spec['start_question_id']['title'] = 'ID of first question';
  $spec['start_question_id']['type'] = CRM_Utils_Type::T_INT;
}

/**
 * SmsConversation.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_create($params) {
  return _civicrm_api3_basic_create('CRM_SmsConversation_BAO_Conversation', $params);
}

/**
 * SmsConversation.delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_delete($params) {
  return _civicrm_api3_basic_delete('CRM_SmsConversation_BAO_Conversation', $params);
}

/**
 * SmsConversation.get API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_get($params) {
  return _civicrm_api3_basic_get('CRM_SmsConversation_BAO_Conversation', $params);
}
