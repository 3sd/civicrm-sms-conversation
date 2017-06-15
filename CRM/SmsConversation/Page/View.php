<?php

class CRM_SmsConversation_Page_View extends CRM_Core_Page {

  public function run() {


    // Get the conversation id from the form
    $this->id = CRM_Utils_Request::retrieve('id', 'Positive', $this);

    // Get the relevant entities
    $this->conversation = civicrm_api3('SmsConversation', 'getsingle', ['id' => $this->id]);
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
      if($action['action_type'] == 1){ //
        $this->nextQuestionsMap[$action['question_id']][]=$action['action_data'];
      }
      if($action['action_type'] == 2){
        $groupIds[] = $action['action_data'];
      }
      if($action['action_type'] == 3){
        $fieldIds[] = $action['action_data'];
      }
    }

    var_dump($groupIds);
    var_dump($fieldIds);


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

  }
}
