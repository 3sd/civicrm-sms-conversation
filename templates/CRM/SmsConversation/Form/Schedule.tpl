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
  </table>
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>

{literal}
  <script type="text/javascript">
      CRM.$(function($) {
          // Changing relationships may affect related members and contributions. Ensure they are refreshed.
          $('#crm-main-content-wrapper').on('crmPopupFormSuccess', function() {
              CRM.tabHeader.resetTab('#tab_smsconversation',true);
          });
      });
  </script>
{/literal}
