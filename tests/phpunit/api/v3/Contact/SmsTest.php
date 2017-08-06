<?php
<?php

/**
 * Basic Contact.sms API Tests
 * @group headless
 */
class api_v3_ContactSmsTest extends CRM_SmsConversation_TestCase {
  protected $_entity = 'Contact';
  protected $_params;

  // FIXME: As the Contact.sms function was copied from chainedsms there is quite a lot of code in there.
  // These tests only do very basic testing, for the parameters that are required by org.civicrm.smsconversation

  public function setUp() {
    parent::setUp();

    $this->createTestContact();
    $this->createTestPhoneFixed();
    $this->createTestPhoneMobile();
    $this->_params = $this->_testContactParams;
  }

  public function testSmsMandatoryMissing() {
    // Check that we are not able to send an SMS if mandatory parameters are missing
    $params = $this->_params;
    unset($params['contact_id']);
    unset($params['id']);
    $this->callAPIFailure($this->_entity, 'sms', $params);
  }

  public function testSmsMandatory() {
    // Check that we can send an SMS (we just check an activity is created)
    $params = $this->_params;
    unset($params['id']);
    // Create a new action with all mandatory params
    $result = $this->callAPISuccess($this->_entity, 'sms', $params);
    $activityTypeId = CRM_Core_PseudoConstant::getKey('CRM_Activity_BAO_Activity', 'activity_type_id', 'Outbound SMS');
    $activityStatusId = CRM_Core_PseudoConstant::getKey('CRM_Activity_BAO_Activity', 'activity_status_id', 'Completed');
    print ($activityTypeId);
    print ($activityStatusId);
    $this->assertEquals($result['values'][$result['id']]['activity_type_id'], $activityTypeId);
    $this->assertEquals($result['values'][$result['id']]['activity_status_id'], $activityStatusId);
  }
}
