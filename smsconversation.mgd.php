<?php
/**
 * The record will be automatically inserted, updated, or deleted from the
 * database as appropriate. For more details, see "hook_civicrm_managed" at:
 * http://wiki.civicrm.org/confluence/display/CRMDOC/Hook+Reference
 */
return array(
  0 => array (
    'name' => 'Cron:SmsConversations.Schedule',
    'entity' => 'Job',
    'params' => array (
      'version' => 3,
      'name' => 'Schedule SMS Conversations',
      'description' => 'Schedule conversations for contacts where conversations are in "Scheduled" state.',
      'run_frequency' => 'Hourly',
      'api_entity' => 'SmsConversationContact',
      'api_action' => 'schedule',
      'parameters' => '',
    ),
  ),
);
