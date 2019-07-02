{l s='Ожидание перенаправления' mod='assetpayments'}

<form id="checkout" method="post" action="{$url}">
    {foreach from=$fields  key=key item=field}
        {if $field|is_array}
            {foreach from=$field  key=k item=v}<input type="hidden" name="{$key}[]" value="{$v}" />{/foreach}
        {else}
			<input type="hidden" name="{$key}" value="{$field}" />
        {/if}
    {/foreach}

	<input type="submit" value="{l s='Оплатить' mod='assetpayments'}">
</form>

<script type="text/javascript">
	$('#checkout').submit();
</script>