<?php

/**
 * Basic SmsConversationAction API Tests
 * @group headless
 */
class SmsConversationActionTest extends CRM_SmsConversation_TestCase {
  protected $_entity = 'SmsConversationAction';
  protected $_params;

  public function setUp() {
    parent::setUp();

    $this->createTestConversation1();
    $this->createTestQuestion1();
    $this->createTestAction1();
    $this->_params = $this->_action1Params;
  }

  public function testCreateMandatoryMissing() {
    // Check that we are not able to create a new action if mandatory parameters are missing
    $params = $this->_params;
    unset($this->_params['question_id']);
    unset($this->_params['id']);
    $this->callAPIFailure($this->_entity, 'create', $this->_params);
    $this->_params = $params;
  }

  public function testCreateMandatory() {
    // Check that we can create a new action and read the parameters back
    $params = $this->_params;
    unset($this->_params['id']);
    // Create a new action with all mandatory params
    $action = $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $this->_params['id'] = $action['id'];
    $result = $this->callAPISuccess($this->_entity, 'get', $this->_params);
    $this->assertEquals($result['values'][$result['id']]['question_id'], $this->_params['question_id']);
    $this->assertEquals($result['values'][$result['id']]['answer_pattern'], $this->_params['answer_pattern']);
    $this->assertEquals($result['values'][$result['id']]['action_type'], $this->_params['action_type']);
    $this->assertEquals($result['values'][$result['id']]['action_data'], $this->_params['action_data']);
    $this->_params = $params;
  }

  public function testCreateUpdate() {
    // Test that we can update with just the id
    $action = $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $params['id'] = $action['id'];
    $params['action_data'] = 'some new data';
    $result = $this->callAPISuccess($this->_entity, 'create', $params);
    // Validate that the action was actually updated
    $this->assertEquals($result['values'][$result['id']]['id'], $params['id']);
    $this->assertEquals($result['values'][$result['id']]['action_data'], $params['action_data']);
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
}
