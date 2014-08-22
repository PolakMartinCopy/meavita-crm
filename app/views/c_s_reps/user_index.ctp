<h1>Seznam repů</h1>
<button id="search_form_show">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSRepForm']) ){
		$hide = '';
	}
?>
<div id="search_form"<?php echo $hide?>>

	<?php echo $form->create('CSRep', array('url' => array('controller' => 'c_s_reps', 'action' => 'index')))?>
	<table class="left_heading">
		<tr>
			<th>Křestní jméno</th>
			<td><?php echo $form->input('CSRepForm.CSRep.first_name', array('label' => false))?></td>
			<th>Příjmení</th>
			<td><?php echo $form->input('CSRepForm.CSRep.last_name', array('label' => false))?></td>
			<th>Login</th>
			<td><?php echo $form->input('CSRepForm.CSRep.login', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Telefon</th>
			<td><?php echo $form->input('CSRepForm.CSRep.phone', array('label' => false))?></td>
			<th>Email</th>
			<td><?php echo $form->input('CSRepForm.CSRep.email', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6">
				<?php
					echo $html->link('reset filtru', array('controller' => 'c_s_reps', 'action' => 'index', 'reset' => 'c_s_reps'))
				?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('CSRepForm.CSRep.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$("#search_form_show").click(function () {
		if ($('#search_form').css('display') == "none"){
			$("#search_form").show("slow");
		} else {
			$("#search_form").hide("slow");
		}
	});
</script>

<?php 
echo $form->create('CSV', array('url' => array('controller' => 'c_s_reps', 'action' => 'xls_export')));
echo $form->hidden('data', array('value' => serialize($find)));
echo $form->hidden('fields', array('value' => serialize($export_fields)));
echo $form->submit('CSV');
echo $form->end();

if (empty($c_s_reps)) {
?>
<p><em>V databázi nejsou žádní uživatelé.</em></p>
<?php } else {?>
<table class="top_heading">
	<tr>
		<th><?php echo $paginator->sort('ID', 'CSRep.id')?></th>
		<th><?php echo $paginator->sort('Křestní jméno', 'CSRep.first_name')?></th>
		<th><?php echo $paginator->sort('Příjmení', 'CSRep.last_name')?></th>
		<th><?php echo $paginator->sort('Telefon', 'CSRep.phone')?></th>
		<th><?php echo $paginator->sort('Email', 'CSRep.email')?></th>
		<th><?php echo $paginator->sort('Login', 'CSRep.login')?></th>
		<th><?php echo $paginator->sort('Peněženka', 'CSRep.wallet')?>
		<th><?php echo $this->Paginator->sort('Poslední prodej', 'CSRepAttribute.last_sale')?></th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($c_s_reps as $rep) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $rep['CSRep']['id']?></td>
		<td><?php echo $rep['CSRep']['first_name']?></td>
		<td><?php echo $this->Html->link($rep['CSRep']['last_name'], array('controller' => 'c_s_reps', 'action' => 'view', $rep['CSRep']['id']))?></td>
		<td><?php echo $rep['CSRep']['phone']?></td>
		<td><?php echo $html->link($rep['CSRep']['email'], 'mailto:' . $rep['CSRep']['email'])?></td>
		<td><?php echo $rep['CSRep']['login']?></td>
		<td><?php echo $rep['CSRep']['wallet']?></td>
		<td><?php echo czech_date($rep['CSRepAttribute']['last_sale'])?></td>
		<td class="actions"><?php
			$links = array();
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSReps/user_view')) {
				$links[] = $this->Html->link('Detail', array('controller' => 'c_s_reps', 'action' => 'view', $rep['CSRep']['id']));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSReps/user_edit')) {
				$links[] = $html->link('Upravit', array('controller' => 'c_s_reps', 'action' => 'edit', $rep['CSRep']['id']));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSReps/user_delete')) {
				$links[] = $html->link('Smazat', array('controller' => 'c_s_reps', 'action' => 'delete', $rep['CSRep']['id']), null, 'Opravdu chcete uživatele ' . $rep['CSRep']['first_name'] . ' ' . $rep['CSRep']['last_name'] . ' smazat?');
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSWalletTransactions/user_add')) {
				$links[] = $this->Html->link('Přidat převod', array('controller' => 'c_s_wallet_transactions', 'action' => 'add', 'c_s_rep_id' => $rep['CSRep']['id']));
			}
			echo implode(' | ', $links);
		?></td>
	</tr>
<?php } ?>
</table>

<?php 
echo $paginator->numbers();
echo $paginator->prev('« Předchozí ', null, null, array('class' => 'disabled'));
echo $paginator->next(' Další »', null, null, array('class' => 'disabled'));

} ?>