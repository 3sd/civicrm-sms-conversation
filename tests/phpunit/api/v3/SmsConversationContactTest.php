<?php

/**
 * Basic SmsConversationAction API Tests
 * @group headless
 */
class SmsConversationContactTest extends CRM_SmsConversation_TestCase {
  protected $_entity = 'SmsConversationContact';
  protected $_params;

  public function setUp() {
    parent::setUp();

    $this->createTestConversation1();
    $this->createTestQuestion1();
    $this->createTestAction1();
    $this->createTestContact1();
    $this->_params = $this->_contact1Params;
  }

  public function testCreateMandatoryMissing() {
    // Check that we are not able to create a new action if mandatory parameters are missing
    $params = $this->_params;
    unset($params['contact_id']);
    unset($params['id']);
    $this->callAPIFailure($this->_entity, 'create', $params);
  }

  public function testCreateMandatory() {
    // Check that we can create a new action and read the parameters back
    $params = $this->_params;
    unset($params['id']);
    // Create a new action with all mandatory params
    $convContact = $this->callAPISuccess($this->_entity, 'create', $params);
    $params['id'] = $convContact['id'];
    $result = $this->callAPISuccess($this->_entity, 'get', $params);
    $this->assertEquals($result['values'][$result['id']]['contact_id'], $params['contact_id']);
    $this->assertEquals($result['values'][$result['id']]['conversation_id'], $params['conversation_id']);
    $this->assertEquals($result['values'][$result['id']]['source_contact_id'], $params['source_contact_id']);
  }

  public function testCreateUpdate() {
    // Test that we can update with just the id
    $convContact = $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $params['id'] = $convContact['id'];
    $params['status_id'] = 3; // Failed
    $result = $this->callAPISuccess($this->_entity, 'create', $params);
    // Validate that the action was actually updated
    $this->assertEquals($result['values'][$result['id']]['id'], $params['id']);
    $this->assertEquals($result['values'][$result['id']]['status_id'], $params['status_id']);
  }


  public function testGet() {
    $this->apiTestGet();
  }

  public function testDeleteMandatoryMissing() {
    $this->apiTestDeleteMandatoryMissing();
  }

  public function testDelete() {
    $this->apiTestDelete();
  }

  // TODO: This API also needs to test the following additional functions
  // start, schedule, getcurrent
  public function testStart() {

  }

  public function testSchedule() {

  }

  /* sms_conversation_status_type
 * Scheduled = 1
 * In Progress = 2
 * Failed = 3
 * Cancelled = 4
 * Completed = 5
 */

  public function createConversations() {
    $params = $this->_params;
    unset($params['id']);
    // Create a new action with all mandatory params
    $params['status_id'] = 1; //
    $this->convContactScheduled = $this->callAPISuccess($this->_entity, 'create', $params);
    $params['status_id'] = 2;
    $this->convContactInProgress = $this->callAPISuccess($this->_entity, 'create', $params);
    $params['status_id'] = 3;
    $this->convContactFailed = $this->callAPISuccess($this->_entity, 'create', $params);
    $params['status_id'] = 4;
    $this->convContactCancelled = $this->callAPISuccess($this->_entity, 'create', $params);
    $params['status_id'] = 5;
    $this->convContactCompleted = $this->callAPISuccess($this->_entity, 'create', $params);
  }

  public function testGetCurrentMandatoryMissing() {
    // contact_id is mandatory
    $this->callAPIFailure($this->_entity, 'create', array());
  }

  public function testGetCurrent() {
    $this->createConversations();
    // Make sure we return the conversation that is "In Progress"
    $params['contact_id'] = $this->_testContactParams['id'];
    $result = $this->callAPISuccess($this->_entity, 'getcurrent', $params);
    $this->assertEquals($result['values'][$result['id']]['id'], $this->convContactInProgress['id']);
  }
}
