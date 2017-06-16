<div class="crm-form-block">
  <table class="form-layout">
    <tr>
      <td>{$form.text.label}</td>
      <td>{$form.text.html}</td>
    </tr>
    <tr>
      <td>{$form.text_invalid.label} {help id="textinvalid" file="CRM/SmsConversation/Form/TextInvalid.hlp"}</td>
      <td>{$form.text_invalid.html}</td>
    </tr>

  </table>
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
