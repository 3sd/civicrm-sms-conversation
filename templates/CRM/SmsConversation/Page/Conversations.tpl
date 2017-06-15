
{foreach from=$conversations item=conversation}
<p>
  {$conversation.name}
  <a href="{crmURL p='civicrm/sms/conversation/view' q="id=`$conversation.id`"}">view</a>
</p>
{/foreach}
<p><a href="{crmURL p='civicrm/sms/conversation/add'}">Add conversation</a>
</p>
