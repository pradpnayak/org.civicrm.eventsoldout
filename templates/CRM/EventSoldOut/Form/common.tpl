{if $eventsoldout}
<table class="eventsoldout-block">
  <tr class="crm-event-manage-fee-form-block-{$eventsoldout}">
    <td scope="row" class="label" width="20%">{$form.$eventsoldout.label}</td>
    <td>{$form.$eventsoldout.html}</td>
  </tr>
</table>
{literal}
<script type="text/javascript">
  CRM.$(function($) {
    $('tr.crm-event-manage-fee-form-block-fee_label')
      .after($('table.eventsoldout-block tr'));
  });
</script>
{/literal}
{/if}
