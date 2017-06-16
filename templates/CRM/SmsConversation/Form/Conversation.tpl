<div class="crm-form-block">
  <table class="form-layout">
    <tr>
      <td>{$form.name.label}</td>
      <td>{$form.name.html}</td>
    </tr>
    {if $action eq 1}
    <tr>
      <td>{$form.start_question_text.label}</td>
      <td>{$form.start_question_text.html}</td>
    </tr>
    {elseif $action eq 2}
    <tr>
      <td>{$form.start_question_id.label}</td>
      <td>{$form.start_question_id.html}</td>
    </tr>
    {/if}

  </table>
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
