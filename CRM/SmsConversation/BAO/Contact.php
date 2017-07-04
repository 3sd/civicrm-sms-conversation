<?php

class CRM_SmsConversation_BAO_Contact extends CRM_SmsConversation_DAO_Contact {

  /**
   * Create a new SmsConversationContact based on array-data
   *
   * @param array $params key-value pairs
   *
   * @return CRM_SmsConversation_DAO_Contact|NULL
   */
  public static function create($params) {
    $className = 'CRM_SmsConversation_DAO_Contact';
    $entityName = 'SmsConversationContact';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  /**
   * Get the current conversation for the contact
   *
   * @param $contactId
   *
   * @return array|bool
   */
  static function getCurrentConversation($contactId) {
    // Get "In Progress" conversation for contact
    // FIXME: There should not be more than one, if there is we are getting the oldest one.  This behaviour may need to change.
    $inProgressId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact', 'status_id', 'In Progress');
    $convContact = civicrm_api3('SmsConversationContact', 'get', [
      'contact_id' => $contactId,
      'status_id' => $inProgressId,
      'options' => ['limit' => 1, 'sort' => "id ASC"],
    ]);

    if (empty($convContact['is_error']) && !empty($convContact['count'])) {
      return $convContact['values'];
    }
    else {
      return FALSE;
    }
  }

  /**
   * Get the next scheduled conversation for a contact
   *
   * @param $contactId
   *
   * @return array|bool
   */
  static function getNextScheduledConversation($contactId) {
    // Get next "Scheduled" conversation for contact
    // We select the earliest by scheduled date, then earliest by Id and select the first one
    $scheduledId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact', 'status_id', 'Scheduled');
    $convContact = civicrm_api3('SmsConversationContact', 'get', [
      'sequential' => 1,
      'contact_id' => $contactId,
      'status_id' => $scheduledId,
      'options' => ['limit' => 1, 'sort' => "scheduled_date ASC,id ASC"],
      'scheduled_date' => array('<=' => "now"),
    ]);

    if (empty($convContact['is_error']) && !empty($convContact['count'])) {
      return $convContact['values'][0];
    }
    else {
      return FALSE;
    }
  }

  /**
   * Start a conversation with contact
   *
   * @param $contactId
   * @param $conversationId
   *
   * @return SmsConversation.contact_id or FALSE
   */
  static function startConversation($contactId, $id = NULL) {
    // Don't allow another conversation to start
    if (CRM_SmsConversation_BAO_Contact::getCurrentConversation($contactId)) {
      throw new CRM_Core_Exception('This contact already has a conversation in progress');
    }

    // Get conversation contact
    if (!empty($id)) {
      // Use passed in conversation contact id
      $convContact = civicrm_api3('SmsConversationContact', 'get', [
        'sequential' => 1,
        'id' => $id,
      ]);
      if (empty($convContact['is_error']) && !empty($convContact['count'])) {
        $convContact =  $convContact['values'][0];
      }
    }
    if (empty($convContact)) {
      // If we don't already have a conversation to work with, get one
      $convContact = CRM_SmsConversation_BAO_Contact::getNextScheduledConversation($contactId);
    }

    if (empty($convContact)) {
      throw new CRM_Core_Exception('No scheduled conversations found');
    }

    if (!empty($convContact['is_error'])) {
      return FALSE;
    }

    // Get the conversation
    $conversation = CRM_SmsConversation_BAO_Conversation::getConversation($convContact['conversation_id']);
    if (!$conversation['is_active']) {
      return FALSE;
    }

    // Get the question
    $question = CRM_SmsConversation_BAO_Question::getQuestion($conversation['start_question_id']);
    // Update conversation contact status
    CRM_SmsConversation_BAO_Contact::updateStatus($convContact['id'], 'In Progress');

    $convContact = CRM_SmsConversation_BAO_Contact::getCurrentConversation($contactId);
    // Ask the question
    if (CRM_SmsConversation_BAO_Question::ask($question['id'], $contactId, reset($convContact))) {
      return $convContact['id'];
    }
    else {
      return FALSE;
    }
  }

  /**
   * End the conversation
   *
   * @param $id
   * @param string $status
   *
   * @return bool
   */
  static function endConversation($id, $status = 'Completed') {
    return CRM_SmsConversation_BAO_Contact::updateStatus($id, $status, NULL);
  }

  /**
   * Update the conversation status
   *
   * @param $id
   * @param $status (crm_smsconversation_status_type)
   *
   * @return bool
   */
  static function updateStatus($id, $status) {
    // Mark the conversation as $status
    $statusId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact', 'status_id', $status);
    if (empty($statusId)) {
      return FALSE;
    }
    $convContact = civicrm_api3('SmsConversationContact', 'create', [
      'id' => $id,
      'status_id' => $statusId,
    ]);
    if (empty($convContact['is_error'])) {
      return $convContact['values'][$id];
    }
    return FALSE;
  }

  /**
   * Record the conversation progress in conversation_record
   *
   * @param $convContact
   * @param $convQuestion
   * @param $sms
   * @param $validAnswer
   *
   * @return bool
   */
  static function recordConversation($convContact, $convQuestion, $sms, $validAnswer) {
    // TODO: Test this function, do we use serialize or something else?
    // Also record sms and validanswer
    $records = json_decode($convContact['conversation_record']);
    $records[] = [
      'q' => $convQuestion['id'],
      'a' => $sms,
      'v' => (boolean) $validAnswer
    ];

    $convContact['conversation_record'] = json_encode($records);
    $convContactResult = civicrm_api3('SmsConversationContact', 'create', $convContact);

    if (empty($convContactResult['is_error'])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Get the count of the conversations for contact
   *
   * @param $contactId
   *
   * @return array|bool
   */
  static function getConversationCount($contactId) {
    // Get count of all conversations for contact
    $convContactCount = civicrm_api3('SmsConversationContact', 'getcount', [
      'contact_id' => $contactId,
    ]);

    return $convContactCount;
  }

  static function getConversationList($params) {
    $params['sequential'] = 1;
    $params['contact_id'] = $params['cid'];

    $params['rowCount'] = $params['rp'];
    if (!empty($params['sortBy'])) {
      $params['sort'] = $params['sortBy'];
    }

    $convList = civicrm_api3('SmsConversationContact', 'get', $params);

    $DT['data'] = array(); // Datatables requires the data element even if no data

    foreach ($convList['values'] as $convContact) {
      $conversation = civicrm_api3('SmsConversation', 'get', ['id' => $convContact['conversation_id']]);
      $convContact['conversation_name'] = $conversation['values'][$convContact['conversation_id']]['name'];
      // Format status
      $convContact['status'] = CRM_Core_PseudoConstant::getLabel('CRM_SmsConversation_BAO_Contact', 'status_id', $convContact['status_id']);
      // Format name
      $sourceContact = civicrm_api3('Contact', 'getsingle', array(
        'return' => array("display_name"),
        'id' => $convContact['source_contact_id'],
      ));
      $url = CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid='.$convContact['source_contact_id']);
      $convContact['source_contact'] = "<a href='{$url}'>{$sourceContact['display_name']}</a>";
      // Format Date
      $convContact['date'] = CRM_Utils_Date::customFormat($convContact['scheduled_date']);
      // Format current question for display (show a shortened (to 30 chars) question text label)
      $convContact['current_question_id'] = CRM_SmsConversation_BAO_Question::getShortQuestionLabel($convContact['current_question_id']);
      // Add links
      $links = self::actionLinks();
      // Get mask
      $mask = CRM_Core_Action::VIEW;
      $scheduledId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact','status_id', 'Scheduled');
      $inProgressId = CRM_Core_PseudoConstant::getKey('CRM_SmsConversation_BAO_Contact','status_id', 'In Progress');
      switch ($convContact['status_id']) {
        case $scheduledId:
          // We show delete if in scheduled state
          $mask += CRM_Core_Action::DELETE;
          break;
        case $inProgressId:
          // We show cancel if in "In Progress" state
          $mask += CRM_Core_Action::UPDATE;
          break;
      }
      $convContact['links'] = CRM_Core_Action::formLink($links,
        $mask,
        array(
          'cid' => $params['cid'],
          'conversation' => $convContact['id'],
        ),
        ts('more')
      );
      $DT['data'][] = $convContact;
    }
    $DT['recordsTotal'] = self::getConversationCount($params['cid']);
    $DT['recordsFiltered'] = $DT['recordsTotal'];
    return $DT;
  }

  static function actionLinks() {
    $links = array(
      CRM_Core_Action::VIEW => array(
        'name' => ts('View'),
        'url' => 'civicrm/contact/view/smsconversation/detail',
        'qs' => 'reset=1&action=view&cid=%%cid%%&conversation=%%conversation%%',
        'title' => ts('View Conversation'),
        'class' => 'crm-popup',
      ),
      CRM_Core_Action::DELETE => array(
        'name' => ts('Delete'),
        'url' => 'civicrm/contact/view/smsconversation/detail',
        'qs' => 'reset=1&action=delete&cid=%%cid%%&conversation=%%conversation%%',
        'title' => ts('Delete Conversation'),
        'class' => 'crm-popup',
      ),
      CRM_Core_Action::UPDATE => array(
        'name' => ts('Cancel'),
        'url' => 'civicrm/contact/view/smsconversation/detail',
        'qs' => 'reset=1&action=update&cid=%%cid%%&conversation=%%conversation%%',
        'title' => ts('Cancel Conversation'),
        'class' => 'crm-popup',
      ),
    );
    return $links;
  }

  /**
   * Get all contact Ids that have conversations
   */
  static function getAllContactIds() {
    $params = array(
      'options' => array('limit' => 0),
      'return' => array("contact_id"),
    );

    $contactIds = array();
    $convContact = civicrm_api3('SmsConversationContact', 'get', $params);
    if (empty($convContact['is_error'])) {
      foreach ($convContact['values'] as $key => $conv) {
        $contactIds[$conv['contact_id']] = $conv['contact_id'];
      }
      return $contactIds;
    }
    return FALSE;
  }

  /**
   * Schedule conversations. If contactId specified schedule conversations for that contact only.
   * @param null $contactId
   */
  static function scheduleConversations($contactId = NULL) {
    $params = array(
      'status_id' => "In Progress",
    );
    if (!empty($contactId)) {
      $contactIds[$contactId] = $contactId;
    }
    else {
      $contactIds = self::getAllContactIds();
    }

    // Loop through each contact and start a conversation if one is waiting
    $result = array();
    foreach ($contactIds as $cid => $value) {
      $params['contact_id'] = $cid;
      $convContact = civicrm_api3('SmsConversationContact', 'get', $params);
      if (empty($convContact['count'])) {
        // No conversations in progress so we need to schedule one
        // Get the one with the oldest date that is in state "Scheduled"
        $convContactScheduled = self::getNextScheduledConversation($cid);
        if ($convContactScheduled) {
          // Start the conversation
          $contactId = self::startConversation($cid, $convContactScheduled['id']);
          if ($contactId) {
            $result[$contactId] = $contactId;
          }
        }
      }
    }
    return $result;
  }
}
