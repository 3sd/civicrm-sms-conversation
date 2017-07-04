<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_SmsConversation_CivirulesActions_Schedule extends CRM_CivirulesActions_Generic_Api {

  /**
   * Method to get the api entity to process in this CiviRule action
   *
   * @access protected
   * @abstract
   */
  protected function getApiEntity() {
    return 'SmsConversationContact';
  }

  /**
   * Method to get the api action to process in this CiviRule action
   *
   * @access protected
   * @abstract
   */
  protected function getApiAction() {
    return 'Create';
  }

  /**
   * Returns an array with parameters used for processing an action
   *
   * @param array $parameters
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return array
   * @access protected
   */
  protected function alterApiParameters($parameters, CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $action_params = $this->getActionParameters();
    $activityData = $triggerData->getEntityData('Activity');
    $acParams['activity_id'] = $activityData['id'];
    $acParams['record_type_id'] = 3;
    $this->ac = civicrm_api3('ActivityContact', 'getsingle', $acParams);
    $parameters['contact_id'] = $this->ac['contact_id'];
    $parameters['conversation_id'] = $action_params['conversation_id'];
    $parameters['process_now'] = true;
    $session = CRM_Core_Session::singleton();
    $parameters['source_contact_id'] = $session->get('userID');
    $parameters['source_record_id'] = true;

    return $parameters;
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a action
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleActionId
   * @return bool|string
   * @access public
   */
  public function getExtraDataInputUrl($ruleActionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/action/smsconversation/schedule', 'rule_action_id='.$ruleActionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $return = '';
    $params = $this->getActionParameters();
    $conversation = civicrm_api3('SmsConversation', 'Getsingle', ['id' => $params['conversation_id']]);
    $return .= ts("Conversation: %1", array(1 => $conversation['name']));

    return $return;
  }

  /**
   * This function validates whether this action works with the selected trigger.
   *
   * This function could be overriden in child classes to provide additional validation
   * whether an action is possible in the current setup.
   *
   * @param CRM_Civirules_Trigger $trigger
   * @param CRM_Civirules_BAO_Rule $rule
   * @return bool
   */
  public function doesWorkWithTrigger(CRM_Civirules_Trigger $trigger, CRM_Civirules_BAO_Rule $rule) {
    $entities = $trigger->getProvidedEntities();
    if (isset($entities['Activity'])) {
      return true;
    }
    return false;
  }

  protected function executeApiAction($entity, $action, $parameters) {

    $currentConversation = civicrm_api3('SmsConversationContact', 'getcurrent', array('contact_id' => $this->ac['contact_id']));
    if (!empty($currentConversation['count'])) {
      return;
    }
    parent::executeApiAction($entity, $action, $parameters);
  }


}
