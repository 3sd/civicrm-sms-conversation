<?php

require_once 'smsconversation.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function smsconversation_civicrm_config(&$config) {
  if (isset(Civi::$statics[__FUNCTION__])) {
    return;
  }
  Civi::$statics[__FUNCTION__] = 1;
  Civi::dispatcher()->addListener('hook_civicrm_post', 'smsconversation_process_inbound',1000);

  _smsconversation_civix_civicrm_config($config);
}

function smsconversation_process_inbound($event){
  if($event->entity=='Activity' && $event->object->activity_type_id == CRM_Core_PseudoConstant::getKey('CRM_Activity_BAO_Activity', 'activity_type_id', 'Inbound SMS')) {
    $activity = civicrm_api('Activity', 'getsingle', array('version'=>'3','id' => $event->id));
    $p = new CRM_SmsConversation_Processor($activity);
    if ($p) {
      $p->inbound();
    }
  }
    // var_dump($event->object->activity_type_id);
    // var_dump($event->id);
    // $r = new ReflectionObject($event);
    // var_dump($r->getProperties());
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
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function smsconversation_civicrm_uninstall() {

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
      'href' => CRM_Utils_System::url('civicrm/sms/conversation/schedule', "cid=$contactId"),
    ];
  }
}

function smsconversation_civicrm_searchTasks( $objectName, &$tasks ){
  if($objectName == 'contact'){
    $tasks[] = [
      'title' => 'SMS - schedule a conversation',
      'class' => 'CRM_SmsConversation_Form_ScheduleMultiple'
    ];
  }
}

/*function smsconversation_civicrm_tabset($tabsetName, &$tabs, $context) {
  // FIXME: For CiviCRM 4.7 we can use this hook instead.
}*/

/**
 * Replace the existing activities tab
 * @param $tabs
 * @param $contactID
 */
function smsconversation_civicrm_tabset ( $tabsetName, &$tabs, $context) {
  // ADD the sms conversation tab as a separate tab
  if ($tabsetName == 'civicrm/contact/view' && !empty($context['contact_id'])) {
    $tabs[] = [
      'title'  => 'SMS Conversations',
      'id'     => 'smsconversation',
      'class' => 'livePage',
      'url'    => CRM_Utils_System::url('civicrm/contact/view/smsconversation', "reset=1&cid={$context['contact_id']}"),
      'weight' => 50,
      'count'  => CRM_SmsConversation_BAO_Contact::getConversationCount($context['contact_id']),
    ];
  }
}

function smsconversation_civicrm_navigationMenu(&$menus){

  // Find the mailing menu
  foreach($menus as &$menu){
    if($menu['attributes']['name'] == 'Mailings'){
      $nextId = max(array_keys($menu['child']));
      $menu['child'][$nextId]=[
        'attributes' => array(
          'label'      => 'SMS Conversations',
          'name'       => 'SMS Conversations',
          'url'        => 'civicrm/sms/conversations',
          'permission' => 'access CiviMail',
          'navID'      => $nextId,
          'operator'   => FALSE,
          'separator'  => TRUE,
          'parentID'   => $menu['attributes']['navID'],
          'active'     => 1
        ),
      ];
    }
  }
}
