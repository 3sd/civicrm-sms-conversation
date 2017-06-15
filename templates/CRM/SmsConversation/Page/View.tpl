
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
        <a href="{crmURL p='civicrm/sms/conversation/question/edit' q="id=`$question.id`"}" title="edit question"><i class="crm-i fa-pencil"></i></a>
        </p>
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
              <a href="{crmURL p='civicrm/sms/conversation/action/edit' q="id=`$action.id`"}" title="add action"><i class="crm-i fa-pencil"></i></a>
            </p>
          {/foreach}
      {/if}
      <a href="{crmURL p='civicrm/sms/conversation/action/add' q="question_id=`$question.id`"}" title="add action"><i class="crm-i fa-plus-circle"></i></a>
      </td>
    </tr>
  {/foreach}
  <tr>
    <td colspan=2>
      <a href="{crmURL p='civicrm/sms/conversation/question/add' q="conversation_id=`$question.id`"}" title="add question"><i class="crm-i fa-plus-circle"></i> question</a> |
      <a href="{crmURL p='civicrm/sms/conversation/edit' q="id=`$conversation.id`"}" title="edit conversation"><i class="crm-i fa-pencil"></i> conversation</a>
    </td>
  </tr>
</table>
