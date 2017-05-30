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
  $spec['text']['api.required'] = 1;
  $spec['text']['title'] = 'Question Text';
  $spec['timeout']['title'] = 'Timeout';
  $spec['timeout']['description'] = 'Optional (default 0)';
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
