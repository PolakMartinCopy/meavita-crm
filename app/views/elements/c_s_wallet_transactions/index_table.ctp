<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'CSWalletTransaction.created')?></th>
		<th><?php echo $this->Paginator->sort('Jméno', 'CSRep.first_name')?></th>
		<th><?php echo $this->Paginator->sort('Příjmení', 'CSRep.last_name')?></th>
		<th><?php echo $this->Paginator->sort('Částka', 'CSWalletTransaction.amount')?></th>
		<th><?php echo $this->Paginator->sort('Stav po transakci', 'CSWalletTransaction.amount_after')?></th>
		<th><?php echo $this->Paginator->sort('Vložil', 'User.last_name')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($c_s_wallet_transactions as $c_s_wallet_transaction) { ?>
	<tr>
		<td><?php echo czech_date($c_s_wallet_transaction['CSWalletTransaction']['created'])?></td>
		<td><?php echo $c_s_wallet_transaction['CSRep']['first_name']?></td>
		<td><?php echo $this->Html->link($c_s_wallet_transaction['CSRep']['last_name'], array('controller' => 'c_s_reps', 'action' => 'view', $c_s_wallet_transaction['CSRep']['id']))?></td>
		<td><?php echo $c_s_wallet_transaction['CSWalletTransaction']['amount']?></td>
		<td><?php echo $c_s_wallet_transaction['CSWalletTransaction']['amount_after']?></td>
		<td><?php echo $c_s_wallet_transaction['User']['last_name']?></td>
		<td><?php
			if ($c_s_wallet_transaction['CSWalletTransaction']['year']) {
				$anchor = 'Příjmový doklad';
				if ((float)$c_s_wallet_transaction['CSWalletTransaction']['amount'] > 0) {
					$anchor = 'Výdajový doklad';
				}
				echo $this->Html->link($anchor, array('controller' => 'c_s_wallet_transactions', 'action' => 'cash_receipt', $c_s_wallet_transaction['CSWalletTransaction']['id']));
			}
		?></td>
	</tr>
	<?php } ?>
</table>
<?php
	echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled'));
	echo $this->Paginator->numbers();
	echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled'));
?>