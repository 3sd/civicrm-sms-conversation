<?php

/**
 * Used for starting an SMS conversation with a SINGLE contact
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_SmsConversation_Form_ScheduleMultiple extends CRM_Contact_Form_Task {

  public function preProcess(){
    CRM_Utils_System::setTitle(ts('Schedule an SMS conversation'));
    parent::preProcess();

    // Check how many of the contacts have valid mobiles
    $mobileParams = ['contact_id' => ["IN" => $this->_contactIds], 'phone_type_id' => 'Mobile', 'return' => 'contact_id'];
    $countMobiles = civicrm_api3('Phone', 'getcount', $mobileParams);
    $mobileParams['option.limit']=$countMobiles;
    $mobiles = civicrm_api3('Phone', 'get', $mobileParams);
    $this->_contactWithMobileIds = array_column($mobiles['values'], 'contact_id');

    // Assign the number of contacts and the number of contacts with mobiles to
    // the template for info.
    $this->assign('countMobiles', $countMobiles);
    $this->assign('countContacts', count($this->_contactIds));
  }

  public function setDefaultValues() {
    list($defaults['scheduled_date'], $defaults['scheduled_date_time']) = CRM_Utils_Date::setDateDefaults(date('Y-m-d H:i:s'), 'activityDateTime');

    return $defaults;
  }


  public function buildQuickForm() {

    // Conversation selector
    $this->addEntityRef('conversation_id', ts('Conversation'), [
      'entity' => 'SmsConversation',
      'api' => ['label_field' => 'name'],
      'placeholder' => ts('- Select conversation -'),
      'select' => ['minimumInputLength' => 0]
    ], TRUE);

    // Choose a time for this
    $this->addDateTime('scheduled_date', ts('Send at'), TRUE);

    //TODO If there are no valid mobiles, set a status message and do not add submit button
    if($this->_contactWithMobileIds){
      $this->addButtons(array(
        array(
          'type' => 'cancel',
          'name' => ts('Cancel'),
          'isDefault' => TRUE,
        ),
        array(
          'type' => 'submit',
          'name' => ts('Send'),
          'isDefault' => TRUE,
        ),
      ));
    }else{
      $session = CRM_Core_Session::singleton();
      CRM_Core_Session::setStatus('No selected contacts had valid mobile phones.');
    }

  }


  public function postProcess() {

    $values = $this->exportValues();
    $session = CRM_Core_Session::singleton();

    foreach($this->_contactWithMobileIds as $contactId){
      $params['contact_id'] = $contactId;
      $params['conversation_id'] = $values['conversation_id'];
      $params['scheduled_date'] = CRM_Utils_Date::processDate($values['scheduled_date'], $values['scheduled_date_time']);
      $params['source_contact_id'] = $session->get('userID');
      $result = civicrm_api3('SmsConversationContact', 'create', $params);
    }

    CRM_Core_Session::setStatus(ts('SMS conversation scheduled with %1 contacts', [1 => count($this->_contactWithMobileIds)]));
    parent::postProcess();
  }
}
