<script type="text/javascript">
$(function() {
	$('#CSWalletTransactionCSRepName').autocomplete({
		delay: 500,
		minLength: 2,
		source: '/user/c_s_reps/autocomplete_list',
		select: function(event, ui) {
			$('#CSWalletTransactionCSRepName').val(ui.item.label);
			$('#CSWalletTransactionCSRepId').val(ui.item.value);
			return false;
		}
	});
});
</script>

<h1>Dobít peněženku</h1>
<?php echo $this->Form->create('CSWalletTransaction', array('url' => $this->passedArgs))?>
<table class="left_heading">
	<tr>
		<th>Rep</th>
		<td><?php
			if (isset($rep)) {
				echo $this->Form->input('CSWalletTransaction.c_s_rep_name', array('label' => false, 'value' => $c_s_rep['CSRep']['name'], 'disabled' => true));
				echo $this->Form->hidden('CSWalletTransaction.c_s_rep_id', array('value' => $c_s_rep['CSRep']['id']));
			} else {
				echo $this->Form->input('CSWalletTransaction.c_s_rep_name', array('label' => false));
				echo $this->Form->error('CSWalletTransaction.c_s_rep_id');
				echo $this->Form->hidden('CSWalletTransaction.c_s_rep_id');
			}
		?></td>
	</tr>
	<tr>
		<th>Částka</th>
		<td><?php echo $this->Form->input('CSWalletTransaction.amount', array('label' => false))?></td>
	</tr>
</table>
<?php 
	echo $this->Form->hidden('CSWalletTransaction.year');
	echo $this->Form->hidden('CSWalletTransaction.month');
	echo $this->Form->hidden('CSWalletTransaction.user_id', array('value' => $user['User']['id']));
	echo $this->Form->submit('Uložit');
	echo $this->Form->end()
?>