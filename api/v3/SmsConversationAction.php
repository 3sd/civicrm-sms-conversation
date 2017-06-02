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
  $spec['question_id'] = array (
    'api.required' => 1,
    'title' => 'ID of question',
    'description' => 'FK to SmsConversationQuestion.id',
    'type' => CRM_Utils_Type::T_INT,
    // FIXME: This should lookup FK to sms_conversation_question.id
  );
  $spec['answer_pattern'] = array (
    'api.required' => 1,
    'title' => 'Answer Pattern',
    'description' => 'Regex or simple text',
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['action_type'] = array (
    'api.required' => 1,
    'title' => 'Action Type',
    'description' => 'sms_conversation_action_type',
    'type' => CRM_Utils_Type::T_INT,
    // FIXME: This should lookup optiongroup: sms_conversation_action_type
  );
  $spec['action_data'] = array (
    'api.required' => 1,
    'title' => 'Action Data',
    'description' => 'Depends on the action_type',
    'type' => CRM_Utils_Type::T_STRING,
  );
}

/**
 * SmsConversationAction.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_action_create($params) {
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
  return _civicrm_api3_basic_get('CRM_SmsConversation_BAO_Action', $params);
}
