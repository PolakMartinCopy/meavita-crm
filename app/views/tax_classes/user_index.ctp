<h1>Daňové třídy</h1>
<ul>
	<li><?php
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/TaxClasses/user_add')) { 
		echo $html->link('Přidat daňovou třídu', array('controller' => 'tax_classes', 'action' => 'add'));
	}		
	?></li>
</ul>
<?php if (empty($tax_classes)) { ?>
<p><em>V databázi nejsou žádné daňové třídy.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>Hodnota</th>
		<th>&nbsp;</th>
	</tr>
<?php 
	$odd = '';
	foreach ($tax_classes as $tax_class) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $tax_class['TaxClass']['id']?></td>
		<td><?php echo $tax_class['TaxClass']['name']?></td>
		<td><?php echo $tax_class['TaxClass']['value']?></td>
		<td class="actions"><?php 
			$links = array();
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/TaxClasses/user_edit')) {
				$links[] = $html->link('Upravit', array('controller' => 'tax_classes', 'action' => 'edit', $tax_class['TaxClass']['id']));
			}
			if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/TaxClasses/user_delete')) {
				$links[] = $html->link('Smazat', array('controller' => 'tax_classes', 'action' => 'delete', $tax_class['TaxClass']['id']), null, 'Opravdu si přejete daňovou třídu ' . $tax_class['TaxClass']['name'] . ' smazat?');
			}
			echo implode(' | ', $links);
		?>
		</td>
	</tr>
<?php } // end foreach?>
</table>
<?php } // end if?>
<ul>
	<li><?php
	if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/TaxClasses/user_add')) { 
		echo $html->link('Přidat daňovou třídu', array('controller' => 'tax_classes', 'action' => 'add'));
	}		
	?></li>
</ul>