<?php

/**
 * SmsConversationContact.create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_sms_conversation_contact_create_spec(&$spec) {
  $spec['conversation_id']['api.required'] = 1;
  $spec['contact_id']['api.required'] = 1;
  $spec['status_id']['api.required'] = 0;
  $spec['current_question_id']['api.required'] = 0;
  $spec['source_contact_id']['api.required'] = 1;
  $spec['scheduled_date']['api.required'] = 0;
}

/**
 * SmsConversationContact.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_contact_create($params) {
  if (!array_key_exists('id', $params)) {
    civicrm_api3_verify_mandatory($params, NULL, array(
      'conversation_id',
      'contact_id',
      'source_contact_id',
    ));
  }

  $statusId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact', 'status_id', 'Scheduled');
  // Force status to "Scheduled" if not specified.
  if (!isset($params['status_id'])) {
    $params['status_id'] = $statusId;
  }

  $result1 = _civicrm_api3_basic_create('CRM_SmsConversation_BAO_Contact', $params);
  // Return all the values
  $params['id'] = $result1['id'];
  $result = _civicrm_api3_basic_get('CRM_SmsConversation_BAO_Contact', $params);

  if(!empty($params['process_now'])){
    civicrm_api3('Job', 'process_sms_conversations', $params);
  }
  return $result;
}

/**
 * Alias for SmsConversationContact.schedule
 * @param $params
 *
 * @return array
 */
function civicrm_api3_sms_conversation_contact_schedule($params) {
  return civicrm_api3_sms_conversation_contact_create($params);
}

function _civicrm_api3_sms_conversation_contact_schedule_spec(&$spec) {
  return _civicrm_api3_sms_conversation_contact_create_spec($spec);
}

/**
 * SmsConversationContact.delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_contact_delete($params) {
  return _civicrm_api3_basic_delete('CRM_SmsConversation_BAO_Contact', $params);
}

/**
 * SmsConversationContact.get API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_contact_get($params) {
  $result = _civicrm_api3_basic_get('CRM_SmsConversation_BAO_Contact', $params);
  // Return an error if we specified an id and it wasn't found
  if (!empty($params['id']) && $result['count'] == 0) {
    $result['is_error'] = 1;
    $result['error_message'] = 'id '.$params['id'].' not found';
  }
  return $result;
}

/**
 * SmsConversation.start API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_contact_start($params) {
  $result = CRM_SmsConversation_BAO_Contact::startConversation($params['contact_id'], !empty($params['id']) ? $params['id'] : NULL);
  if ($result) {
    return civicrm_api3_create_success($result,$params,'SmsConversationContact','start');
  }
}

/**
 * SmsConversation.start API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_sms_conversation_contact_start_spec(&$spec) {
  $spec['contact_id']['api.required'] = 1;
  $spec['id']['api.required'] = 0;
}

/**
 * Get current conversation for contact
 * @param $params
 *
 * @return array|bool
 */
function civicrm_api3_sms_conversation_contact_getcurrent($params) {
  $result = CRM_SmsConversation_BAO_Contact::getCurrentConversation($params['contact_id']);
  $values[$result['id']] = $result;
  return civicrm_api3_create_success($values,$params,'SmsConversationContact','getcurrent');
}

function _civicrm_api3_sms_conversation_contact_getcurrent_spec(&$spec) {
  $spec['contact_id']['api.required'] = 1;
}
