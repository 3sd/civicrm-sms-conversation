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
  $spec['is_active'] = array(
    'title' => 'Enabled?',
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['start_question_id'] = array(
    'api.required' => 1,
    'title' => 'ID of first question',
    'type' => CRM_Utils_Type::T_INT,
  );
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

/**
 * SmsConversation.get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_sms_conversation_get_spec(&$spec) {
  $spec['id'] = array(
    'title' => 'Conversation ID',
    'type' => CRM_Utils_Type::T_INT,
  );
}

/**
 * SmsConversation.start API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_start($params) {
  return CRM_SmsConversation_BAO_Conversation::start($params['contact_id'], $params['conversation_id']);
}

/**
 * SmsConversation.start API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_sms_conversation_start_spec(&$spec) {
  $spec['contact_id'] = array(
    'api.required' => 1,
    'title' => 'Contact ID',
    'api.aliases' => array('contact_id'),
  );
  $spec['conversation_id'] = array(
    'api.required' => 1,
    'title' => 'Conversation ID',
    'api.aliases' => array('civicrm_sms_conversation.id'),
  );
}

function civicrm_api3_sms_conversation_example1($params) {
  return CRM_SmsConversation_Examples::addExampleConversation1();
}

function civicrm_api3_sms_conversation_example2($params) {
  return CRM_SmsConversation_Examples::addExampleConversation2();
}