<?php

class CRM_SmsConversation_Examples {

  static function addExampleConversation1() {
    $conversation = civicrm_api3('SmsConversation', 'create', array(
      'name' => 'Example 1: What are you up to?',
      'is_active' => 1,
    ));

    // Add question
    $question = civicrm_api3('SmsConversationQuestion', 'create', array(
      'text' => "Can you tell us what you are up to at the moment, are you working, in education or doing something else?",
      'text_invalid' => "Sorry I didn't understand, please reply a for working or b for education",
      'timeout' => 0,
      'conversation_id' => $conversation['id'],
    ));

    $convParams = $conversation['values'][$conversation['id']];
    $convParams['start_question_id'] = $question['id'];
    $conversation = civicrm_api3('SmsConversation', 'create', $convParams);

    $question2 = civicrm_api3('SmsConversationQuestion', 'create', array(
      'text' => "Thankyou for your time",
      'text_invalid' => "",
      'timeout' => 0,
      'conversation_id' => $conversation['id'],
    ));

    // Action for any valid answer
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "/working|education|a\s|b\s/i",
      'action_type' => 3, // Record in a custom field
      'action_data' => "7", // Custom field with ID 7 (probably shouldn't be hardcoded)
    ));

    // Action for answer a|working
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "/working|a\s/i",
      'action_type' => 1, // Ask another question
      'action_data' => $question2['id'],
    ));

    // Action for answer b|education
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "/education|b\s/i",
      'action_type' => 2, // Add to group
      'action_data' => "5", // Group ID 5 (education)
    ));

    return TRUE;
  }

  static function addExampleConversation2() {
    $conversation = civicrm_api3('SmsConversation', 'create', array(
      'name' => 'Example 2: Collect information',
      'is_active' => 1,
    ));

    // Add question
    $question = civicrm_api3('SmsConversationQuestion', 'create', array(
      'text' => "What is your favourite colour?",
      'text_invalid' => "",
      'timeout' => 0,
      'conversation_id' => $conversation['id'],
    ));

    $convParams = $conversation['values'][$conversation['id']];
    $convParams['start_question_id'] = $question['id'];
    $conversation = civicrm_api3('SmsConversation', 'create', $convParams);

    $question2 = civicrm_api3('SmsConversationQuestion', 'create', array(
      'text' => "What is your mother's maiden name?",
      'text_invalid' => "",
      'timeout' => 0,
      'conversation_id' => $conversation['id'],
    ));

    $question3 = civicrm_api3('SmsConversationQuestion', 'create', array(
      'text' => "Thankyou for your time",
      'text_invalid' => "",
      'timeout' => 0,
      'conversation_id' => $conversation['id'],
    ));

    // Action for question 1
    $favColourCustomField = civicrm_api3('CustomField', 'get', array(
      'name' => "favourite_colour",
    ));
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "/.*/",
      'action_type' => 3, // Record in a customfield
      'action_data' => $favColourCustomField['id'], // Custom field ID
    ));

    // Action for question 2
    $maidenNameCustomField = civicrm_api3('CustomField', 'get', array(
      'sequential' => 1,
      'name' => "Mother_s_Maiden_Name",
    ));
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "/.*/",
      'action_type' => 1, // Ask another question
      'action_data' => $maidenNameCustomField['id'], // Custom field ID
    ));

    return TRUE;
  }
}