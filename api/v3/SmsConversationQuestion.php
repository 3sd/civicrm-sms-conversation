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
  $spec['timeout']['api.required'] = 0;
  $spec['invalid_text']['api.required'] = 0;
  $spec['conversation_id']['api.required'] = 1;
}

/**
 * SmsConversationQuestion.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_question_create($params) {
  if (!array_key_exists('id', $params)) {
    civicrm_api3_verify_mandatory($params, NULL, array(
      'text',
      'conversation_id',
    ));
  }
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
  $result = _civicrm_api3_basic_get('CRM_SmsConversation_BAO_Question', $params);
  // Return an error if we specified an id and it wasn't found
  if (!empty($params['id']) && $result['count'] == 0) {
    $result['is_error'] = 1;
    $result['error_message'] = 'id '.$params['id'].' not found';
  }
  return $result;
}
