{if $conversations}
<ul>
  {foreach from=$conversations item=conversation}
  <li>
    <a href="{crmURL p='civicrm/sms/conversation/view' q="id=`$conversation.id`"}"> {$conversation.name} </a>
  </li>
  {/foreach}
</ul>
{/if}
<p><a href="{crmURL p='civicrm/sms/conversation/edit' q="action=add"}">Create a new conversation</a>
</p>
