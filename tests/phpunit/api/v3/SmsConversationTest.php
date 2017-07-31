<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * FIXME - Add test description.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test
 * class. Simply create corresponding functions (e.g. "hook_civicrm_post(...)"
 * or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or
 * test****() functions will rollback automatically -- as long as you don't
 * manipulate schema or truncate tables. If this test needs to manipulate
 * schema or truncate tables, then either: a. Do all that using setupHeadless()
 * and Civi\Test. b. Disable TransactionalInterface, and handle all
 * setup/teardown yourself.
 *
 * @group headless
 */
class SmsConversationTest extends CRM_SmsConversation_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  protected $_entity = 'SmsConversation';
  protected $_params;

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->install(array('org.civicoop.civirules','civicrm.sms.conversations'))
      ->apply();
  }

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
