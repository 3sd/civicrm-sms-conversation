<?php

/**
 * Basic SmsConversation API Tests
 * @group headless
 */
class api_v3_SmsConversationTest extends CRM_SmsConversation_TestCase {
  protected $_entity = 'SmsConversation';
  protected $_params;

  public function setUp() {
    parent::setUp();

    $this->createTestConversation1();

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
    $this->apiTestGet();
  }

  public function testDeleteMandatoryMissing() {
    $this->apiTestDeleteMandatoryMissing();
  }

  public function testDelete() {
    $this->apiTestDelete();
  }
}
