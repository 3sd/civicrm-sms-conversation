<?php

class CRM_SmsConversation_Examples {

  function addExampleConversation1() {
    // Add question
    $question = civicrm_api3('SmsConversationQuestion', 'create', array(
      'text' => "Can you tell us what you are up to at the moment, are you working, in education or doing something else?",
      'text_invalid' => "Sorry I didn't understand, please reply a for working or b for education",
      'timeout' => 0,
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
      'action_data' => "", // FIXME
    ));

    // Action for answer b|education
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "/education|b\s/i",
      'action_type' => 2, // Add to group
      'action_data' => "5", // Group ID 5 (education)
    ));

  }

  function addExampleConversation2() {
    // Add question
    $question = civicrm_api3('SmsConversationQuestion', 'create', array(
      'text' => "What is your favourite colour?",
      'text_invalid' => "",
      'timeout' => 0,
    ));

    $question2 = civicrm_api3('SmsConversationQuestion', 'create', array(
      'text' => "What is your mother's maiden name?",
      'text_invalid' => "",
      'timeout' => 0,
    ));

    $question3 = civicrm_api3('SmsConversationQuestion', 'create', array(
      'text' => "Thankyou for your time",
      'text_invalid' => "",
      'timeout' => 0,
    ));

    // Action for question 1
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "",
      'action_type' => 3, // Record in a customfield
      'action_data' => 8, // Custom field with ID 8 (probably shouldn't be hardcoded)
    ));

    // Action for question 1
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question['id'],
      'answer_pattern' => "",
      'action_type' => 1, // Ask another question
      'action_data' => $question2['id'], // Custom field with ID 7 (probably shouldn't be hardcoded)
    ));

    // Action for question 2
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question2['id'],
      'answer_pattern' => "",
      'action_type' => 3, // Record in a customfield
      'action_data' => 9, // Custom field with ID 8 (probably shouldn't be hardcoded)
    ));

    // Action for question 2
    $action = civicrm_api3('SmsConversationAction', 'create', array(
      'question_id' => $question2['id'],
      'answer_pattern' => "",
      'action_type' => 1, // Ask another question
      'action_data' => $question3['id'], // Custom field with ID 7 (probably shouldn't be hardcoded)
    ));
  }
}