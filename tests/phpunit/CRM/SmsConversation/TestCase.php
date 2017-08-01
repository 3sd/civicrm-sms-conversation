<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

abstract class CRM_SmsConversation_TestCase extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
//abstract class CRM_SmsConversation_TestCase extends CiviUnitTestCase {
  use CiviUnitTestApiFunctions;

  protected $_apiversion = 3;

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->install(array('org.civicoop.civirules','civicrm.sms.conversations'))
      ->apply();
  }

  public function setUp() {
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

  public function createTestConversation1() {
    // Create conversation and assign id to params
    $conversation = civicrm_api3('SmsConversation', 'create', $this->_conversation1Params);
    $this->_conversation1Params['id'] = $conversation['id'];
  }

  public function createTestQuestion1() {
    // Add question
    $this->_question1Params['conversation_id'] = $this->_conversation1Params['id'];
    $question = civicrm_api3('SmsConversationQuestion', 'create', $this->_question1Params);
  }

  public function createTestAction1() {
    // Add action
    $this->_action1Params['question_id'] = $this->_question1Params['id'];
    $action = civicrm_api3('SmsConversationAction', 'create', $this->_action1Params);
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
