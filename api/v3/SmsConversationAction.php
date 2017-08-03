<?php

/**
 * SmsConversationAction.create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_sms_conversation_action_create_spec(&$spec) {
  $spec['id']['api.required'] = 0;
  $spec['question_id']['api.required'] = 1;
  $spec['answer_pattern']['api.required'] = 1;
  $spec['action_type']['api.required'] = 1;
  $spec['action_data']['api.required'] = 1;
}

/**
 * SmsConversationAction.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_action_create($params) {
  if (!array_key_exists('id', $params)) {
    civicrm_api3_verify_mandatory($params, NULL, array(
      'question_id',
      'answer_pattern',
      'action_type',
      'action_data'
    ));
  }
  return _civicrm_api3_basic_create('CRM_SmsConversation_BAO_Action', $params);
}

/**
 * SmsConversationAction.delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_action_delete($params) {
  return _civicrm_api3_basic_delete('CRM_SmsConversation_BAO_Action', $params);
}

/**
 * SmsConversationAction.get API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_action_get($params) {
  $result = _civicrm_api3_basic_get('CRM_SmsConversation_BAO_Action', $params);
  // Return an error if we specified an id and it wasn't found
  if (!empty($params['id']) && $result['count'] == 0) {
    $result['is_error'] = 1;
    $result['error_message'] = 'id '.$params['id'].' not found';
  }
  return $result;
}
