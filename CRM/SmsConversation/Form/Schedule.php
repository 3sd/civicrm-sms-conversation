<?php

/**
 * Used for starting an SMS conversation with a SINGLE contact
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_SmsConversation_Form_Schedule extends CRM_Core_Form {

  public function preProcess() {
    $this->contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this);
    $contact = civicrm_api3('Contact', 'getsingle', ['id' => $this->contactId]);
    CRM_Utils_System::setTitle(ts('Start an SMS conversation with %1', [1 => $contact['display_name']]));
    parent::preProcess();
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

    $this->addButtons(array(
      array(
        'type' => 'cancel',
        'name' => ts('Cancel'),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'submit',
        'name' => ts('Schedule'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    list($defaults['scheduled_date'], $defaults['scheduled_date_time']) = CRM_Utils_Date::setDateDefaults(date('Y-m-d H:i:s'), 'activityDateTime');

    return $defaults;
  }

  public function postProcess() {

    $values = $this->exportValues();
    $session = CRM_Core_Session::singleton();

    $params['contact_id'] = $this->contactId;
    $params['conversation_id'] = $values['conversation_id'];
    $params['scheduled_date'] = CRM_Utils_Date::processDate($values['scheduled_date'], $values['scheduled_date_time']);
    $params['source_contact_id'] = $session->get('userID');
    $params['process_now'] = TRUE; // Start the conversation immediately
    // Create new conversation for contact
    try {
      $status = civicrm_api3('SmsConversationContact', 'schedule', $params);
    }
    catch (Exception $e) {
      $status = $e->getMessage();
    }
    CRM_Core_Session::setStatus($status);
    parent::postProcess();
  }

}
