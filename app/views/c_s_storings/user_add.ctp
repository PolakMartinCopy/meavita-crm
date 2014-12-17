<script type="text/javascript">
	// musim si nastavit globalni promennou, abych mohl ve skriptu pri pridavani radku generovat select pro vyber meny
	var currencies = <?php echo json_encode($currencies)?>;
</script>
<script type="text/javascript" src="/js/c_s_storing_add_edit.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		// pri zmene promenne z KC na EUR a opacne menit kurz v polich
		$(document).delegate('.CSTransactionItemCurrency', 'change', function(e) {
			var id = $(this).attr('id');
			var val = $('#' + id + ' option:selected').val();
			var exchangeRateFieldId = id;
			var exchangeRate = <?php echo $exchange_rate?>;
			exchangeRateFieldId = exchangeRateFieldId.replace('CurrencyId', 'ExchangeRate');
			// kurz nastavim defaultne na 1
			$('#' + exchangeRateFieldId).val(1);

			// pokud je zvoleno EUR (val == 2) a mam aktualni kurz, nastavim ho
			if (val == 2 && exchangeRate) {
				$('#' + exchangeRateFieldId).val(exchangeRate);
			}
		});
	});
</script>

<h1>Naskladnit zboží</h1>
<?php
	echo $this->Form->create('CSStoring');
	echo $this->element('c_s_storings/add_edit_form');
	echo $this->Form->hidden('CSStoring.user_id', array('value' => $user['User']['id']));
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>