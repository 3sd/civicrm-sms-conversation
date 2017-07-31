<?php

//require_once 'CiviTest/CiviUnitTestCase.php';

class CRM_SmsConversation_TestCase extends PHPUnit_Framework_TestCase {

  protected $_apiversion = 3;

  /**
   * wrap api functions.
   * so we can ensure they succeed & throw exceptions without litterering the test with checks
   *
   * @param string $entity
   * @param string $action
   * @param array $params
   * @param mixed $checkAgainst
   *   Optional value to check result against, implemented for getvalue,.
   *   getcount, getsingle. Note that for getvalue the type is checked rather than the value
   *   for getsingle the array is compared against an array passed in - the id is not compared (for
   *   better or worse )
   *
   * @return array|int
   */
  public function callAPISuccess($entity, $action, $params, $checkAgainst = NULL) {
    $params = array_merge(array(
      'version' => $this->_apiversion,
      'debug' => 1,
    ),
      $params
    );
    switch (strtolower($action)) {
      case 'getvalue':
        return $this->callAPISuccessGetValue($entity, $params, $checkAgainst);

      case 'getsingle':
        return $this->callAPISuccessGetSingle($entity, $params, $checkAgainst);

      case 'getcount':
        return $this->callAPISuccessGetCount($entity, $params, $checkAgainst);
    }
    $result = $this->civicrm_api($entity, $action, $params);
    $this->assertAPISuccess($result, "Failure in api call for $entity $action");
    return $result;
  }

  /**
   * This function exists to wrap api getValue function & check the result
   * so we can ensure they succeed & throw exceptions without litterering the test with checks
   * There is a type check in this
   *
   * @param string $entity
   * @param array $params
   * @param string $type
   *   Per http://php.net/manual/en/function.gettype.php possible types.
   *   - boolean
   *   - integer
   *   - double
   *   - string
   *   - array
   *   - object
   *
   * @return array|int
   */
  public function callAPISuccessGetValue($entity, $params, $type = NULL) {
    $params += array(
      'version' => $this->_apiversion,
      'debug' => 1,
    );
    $result = $this->civicrm_api($entity, 'getvalue', $params);
    if ($type) {
      if ($type == 'integer') {
        // api seems to return integers as strings
        $this->assertTrue(is_numeric($result), "expected a numeric value but got " . print_r($result, 1));
      }
      else {
        $this->assertType($type, $result, "returned result should have been of type $type but was ");
      }
    }
    return $result;
  }

  /**
   * This function exists to wrap api getValue function & check the result
   * so we can ensure they succeed & throw exceptions without litterering the test with checks
   * There is a type check in this
   * @param string $entity
   * @param array $params
   * @param null $count
   * @throws Exception
   * @return array|int
   */
  public function callAPISuccessGetCount($entity, $params, $count = NULL) {
    $params += array(
      'version' => $this->_apiversion,
      'debug' => 1,
    );
    $result = $this->civicrm_api($entity, 'getcount', $params);
    if (!is_int($result) || !empty($result['is_error']) || isset($result['values'])) {
      throw new Exception('Invalid getcount result : ' . print_r($result, TRUE) . " type :" . gettype($result));
    }
    if (is_int($count)) {
      $this->assertEquals($count, $result, "incorrect count returned from $entity getcount");
    }
    return $result;
  }

  /**
   * This function exists to wrap api getsingle function & check the result
   * so we can ensure they succeed & throw exceptions without litterering the test with checks
   *
   * @param string $entity
   * @param array $params
   * @param array $checkAgainst
   *   Array to compare result against.
   *   - boolean
   *   - integer
   *   - double
   *   - string
   *   - array
   *   - object
   *
   * @throws Exception
   * @return array|int
   */
  public function callAPISuccessGetSingle($entity, $params, $checkAgainst = NULL) {
    $params += array(
      'version' => $this->_apiversion,
      'debug' => 1,
    );
    $result = $this->civicrm_api($entity, 'getsingle', $params);
    if (!is_array($result) || !empty($result['is_error']) || isset($result['values'])) {
      throw new Exception('Invalid getsingle result' . print_r($result, TRUE));
    }
    if ($checkAgainst) {
      // @todo - have gone with the fn that unsets id? should we check id?
      $this->checkArrayEquals($result, $checkAgainst);
    }
    return $result;
  }

  /**
   * Check that api returned 'is_error' => 0.
   *
   * @param array $apiResult
   *   Api result.
   * @param string $prefix
   *   Extra test to add to message.
   */
  public function assertAPISuccess($apiResult, $prefix = '') {
    if (!empty($prefix)) {
      $prefix .= ': ';
    }
    $errorMessage = empty($apiResult['error_message']) ? '' : " " . $apiResult['error_message'];

    if (!empty($apiResult['debug_information'])) {
      $errorMessage .= "\n " . print_r($apiResult['debug_information'], TRUE);
    }
    if (!empty($apiResult['trace'])) {
      $errorMessage .= "\n" . print_r($apiResult['trace'], TRUE);
    }
    $this->assertEquals(0, $apiResult['is_error'], $prefix . $errorMessage);
  }

  /**
   * This function exists to wrap api functions.
   * so we can ensure they fail where expected & throw exceptions without litterering the test with checks
   * @param string $entity
   * @param string $action
   * @param array $params
   * @param string $expectedErrorMessage
   *   Error.
   * @param null $extraOutput
   * @return array|int
   */
  public function callAPIFailure($entity, $action, $params, $expectedErrorMessage = NULL, $extraOutput = NULL) {
    if (is_array($params)) {
      $params += array(
        'version' => $this->_apiversion,
      );
    }
    $result = $this->civicrm_api($entity, $action, $params);
    $this->assertAPIFailure($result, "We expected a failure for $entity $action but got a success", $expectedErrorMessage);
    return $result;
  }

  /**
   * Check that api returned 'is_error' => 1.
   *
   * @param array $apiResult
   *   Api result.
   * @param string $prefix
   *   Extra test to add to message.
   * @param null $expectedError
   */
  public function assertAPIFailure($apiResult, $prefix = '', $expectedError = NULL) {
    if (!empty($prefix)) {
      $prefix .= ': ';
    }
    if ($expectedError && !empty($apiResult['is_error'])) {
      $this->assertEquals($expectedError, $apiResult['error_message'], 'api error message not as expected' . $prefix);
    }
    $this->assertEquals(1, $apiResult['is_error'], "api call should have failed but it succeeded " . $prefix . (print_r($apiResult, TRUE)));
    $this->assertNotEmpty($apiResult['error_message']);
  }

  /**
   * A stub for the API interface. This can be overriden by subclasses to change how the API is called.
   *
   * @param $entity
   * @param $action
   * @param array $params
   * @return array|int
   */
  public function civicrm_api($entity, $action, $params) {
    return civicrm_api($entity, $action, $params);
  }

  public function createTestConversation1() {
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
