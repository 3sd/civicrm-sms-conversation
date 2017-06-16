<?php

class CRM_SmsConversation_Page_Tab extends CRM_Core_Page {

  public function run() {
    // Get contact Id
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $this->assign('contactId', $this->_contactId);

    // check logged in url permission
    CRM_Contact_Page_View::checkUserPermission($this);


    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('Tab'));

    // Example: Assign a variable for use in a template
    $this->assign('currentTime', date('Y-m-d H:i:s'));

    parent::run();
  }
}
