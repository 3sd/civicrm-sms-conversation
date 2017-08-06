<?php

/**
 * Basic SmsConversationQuestion API tests
 * @group headless
 */
class api_v3_SmsConversationQuestionTest extends CRM_SmsConversation_TestCase {
  protected $_entity = 'SmsConversationQuestion';
  protected $_params;

  public function setUp() {
    parent::setUp();

    $this->createTestConversation1();
    $this->createTestQuestion1();
    $this->_params = $this->_question1Params;
  }

  public function testCreateMandatoryMissing() {
    $params = $this->_params;
    unset($params['conversation_id']);
    unset($params['id']);
    $this->callAPIFailure($this->_entity, 'create', $params);
    $this->_params = $params;
  }

  public function testCreateMandatory() {
    $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $result = $this->callAPISuccess($this->_entity, 'get', $this->_params);
    $this->assertEquals($result['values'][$result['id']]['text'], $this->_params['text']);
    $this->assertEquals($result['values'][$result['id']]['conversation_id'], $this->_params['conversation_id']);
  }

  public function testCreateOptional() {
    // Check that we can create a question with optional parameters
    $this->_params['text_invalid'] = 'invalid answer';
    $this->_params['timeout'] = 0;
    $question = $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $result = $this->callAPISuccess($this->_entity, 'get', $this->_params);
    $this->assertEquals($result['values'][$result['id']]['text_invalid'], $this->_params['text_invalid']);
    $this->assertEquals($result['values'][$result['id']]['timeout'], $this->_params['timeout']);
    // Check that we can update an existing question
    $this->_params['timeout'] = 60;
    $this->_params['text'] = 'Changed the question text';
    $this->_params['id'] = $question['id'];
    $question = $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $this->assertEquals($question['values'][$result['id']]['timeout'], $this->_params['timeout']);
    $this->assertEquals($question['values'][$result['id']]['text'], $this->_params['text']);
    $this->assertEquals($question['values'][$result['id']]['id'], $this->_params['id']);
  }

  public function testCreateUpdate() {
    // Test that we can update with just the id
    $question = $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $params['id'] = $question['id'];
    $params['text_invalid'] = 'a change';
    $this->callAPISuccess($this->_entity, 'create', $params); // FIXME: This passes but fails in API explorer! We want it to pass...
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
