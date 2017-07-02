<?php

class CRM_SmsConversation_Page_Tab extends CRM_Core_Page {

  public function run() {
    // Get contact Id
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $this->assign('contactId', $this->_contactId);

    // check logged in url permission
    CRM_Contact_Page_View::checkUserPermission($this);

    $this->ajaxResponse['tabCount'] = CRM_SmsConversation_BAO_Contact::getConversationCount($this->_contactId);
    return parent::run();
  }
}
