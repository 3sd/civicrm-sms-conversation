<div class="crm-form-block">
  <table class="form-layout">
    <tr>
      <td colspan="2"><b>Question: {$question.text}</b></td>
    </tr>
    <tr class="answer_pattern_raw">
    <tr>
      <td>{$form.answer_pattern_type.label}</td>
      <td>{$form.answer_pattern_type.html}</td>
    </tr>
    <tr class="answer_pattern_raw">
      <td>{$form.answer_pattern_raw.label}</td>
      <td>{$form.answer_pattern_raw.html}<br /> <i>
          <span class="answer-pattern-help answer-pattern-help-list">Help test for the pattern</span>
          <span class="answer-pattern-help answer-pattern-help-list-exact">Seperate each term with a comma</span>
          <span class="answer-pattern-help answer-pattern-help-list-contains">Seperate each term with a comma</span>
          <span class="answer-pattern-help answer-pattern-help-regexp">Please enter a <a href="http://php.net/manual/en/book.pcre.php">regular expression</a></span>
        </i></td>
    </tr>
    {if $smsActionType.name eq 'question'}
    <tr>
      <td>{$form.next_question_id.label}</td>
      <td>{$form.next_question_id.html}</td>
    </tr>
    <tr>
      <td>{$form.weight.label} {help id="weight" file="CRM/SmsConversation/Form/Weight.hlp"}</td>
      <td>{$form.weight.html}</td>
    </tr>
    {else}
    <tr>
      <td>{$form.action_data.label}</td>
      <td>{$form.action_data.html}</td>
    </tr>
    {/if}

  </table>
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
