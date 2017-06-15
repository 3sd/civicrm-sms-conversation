<?php

class CRM_SmsConversation_Page_Conversations extends CRM_Core_Page {

  public function run() {
    $conversationCount = civicrm_api3('SmsConversation', 'getcount');
    $conversations = civicrm_api3('SmsConversation', 'get', ['option.limit' => $conversationCount])['values'];
    $this->assign('conversations', $conversations);
    parent::run();
  }

}
