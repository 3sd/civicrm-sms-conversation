
<table class="layout display">
  <tr>
    <th>Question
    </th>
    <th>Match <i class="crm-i fa-arrow-right"></i> Action
    </th>
  </tr>
  {foreach from=$orderedQuestions item=question}
    <tr class="{cycle values='odd,even'}-row crm-report">
      <td>
        <p><b>{$question.number}. {$question.text}</b>
        <a href="{crmURL p='civicrm/sms/conversation/question/edit' q="action=update&id=`$question.id`"}" title="edit question"><i class="crm-i fa-pencil"></i></a>
        </p>
        {if $question.text_invalid}
        <p><i>Invalid text: "{$question.text_invalid}"</i></p>
          {/if}
      </td>
      <td>
        {if $question.actions}
          {foreach from=$question.actions item=action}
            <p>{$action.friendly_answer_pattern} <i class="crm-i fa-arrow-right"></i>
              {if $action.action_type == 1}
                {include file="CRM/SmsConversation/Page/View/Question.tpl"}
              {elseif $action.action_type == 2}
                {include file="CRM/SmsConversation/Page/View/AddGroup.tpl"}
              {elseif $action.action_type == 3}
                {include file="CRM/SmsConversation/Page/View/AddField.tpl"}
              {/if}
              <a href="{crmURL p='civicrm/sms/conversation/action/edit' q="action=update&id=`$action.id`"}" title="add action"><i class="crm-i fa-pencil"></i></a>
            </p>
          {/foreach}
      {/if}
      <!-- add http://jsfiddle.net/LxauG/606/ here for nice form controls -->
      <a href="{crmURL p='civicrm/sms/conversation/action/edit' q="action=add&action_type=3&question_id=`$question.id`"}" title="add action"><i class="crm-i fa-plus-circle"> record in field</i></a>
      <a href="{crmURL p='civicrm/sms/conversation/action/edit' q="action=add&action_type=2&question_id=`$question.id`"}" title="add action"><i class="crm-i fa-plus-circle"> add to group</i></a>
      <a href="{crmURL p='civicrm/sms/conversation/action/edit' q="action=add&action_type=1&question_id=`$question.id`"}" title="add action"><i class="crm-i fa-plus-circle"> go to question</i></a>
      </td>
    </tr>
  {/foreach}
  <tr>
    <td colspan=2>
      <a href="{crmURL p='civicrm/sms/conversation/question/add' q="action=add&conversation_id=`$question.id`"}" title="add question"><i class="crm-i fa-plus-circle"></i> question</a> |
      <a href="{crmURL p='civicrm/sms/conversation/edit' q="action=update&id=`$conversation.id`"}" title="edit conversation"><i class="crm-i fa-pencil"></i> conversation</a>
    </td>
  </tr>
</table>
{if $unusedQuestions}
<div class="messages status no-popup">
  <p><b>Note:</b> the following questions are defined for this conversation but will do not have an action that would trigger them to be asked:</p>
  <ul>
    {foreach from=$unusedQuestions item=question}
    <li>{$question.text}</li>
    {/foreach}
  </ul>
</div>
{/if}
