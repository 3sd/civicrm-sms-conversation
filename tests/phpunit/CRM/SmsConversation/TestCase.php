<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

abstract class CRM_SmsConversation_TestCase extends \CivixPhar\PHPUnit\Framework\TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  //abstract class CRM_SmsConversation_TestCase extends CiviUnitTestCase {
  use CiviUnitTestApiFunctions;

  protected $_apiversion = 3;

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->install(array('org.civicoop.civirules','io.3sd.dummysms','civicrm-sms-conversation'))
      ->apply();
  }

  public function setUp() {
    // Install SMS provider
    $smsProvider = civicrm_api3('SmsProvider', 'create', array(
      'sequential' => 1,
      'name' => "DummySMS",
      'title' => "dummy",
      'username' => "dummy",
      'password' => "dummy",
      'api_type' => 0,
      'api_url' => 0,
      'api_params' => 0,
      'is_active' => 1,
      'is_default' => 1,
    ));

    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  protected $_conversation1Params = array(
    'name' => 'Example 1: What are you up to?',
    'is_active' => 1,
  );

  protected $_question1Params = array(
    'text' => "Are you working or in education? Please answer 'A' for education or 'B' for working.",
    'text_invalid' => "Sorry I didn't understand, please reply 'A' for working or 'B' for education",
    'timeout' => 0,
  );

  protected $_action1Params = array(
    'answer_pattern' => "/working|education|a\s|b\s/i",
    'action_type' => 3, // Record in a custom field
  );

  protected $_contact1Params = array(
    // Mandatory
    //'conversation_id'] =>
    //'contact_id']
    //'source_contact_id']
    // Optional
    //'status_id']['api.required'] = 0;
    //'current_question_id']['api.required'] = 0;
    //'scheduled_date']['api.required'] = 0;
  );

  protected $_testContactParams = array(
    'contact_type' => "Individual",
    'first_name' => "Bob",
    'last_name' => "McEdwards",
  );

  protected $_testSourceContactParams = array(
    'contact_type' => "Individual",
    'first_name' => "Ed",
    'last_name' => "McEdwards",
  );

  public function createTestConversation1() {
    // Create conversation and assign id to params
    $conversation = civicrm_api3('SmsConversation', 'create', $this->_conversation1Params);
    $this->_conversation1Params['id'] = $conversation['id'];
  }

  public function createTestQuestion1() {
    // Add question
    $this->_question1Params['conversation_id'] = $this->_conversation1Params['id'];
    $question = civicrm_api3('SmsConversationQuestion', 'create', $this->_question1Params);
    $this->_question1Params['id'] = $question['id'];
    $this->_conversation1Params['start_question_id'] = $question['id'];
  }

  public function createTestAction1() {
    // Add action
    $uptoCustomField = civicrm_api3('CustomField', 'get', array(
      'name' => "what_are_you_up_to",
    ));
    $this->_action1Params['question_id'] = $this->_question1Params['id'];
    $this->_action1Params['action_data'] = "custom_".$uptoCustomField['id'];
    $action = civicrm_api3('SmsConversationAction', 'create', $this->_action1Params);
    $this->_action1Params['id'] = $action['id'];
  }

  public function createTestContact() {
    $result = civicrm_api3('Contact', 'create', $this->_testContactParams);
    $this->_testContactParams['id'] = $result['id'];
  }

  public function createTestSourceContact() {
    $result = civicrm_api3('Contact', 'create', $this->_testSourceContactParams);
    $this->_testSourceContactParams['id'] = $result['id'];
  }

  public function createTestPhoneMobile() {
    $result = civicrm_api3('Phone', 'create', array(
      'contact_id' => $this->_testContactParams['id'],
      'phone' => "01234",
      'phone_type_id' => "Mobile",
    ));
    $this->_testContactParams['mobile_id'] = $result['id'];
  }

  public function createTestPhoneFixed() {
    $result = civicrm_api3('Phone', 'create', array(
      'contact_id' => $this->_testContactParams['id'],
      'phone' => "56789",
      'phone_type_id' => "Phone",
    ));
    $this->_testContactParams['phone_id'] = $result['id'];
  }

  public function createTestContact1() {
    $this->createTestContact();
    $this->createTestSourceContact();
    $result = civicrm_api3('SmsConversationContact', 'create', array(
      'contact_id' => $this->_testContactParams['id'],
      'conversation_id' => $this->_conversation1Params['id'],
      'source_contact_id' => $this->_testSourceContactParams['id'],
    ));
    $this->_contact1Params = array(
      'contact_id' => $result['values'][$result['id']]['contact_id'],
      'conversation_id' => $result['values'][$result['id']]['conversation_id'],
      'source_contact_id' => $result['values'][$result['id']]['source_contact_id'],
      'id' => $result['id'],
    );
  }

  public function apiTestGet() {
    $params = $this->_params;
    unset($params['id']);
    // Create two new entities
    $this->callAPISuccess($this->_entity, 'create', $params);
    $this->callAPISuccess($this->_entity, 'create', $params);
    $result = $this->callAPISuccess($this->_entity, 'get', array());
    // Check that get fails when id not found
    $this->callAPIFailure($this->_entity, 'get', array('id' => 999));
    // We created 2 entities above and one during setup
    $this->assertEquals($result['count'], 3);
  }

  public function apiTestDeleteMandatoryMissing() {
    // Make sure delete fails when we don't specify any parameters
    $this->callAPIFailure($this->_entity, 'delete', array());
  }


  public function apiTestDelete() {
    // Create an entity, then delete it
    $entity = $this->callAPISuccess($this->_entity, 'create', $this->_params);
    $result = $this->callAPISuccess($this->_entity, 'delete', array('id' => $entity['id']));
    $this->assertEquals($result['count'], 1);
    // Try and get the now deleted entity
    $result2 = $this->callAPIFailure($this->_entity, 'get', array('id' => $entity['id']));
    $this->assertEquals($result2['is_error'], 1);
  }

  public function createTestConversation_example1() {
    $conversation = civicrm_api3('SmsConversation', 'create', array(
      'name' => 'Example 1: What are you up to?',
      'is_active' => 1,
    ));

    // Add question
    $question = civicrm_api3('SmsConversationQuestion', 'create', array(
      'text' => "Are you working or in education? Please answer 'A' for education or 'B' for working.",
      'text_invalid' => "Sorry I didn't understand, please reply 'A' for working or 'B' for education",
      'timeout' => 0,
      'conversation_id' => $conversation['id'],
    ));

    $convParams = $conversation['values'][$conversation['id']];
    $convParams['start_question_id'] = $question['id'];
    $conversation = civicrm_api3('SmsConversation', 'create', $convParams);

    $question2 = civicrm_api3('SmsConversationQuestion', 'create', array(
      'text' => "What is your job title?",
      'timeout' => 0,
      'conversation_id' => $conversation['id'],
    ));

    $question3 = civicrm_api3('SmsConversationQuestion', 'create', array(
      'text' => "Thanks for your time, and for letting us know!",
      'text_invalid' => "",
      'timeout' => 0,
      'conversation_id' => $conversation['id'],
    ));

    $uptoCustomField = civicrm_api3('CustomField', 'get', array(
      'name' => "what_are_you_up_to",
    ));
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "/working|education|a\s|b\s/i",
      'action_type' => 3, // Record in a custom field
      'action_data' => "custom_".$uptoCustomField['id'],
    ));

    // Action for answer a|working
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "/working|a\s/i",
      'action_type' => 1, // Ask another question
      'action_data' => $question2['id'],
    ));

    // Action for answer a|working
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "/.*/",
      'action_type' => 1, // Ask another question
      'action_data' => $question3['id'],
    ));

    // Action for answer a|working
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question2['id'],
      'answer_pattern' => "/.*/",
      'action_type' => 1, // Ask another question
      'action_data' => $question3['id'],
    ));

    // Action for answer b|education
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "/education|b\s/i",
      'action_type' => 2, // Add to group
      'action_data' => "5", // Group ID 5 (education)
    ));

    // Action for any valid answer
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question2['id'],
      'answer_pattern' => "/.*/",
      'action_type' => 3, // Record in a custom field
      'action_data' => "job_title", // Field ID
    ));
  }

}
