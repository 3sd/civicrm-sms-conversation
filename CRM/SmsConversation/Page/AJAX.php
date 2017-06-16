<?php

class CRM_SmsConversation_Page_AJAX {

  public static function getContactConversations() {
    $params = $_GET;
    $requiredParams = array(
      'cid' => 'Integer',
    );
    $optionalParams = array(
      'conversation_name' => 'String',
      'source_contact_id' => 'String',
      'scheduled_date' => 'String',
      'status_id' => 'Integer',
    );
    $params = CRM_Core_Page_AJAX::defaultSortAndPagerParams();
    $params += CRM_Core_Page_AJAX::validateParams($requiredParams, $optionalParams);

    // get conversation list
    $conversations = CRM_SmsConversation_BAO_Contact::getConversationList($params);

    CRM_Utils_JSON::output($conversations);
  }
}
