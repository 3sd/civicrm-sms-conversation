{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2017                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
<div id="sms-conversations-tab" class="view-content">
<div class="action-link">
  <a accesskey="N" href='{crmURL p="civicrm/sms/conversation/schedule" q="cid=`$contactId`"}' class="button medium-popup"><span><i class="crm-i fa-comment"></i> Schedule Conversation</span></a>
</div>

<table class="crm-smsconversation-selector crm-ajax-table" data-order='[[2,"desc"]]'>
  <thead>
  <tr>
    <th data-data="conversation_name" cell-class="crm-smsconversation-conversation_name crmf-title" class='crm-smsconversation-conversation_name'>{ts}Conversation{/ts}</th>
    <th data-data="source_contact" cell-class="crm-smsconversation-source_contact right" class='crm-smsconversation-source_contact'>{ts}Started By{/ts}</th>
    <th data-data="date" cell-class="crm-smsconversation-date" class='crm-smsconversation-date'>{ts}Date{/ts}</th>
    <th data-data="current_question_id" cell-class="crm-smsconversation-current_question_id" class='crm-smsconversation-current_question_id'>{ts}Current Question{/ts}</th>
    <th data-data="status" cell-class="crm-smsconversation-status" class='crm-smsconversation-status'>{ts}Status{/ts}</th>
    <th data-data="links" data-orderable="false" cell-class="crm-smsconversation-smsconversation_links" class='crm-smsconversation-smsconversation_links'>&nbsp;</th>
  </tr>
  </thead>
</table>
</div>

{literal}
<script type="text/javascript">
    (function($) {
        var ZeroRecordText = {/literal}'{ts escape="js"}<div class="status messages">No SMS Conversations for this contact.{/ts}</div>'{literal};
        $('table.crm-smsconversation-selector').data({
            "ajax": {
                "url": {/literal}'{crmURL p="civicrm/ajax/smsconversationlist" h=0 q="snippet=4&cid=`$contactId`"}'{literal},
                "data": function (d) {
                }
            },
            "language": {
                "zeroRecords": ZeroRecordText,
                "emptyTable": ZeroRecordText
            },
            "drawCallback": function(settings) {
                //Add data attributes to cells
                $('thead th', settings.nTable).each( function( index ) {
                    $.each(this.attributes, function() {
                        if(this.name.match("^cell-")) {
                            var cellAttr = this.name.substring(5);
                            var cellValue = this.value;
                            $('tbody tr', settings.nTable).each( function() {
                                $('td:eq('+ index +')', this).attr( cellAttr, cellValue );
                            });
                        }
                    });
                });
                //Reload table after draw
                $(settings.nTable).trigger('crmLoad');
            }
        });
        $('#crm-container')
            .on('click', 'a.button, a.action-item[href*="action=update"], a.action-item[href*="action=delete"]', CRM.popup)
            .on('crmPopupFormSuccess', 'a.button, a.action-item[href*="action=update"], a.action-item[href*="action=delete"]', function() {
                // Refresh datatable when form completes
                $('table.crm-smsconversation-selector').DataTable().draw();
            });
    })(CRM.$);
</script>
{/literal}

{literal}
  <script type="text/javascript">
      CRM.$(function($) {
          // Changing relationships may affect related members and contributions. Ensure they are refreshed.
          $('#sms-conversations-tab').on('crmPopupFormSuccess', function() {
              CRM.tabHeader.resetTab('#tab_smsconversation',true);
          });
      });
  </script>
{/literal}
