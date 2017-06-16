<?php

class CRM_SmsConversation_Examples {

  static function addExampleConversation1() {
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

    // Action for question 1 (record in a custom field)
    $favColourCustomField = civicrm_api3('CustomField', 'get', array(
      'name' => "favourite_colour",
    ));
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "/.*/",
      'action_type' => 3, // Record in a customfield
      'action_data' => "custom_".$favColourCustomField['id'], // Custom field ID
    ));

    // Action for question 1 (ask another question)
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "/.*/",
      'action_type' => 1, // Ask another question
      'action_data' => $question2['id'], // Question ID
    ));

    // Action for question 2 (record in a custom field)
    $maidenNameCustomField = civicrm_api3('CustomField', 'get', array(
      'sequential' => 1,
      'name' => "Mother_s_Maiden_Name",
    ));

    // Action for question 2 (ask another question)
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question2['id'],
      'answer_pattern' => "/.*/",
      'action_type' => 1, // Ask another question
      'action_data' => $question3['id'], // Question ID
    ));

    return TRUE;
  }
}
