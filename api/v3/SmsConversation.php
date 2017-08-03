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
  $spec['is_active']['api.required'] = 0;
  $spec['start_question_id']['api.required'] = 0;
  $spec['start_question_id']['FKClassName'] = 'CRM_SmsConversation_BAO_Question';
  $spec['start_question_id']['FKApiName'] = 'SmsConversationQuestion';
  $spec['name']['api.required'] = 1;
}

/**
 * SmsConversation.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_create($params) {
  if (!array_key_exists('id', $params)) {
    civicrm_api3_verify_mandatory($params, NULL, array(
      'name',
    ));
  }
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
  $result = _civicrm_api3_basic_get('CRM_SmsConversation_BAO_Conversation', $params);
  // Return an error if we specified an id and it wasn't found
  if (!empty($params['id']) && $result['count'] == 0) {
    $result['is_error'] = 1;
    $result['error_message'] = 'id '.$params['id'].' not found';
  }
  return $result;
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
  $spec['id']['api.required'] = 0;
}

function civicrm_api3_sms_conversation_example1($params) {
  return CRM_SmsConversation_Examples::addExampleConversation1();
}

function civicrm_api3_sms_conversation_example2($params) {
  return CRM_SmsConversation_Examples::addExampleConversation2();
}
