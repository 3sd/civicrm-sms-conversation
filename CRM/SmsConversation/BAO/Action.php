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
   * Get weighting of next question action
   *
   * @param $action
   */
  static function processNextQuestionActionData(&$action) {
    // weight is stored in action_data.
    // eg. 0:2 ~= weight:question ID
    $actionData = explode(':',$action['action_data']);
    if (!$actionData || count($actionData) < 2) {
      $action['weight'] = 0;
      $action['next_question_id'] = $action['action_data'];
      $action['action_data'] = $action['weight'] . ':' . $action['next_question_id'];
    }
    else {
      $action['weight'] = (empty($actionData[0]) ? 0 : $actionData[0]);
      $action['next_question_id'] = $actionData[1];
    }
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
        return self::actionRecordInField($action, $contactId, $sms);
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

    self::processNextQuestionActionData($action);

    CRM_SmsConversation_BAO_Question::ask($action['next_question_id'], $contactId, $convContact);
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
   * Record answer in field specified in action_data
   * @param $action
   * @param $contactId
   */
  static function actionRecordInField($action, $contactId, $sms) {

    $fieldName = $action['action_data'];
    $params[$fieldName] = $sms;

    // Work out what entity we should be creating / updating

    // If this is an address field, update the primary address if it exists,
    // else create a new (primary) address.
    if(self::isAddressField($fieldName)){
      $entity = 'Address';
      try{
        $params['id'] = civicrm_api3('Address', 'getsingle', [
          'contact_id' => $contactId,
          'is_primary' => '1'
        ])['id'];
      }catch (Exception $e){
        $params['contact_id'] = $contactId;
        $params['location_type_id'] = civicrm_api3('LocationType', 'getvalue', [
          'return' => "id",
          'is_default' => 1
        ]);
      }
    // Else, assume we are updating the contact. I *think* that email will be
    // handled reasonably, and think that asking for a phone number via SMS can
    // probably be classed as an edge case
    }else{

      $entity = 'Contact';
      $params['id'] = $contactId;
    }

    $result = civicrm_api3($entity, 'create', $params);
    if (empty($result['is_error'])) {
      return TRUE;
    }
    return FALSE;
  }

  static function isAddressField($field){
    $addressFields = civicrm_api3('Address', 'getfields')['values'];
    return in_array($field, array_keys($addressFields));
  }
}
