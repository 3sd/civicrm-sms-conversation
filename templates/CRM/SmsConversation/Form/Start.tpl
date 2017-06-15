<div>
  <span>{$form.conversation_id.label}</span>
  <span>{$form.conversation_id.html}</span>
</div>
<div>
  <span>{$form.send_at.label}</span>
  <span>{include file="CRM/common/jcalendar.tpl" elementName=send_at}</span>
</div>

<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
