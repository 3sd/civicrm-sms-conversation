<?php

require_once 'smsconversation.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function smsconversation_civicrm_config(&$config) {
  _smsconversation_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function smsconversation_civicrm_xmlMenu(&$files) {
  _smsconversation_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function smsconversation_civicrm_install() {
  _smsconversation_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function smsconversation_civicrm_postInstall() {
  _smsconversation_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function smsconversation_civicrm_uninstall() {
  _smsconversation_civix_civicrm_uninstall();

  // Delete action_type option group
  $result = civicrm_api3('OptionGroup', 'get', array(
    'name' => "sms_conversation_action_type",
  ));
  if (!empty($result['id'])) {
    $result = civicrm_api3('OptionGroup', 'delete', array(
      'id' => $result['id'],
    ));
  }

  // Delete status_type option group
  $result = civicrm_api3('OptionGroup', 'get', array(
    'name' => "sms_conversation_status_type",
  ));
  if (!empty($result['id'])) {
    $result = civicrm_api3('OptionGroup', 'delete', array(
      'id' => $result['id'],
    ));
  }
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function smsconversation_civicrm_enable() {
  _smsconversation_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function smsconversation_civicrm_disable() {
  _smsconversation_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function smsconversation_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _smsconversation_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function smsconversation_civicrm_managed(&$entities) {
  _smsconversation_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function smsconversation_civicrm_caseTypes(&$caseTypes) {
  _smsconversation_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function smsconversation_civicrm_angularModules(&$angularModules) {
  _smsconversation_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function smsconversation_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _smsconversation_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function smsconversation_civicrm_post( $op, $objectName, $objectId, &$objectRef ){
  //try and return as quickly as possible
  if($objectName=='Activity' && $objectRef->activity_type_id == CRM_Core_OptionGroup::getValue('activity_type', 'Inbound SMS', 'name')){
    // process inbound SMS
    $activity = civicrm_api('Activity', 'getsingle', array('version'=>'3','id' => $objectId));
    $p = new CRM_SmsConversation_Processor($activity);
    if ($p) {
      $p->inbound();
    }
  }
}

/**
 * Implements hook_civicrm_entityTypes.
 *
 * @param array $entityTypes
 *   Registered entity types.
 */
function smsconversation_civicrm_entityTypes(&$entityTypes) {
  $entityTypes['CRM_SmsConversation_DAO_Action'] = array (
    'name' => 'SmsConversationAction',
    'class' => 'CRM_SmsConversation_DAO_Action',
    'table' => 'civicrm_sms_conversation_action',
  );
  $entityTypes['CRM_SmsConversation_DAO_Contact'] = array (
    'name' => 'SmsConversationContact',
    'class' => 'CRM_SmsConversation_DAO_Contact',
    'table' => 'civicrm_sms_conversation_contact',
  );
  $entityTypes['CRM_SmsConversation_DAO_Conversation'] = array (
    'name' => 'SmsConversationConversation',
    'class' => 'CRM_SmsConversation_DAO_Conversation',
    'table' => 'civicrm_sms_conversation',
  );
  $entityTypes['CRM_SmsConversation_DAO_Question'] = array (
    'name' => 'SmsConversationQuestion',
    'class' => 'CRM_SmsConversation_DAO_Question',
    'table' => 'civicrm_sms_conversation_question',
  );
}

function smsconversation_civicrm_summaryActions(&$actions, $contactId){

  // If the contact has a mobile phone, start a conversation with them
  $count = civicrm_api3('Phone', 'getcount', ['contact_id' => $contactId, 'phone_type_id' => 'Mobile']);
  if($count){
      $actions['smsconversation'] = [
      'title' => 'Start SMS conversation',
      'weight' => 999,
      'ref' => 'sms-conversation',
      'key' => 'sms-conversation',
      'href' => '/civicrm/sms/conversation/start'
    ];
  }
}
