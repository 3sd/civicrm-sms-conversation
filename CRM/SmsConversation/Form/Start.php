<?php

/**
 * Used for starting an SMS conversation with a SINGLE contact
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_SmsConversation_Form_Start extends CRM_Core_Form {

  public function preProcess(){
    $this->contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this);
    $contact = civicrm_api3('Contact', 'getsingle', ['id' => $this->contactId]);
    CRM_Utils_System::setTitle(ts('Start an SMS conversation with %1', [1 => $contact['display_name']]));

  }

  public function setDefaultValues() {
    list($defaults['send_at'], $defaults['send_at_time']) = CRM_Utils_Date::setDateDefaults(date('Y-m-d H:i:s'), 'activityDateTime');

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
    $this->addDateTime('send_at', ts('Send at'), TRUE);

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

    // export form elements
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    $params['contact_id'] = $this->contactId;
    $params['conversation_id'] = $values['conversation_id'];
    $session = CRM_Core_Session::singleton();
    $params['source_contact_id'] = $session->get('userID');
    // Create new conversation for contact
    $result = civicrm_api3('SmsConversationContact', 'create', $params);

    try {
      // Start the conversation
      $status = civicrm_api3('SmsConversationContact', 'start', $params);
    }
    catch (Exception $e) {
      $status = $e->getMessage();
    }
    CRM_Core_Session::setStatus($status);
    parent::postProcess();
  }

}
