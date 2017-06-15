<div class="crm-form-block">
  <table class="form-layout">
    <tr>
      <td>{$form.conversation_id.label}</td>
      <td>{$form.conversation_id.html}</td>
    </tr>
    <tr>
      <td>{$form.scheduled_date.label}</td>
      <td>{include file="CRM/common/jcalendar.tpl" elementName=scheduled_date}</td>
    </tr>
    <td colspan="2">
      SMS conversations will be scheduled with <b>{$countMobiles} contacts</b>. {if $countMobiles neq $countContacts}
      ({$countContacts} contacts were selected, but only {$countMobiles} of them have a mobile phone.)
      {/if}
    </td>
    <tr>
  </table>
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
