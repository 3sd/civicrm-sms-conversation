<?php

/**
 * Basic SmsConversation API Tests
 * @group headless
 */
class SmsConversationTest extends CRM_SmsConversation_TestCase {
  protected $_entity = 'SmsConversation';
  protected $_params;

  public function setUp() {
    parent::setUp();

    $this->_params = array(
      'name' => 'Test SMS Conversation',
    );
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function testCreate() {
    $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $result = $this->callAPISuccess($this->_entity, 'get', $this->_params);
    $this->assertEquals($result['values'][$result['id']]['name'], $this->_params['name']);
    $this->assertEquals($result['values'][$result['id']]['is_active'], 1);
  }

  public function testCreateInactive() {
    $this->_params['is_active'] = 0;
    $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $result = $this->callAPISuccess($this->_entity, 'get', $this->_params);
    $this->assertEquals($result['values'][$result['id']]['name'], $this->_params['name']);
    $this->assertEquals($result['values'][$result['id']]['is_active'], 0);
    unset($this->_params['is_active']);
  }

  public function testCreateActive() {
    $params = $this->_params;
    $params['is_active'] = 1;
    $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $result = $this->callAPISuccess($this->_entity, 'get', $this->_params);
    $this->assertEquals($result['values'][$result['id']]['name'], $this->_params['name']);
    $this->assertEquals($result['values'][$result['id']]['is_active'], 1);
  }

  public function testCreateWithQuestionId() {
    $this->_params['start_question_id'] = 5;
    $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $result = $this->callAPISuccess($this->_entity, 'get', $this->_params);
    $this->assertEquals($result['values'][$result['id']]['name'], $this->_params['name']);
    $this->assertEquals($result['values'][$result['id']]['start_question_id'], 5);
    unset($this->_params['start_question_id']);
  }

  public function testGet() {
    $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $result = $this->callAPISuccess($this->_entity, 'get', $this->_params);
    $this->callAPIFailure($this->_entity, 'get', array('id' => 99));
    // We created 3 conversations above
    $this->assertEquals($result['count'], 2);
  }

  public function testDelete() {
    $conversation = $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $result = $this->callAPISuccess($this->_entity, 'delete', array('id' => $conversation['id']));
    $this->assertEquals($result['count'], 1);
    $this->callAPIFailure($this->_entity, 'get', array('id' => $conversation['id']));
  }
}
