<?php

function civicrm_api3_job_process_sms_conversations($params) {
  if (isset($params['contact_id'])) {
    $contactId = $params['contact_id'];
  }
  $result = CRM_SmsConversation_BAO_Contact::scheduleConversations($contactId);
  return civicrm_api3_create_success($result, $params,'SmsConversation','schedule');
}

function _civicrm_api3_job_process_sms_conversations_spec(&$spec) {
  $spec['contact_id'] = array(
    'title' => 'Contact ID',
    'description' => 'If specified, conversations will be scheduled for that contact only',
    'api.aliases' => array('contact_id'),
  );
}
