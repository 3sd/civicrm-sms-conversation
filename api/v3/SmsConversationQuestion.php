<?php

/**
 * SmsConversationQuestion.create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_sms_conversation_question_create_spec(&$spec) {
  $spec['text'] = array(
    'api.required' => 1,
    'title' => 'Question Text'
  );
  $spec['timeout'] = array(
    'title' => 'Timeout',
    'description' => 'Optional (default 0)',
  );
  $spec['invalid_text'] = array (
    'title' => 'Invalid Text',
    'description' => 'If left blank no SMS will be sent on invalid and the conversation will end.',
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['conversation_id'] = array(
    'api.required' => 1,
    'title' => 'ID of Conversation',
    'type' => CRM_Utils_Type::T_INT,
    'FKClassName' => 'CRM_SmsConversation_BAO_Conversation',
    'FKApiName' => 'SmsConversation',
  );
}

/**
 * SmsConversationQuestion.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_question_create($params) {
  return _civicrm_api3_basic_create('CRM_SmsConversation_BAO_Question', $params);
}

/**
 * SmsConversationQuestion.delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_question_delete($params) {
  return _civicrm_api3_basic_delete('CRM_SmsConversation_BAO_Question', $params);
}

/**
 * SmsConversationQuestion.get API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_question_get($params) {
  return _civicrm_api3_basic_get('CRM_SmsConversation_BAO_Question', $params);
}
