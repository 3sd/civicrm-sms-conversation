<?php
/**
 * Class for CiviRules Group Contact Action Form
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_SmsConversation_CivirulesActions_Form_Schedule extends CRM_CivirulesActions_Form_Form {


  /**
   * Overridden parent method to build the form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_action_id');
    $this->addEntityRef('conversation_id', ts('Conversation'), [
      'entity' => 'SmsConversation',
      'api' => ['label_field' => 'name'],
      'placeholder' => ts('- Select conversation -'),
      'select' => ['minimumInputLength' => 0]
    ], TRUE);
    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));
  }

  /**
   * Overridden parent method to set default values
   *
   * @return array $defaultValues
   * @access public
   */
  public function setDefaultValues() {
    $defaultValues = parent::setDefaultValues();
    $data = unserialize($this->ruleAction->action_params);
    if (!empty($data['conversation_id'])) {
      $defaultValues['conversation_id'] = $data['conversation_id'];
    }
    return $defaultValues;
  }

  /**
   * Overridden parent method to process form data after submitting
   *
   * @access public
   */
  public function postProcess() {
    $data['conversation_id'] = $this->_submitValues['conversation_id'];
    $this->ruleAction->action_params = serialize($data);
    $this->ruleAction->save();
    parent::postProcess();
  }

}
