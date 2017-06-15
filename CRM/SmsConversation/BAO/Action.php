<?php

class CRM_SmsConversation_BAO_Action extends CRM_SmsConversation_DAO_Action {

  /**
   * Create a new SmsConversationAction based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_SmsConversation_DAO_Action|NULL
   */
  public static function create($params) {
    $className = 'CRM_SmsConversation_DAO_Action';
    $entityName = 'SmsConversationAction';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  /**
   * Check if the SMS is a valid answer for the action
   * @param $action
   * @param $sms
   *
   * @return bool
   */
  static function validAnswer($action, $sms) {
    // answer_pattern will be in regex format: eg /abc/i
    // If this is extended to support alternative matching (eg. optionvalue) we would add a switch statement matching on the first character.
    if (preg_match($action['answer_pattern'], $sms)) {
      return $action;
    }
    return FALSE;
  }

  /**
   * Get all actions for question ID
   * @param $questionId
   *
   * @return bool
   */
  static function getAction($questionId) {
    // Contact is having a conversation, get the possible actions for the question
    $convActions = civicrm_api3('SmsConversationAction', 'get', array(
        'question_id' => $questionId,
      )
    );

    if (!empty($convActions['is_error']) || !isset($convActions['values'])) {
      // Something went wrong getting actions
      return FALSE;
    }
    return $convActions['values'];
  }

  /**
   * Process action
   * @param $action
   *
   * @return bool
   */
  static function processAction($action, $contactId, $conversationId, $sms) {
    // Perform action based on action_type, action_data and answer
    // TODO processAction
    Civi::log('processAction');
    if (!isset($action['action_type'])) {
      return FALSE;
    }

    switch ($action['action_type']) {
      case 1: // Ask another question
        return self::actionAskQuestion($action, $contactId);
        break;
      case 2: // Add contact to group
        return self::actionAddContactToGroup($action, $contactId);
        break;
      case 3: // Record answer in custom field
        return self::actionRecordInCustomField($action, $contactId, $sms);
        break;
      case 4: // Trigger CiviRule
        Civi::log('Trigger CiviRule not implemented');
        break;
      default:
        Civi::log('SmsConversationAction::processAction: '.$action['action_type'].' not implemented');
        CRM_SmsConversation_BAO_Conversation::end($contactId, $conversationId);

    }
  }

  /**
   * Process the ask question action
   * @param $action
   */
  static function actionAskQuestion($action, $contactId) {
    // Trigger another question
    // action_date = next question_id
    // Trigger the question
    $convContact = CRM_SmsConversation_BAO_Contact::getCurrentConversation($contactId);

    CRM_SmsConversation_BAO_Question::ask($action['question_id'], $contactId, $convContact);
  }

  /**
   * Add contact to group specified by ID in action_data
   * @param $action
   * @param $contactId
   */
  static function actionAddContactToGroup($action, $contactId) {
    $groupResult = civicrm_api3('GroupContact', 'create', array(
      'group_id' => $action['action_data'],
      'contact_id' => $contactId,
    ));
    if (empty($groupResult['is_error'])) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Record answer in custom field specified by ID in action_data
   * @param $action
   * @param $contactId
   */
  static function actionRecordInCustomField($action, $contactId, $sms) {
    // Get contact and then create with additional custom field as parameter
    $contact = civicrm_api3('Contact', 'get', array(
      'id' => $contactId,
    ));
    $params = $contact['values'][$contactId];

    $customFieldName = 'custom_' . $action['action_data'];
    $params[$customFieldName] = $sms;
    $contactResult = civicrm_api3('Contact', 'create', $params);
    if (empty($contactResult['is_error'])) {
      return FALSE;
    }
    return TRUE;
  }

}
