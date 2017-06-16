<?php

class CRM_SmsConversation_Page_View extends CRM_Core_Page {

  public function run() {


    // Get the conversation id from the form
    $this->id = CRM_Utils_Request::retrieve('id', 'Positive', $this);

    // Get the relevant entities
    try {
      $this->conversation = civicrm_api3('SmsConversation', 'getsingle', ['id' => $this->id]);
    }
    catch (Exception $e) {
      // If we can't find a conversation with id, redirect to the conversation overview
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/sms/conversation'));
    }
    CRM_Utils_System::setTitle(ts('SMS conversation: %1', [1 => $this->conversation['name']]));

    $this->questions = civicrm_api3('SmsConversationQuestion', 'get', ['conversation_id' => $this->id])['values'];

    $questionIds = array_column($this->questions, 'id');
    $this->actions = civicrm_api3('SmsConversationAction', 'get', [ 'question_id' => ['IN' => $questionIds], ])['values'];

    // Decorate the actions
    foreach($this->actions as $key => $action){
      $this->actions[$key]['friendly_answer_pattern'] = $this->getFriendlyAnswerPattern($action['answer_pattern']);
    }


    // Decorate the questions...

    // ...add an actions array to each question
    foreach($this->questions as $key => $question){
      $this->questions[$key]['actions'] = [];
    }
    foreach($this->actions as $action){
      $this->questions[$action['question_id']]['actions'][] = $action;
    }

    // ...create a map of questions to next questions for ordering the questions
    $this->nextQuestionsMap = [];
    $groupIds = [];
    $fieldIds = [];

    foreach($this->actions as $action){
      // Get the next question so we can order questions appropriatley
      if($action['action_type'] == 1){
        $this->nextQuestionsMap[$action['question_id']][]=$action['action_data'];
      }
      // Get the group id so we can retreive the group title
      if($action['action_type'] == 2){
        $groupIds[] = $action['action_data'];
      }
    }

    $this->contactFieldTitles = array_column(civicrm_api3('Contact', 'getfields')['values'], 'title', 'name');

    // ...order the questions appropriatley
    $this->questionNumber = 1;
    $this->orderedQuestions[$this->questionNumber] = $this->questions[$this->conversation['start_question_id']];
    $this->orderedQuestions[$this->questionNumber]['number'] = $this->questionNumber;
    unset($this->questions[$this->conversation['start_question_id']]);
    while($this->transplantQuestions());

    // ...recreate the question array with question ID as array keys
    foreach($this->orderedQuestions as $question){
      $this->unorderedQuestions[$question['id']]=$question;
    }

    // Assign template variables
    $this->assign('conversation', $this->conversation);
    $this->assign('orderedQuestions', $this->orderedQuestions);
    $this->assign('unorderedQuestions', $this->unorderedQuestions);
    $this->assign('contactFieldTitles', $this->contactFieldTitles);

    if($groupIds){
      $groups = array_column(civicrm_api3('Group', 'get', ['id' => ['IN' => $groupIds]])['values'], 'title', 'id');
      $this->assign('groups', $groups);
    }


    // Get the questions
    parent::run();
  }

  function transplantQuestions(){
    $this->questionNumber++;
    $currentQuestion = current($this->orderedQuestions);
    if(isset($this->nextQuestionsMap[$currentQuestion['id']])){
      $nextQuestions = $this->nextQuestionsMap[$currentQuestion['id']];
      foreach($nextQuestions as $nextQuestion){
        if(isset($this->questions[$nextQuestion])){
          $this->orderedQuestions[$this->questionNumber] = $this->questions[$nextQuestion];
          $this->orderedQuestions[$this->questionNumber]['number'] = $this->questionNumber;
          unset($this->questions[$nextQuestion]);
        }
      }
    }
    return next($this->orderedQuestions);
  }

  function getFriendlyAnswerPattern($pattern){
    if($pattern == '/.*/'){
      return 'anything';
    }
    return $pattern;

  }
}
