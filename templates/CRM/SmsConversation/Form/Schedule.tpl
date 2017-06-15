<div class="crm-form-block">
  <div>
    <span>{$form.conversation_id.label}</span>
    <span>{$form.conversation_id.html}</span>
  </div>
  <div>
    <span>{$form.scheduled_date.label}</span>
    <span>{include file="CRM/common/jcalendar.tpl" elementName=scheduled_date}</span>
  </div>


  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
