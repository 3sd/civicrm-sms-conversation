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
      <td>{$form.answer_pattern_raw.html}</td>
    </tr>
      <tr>
      <td>{$form.action_data.label}</td>
      <td>{$form.action_data.html}</td>
    </tr>
  </table>
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
