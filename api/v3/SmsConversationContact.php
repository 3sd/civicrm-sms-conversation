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
  $spec['conversation_id'] = array (
    'api.required' => 1,
    'title' => 'ID of conversation',
    'description' => 'FK to SmsConversation.id',
    'type' => CRM_Utils_Type::T_INT,
    // FIXME: This should lookup FK to sms_conversation.id
  );
  $spec['contact_id'] = array (
    'api.required' => 1,
    'title' => 'ID of contact',
    'description' => 'FK to civicrm_contact.id',
    'type' => CRM_Utils_Type::T_INT,
    // FIXME: This should lookup FK to civicrm_contact.id
  );
  $spec['status_id'] = array (
    'title' => 'Conversation Status',
    'description' => 'From option group sms_conversation_status_type',
    'type' => CRM_Utils_Type::T_INT,
    // FIXME: This should lookup option group to sms_conversation_status_type
  );
  $spec['current_question_id'] = array (
    'title' => 'ID of current question',
    'description' => 'FK to sms_conversation_question.id',
    'type' => CRM_Utils_Type::T_INT,
    // FIXME: This should lookup FK to sms_conversation_question.id
  );
  $spec['source_contact_id'] = array (
    'api.required' => 1,
    'title' => 'ID of contact that started conversation',
    'description' => 'FK to civicrm_contact.id',
    'type' => CRM_Utils_Type::T_INT,
    // FIXME: This should lookup FK to civicrm_contact.id
  );
}

/**
 * SmsConversationContact.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_sms_conversation_contact_create($params) {
  $inProgressId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact', 'status_id', 'In Progress');
  // Force status to "In Progress" if not specified.
  if (!isset($params['status_id'])) {
    $params['status_id'] = $inProgressId;
  }
  return _civicrm_api3_basic_create('CRM_SmsConversation_BAO_Contact', $params);
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
  return _civicrm_api3_basic_get('CRM_SmsConversation_BAO_Contact', $params);
}
