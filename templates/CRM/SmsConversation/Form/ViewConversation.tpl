{* HEADER *}

<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="top"}
</div>

{if $action == 8} {* DELETE *}
  <div class="crm-section">
    <p>Are you sure you want to delete this conversation?</p>
  </div>
{elseif $action == 2} {* UPDATE *}
  <div class="crm-section">
    <p>Are you sure you want to cancel this conversation?</p>
  </div>
{else}

  {* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}
  <div class="help">Valid Answer = <span style="color:green">Green</span>; Invalid Answer = <span style="color:red">Red</span></div>
  <div class="crm-section">
    <table>
      <tr>
        <th></th>
        <th>Question</th>
        <th>Answer</th>
      </tr>
      {foreach from=$conversationRecord item=conv}
        <tr>
          <td>{$conv.q}</td>
          {if $conv.vPrev}
            <td>{$conv.question}</td>
          {else}
            <td style="color:red">{$conv.question}</td>
          {/if}
          {if $conv.v}
            <td style="color:green">{$conv.a}</td>
          {else}
            <td style="color:red">{$conv.a}</td>
          {/if}
        </tr>
      {/foreach}
    </table>
  </div>
{/if}
{* FOOTER *}
<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
