CRM.$(function($) {

  controlAnswerPatternRaw();
  controlActionData();

  $(document).on( "change", '[name=answer_pattern_type]', controlAnswerPatternRaw);

  function controlAnswerPatternRaw(){
    if($('[name=answer_pattern_type]').val() === 'anything' ){
      $('tr.answer_pattern_raw').hide();
    }else{
      $('tr.answer_pattern_raw').show();
    }
  }

  // Ensure that the hidden required field has a value to avoid form
  // invalidation. (We ignore the value.)
  $('[name=_qf_Action_submit]').on( "click", function(){
    if($('select[name=answer_pattern_type]').val() === 'anything'){
      $('input[name=answer_pattern_raw]').val('/.*/');
    }
  });

  $(document.body).on( "change", '[name=action_type]', controlActionData);

  function controlActionData(){
    action_type_class = 'action_type_' + $('[name=action_type]').val();
    $('tr.action_type').hide();
    $('tr.action_type.' + action_type_class).show();
  }
  //   $('tr.action_type.).show();
  //   if( a === 'anything' ){
  //     $('tr.x').hide();
  //   }else{
  //     $('tr.x').show();
  //   }
  // }

});
