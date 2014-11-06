<h1>Nástěnka</h1>
<?php if (empty($notes)) { ?>
<p><em>Nástěnka je prázdná.</em></p>
<?php } else { ?>
<table class="top_heading">
	<thead>
		<tr>
			<th style="width:70%">Text</th>
			<th>Dokumenty</th>
			<th style="width:5%">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$odd = true;
		foreach ($notes as $note) { ?>
		<tr<?php echo ($odd ? ' class="odd"' : '')?>>
			<td><?php
				$user_name = $note['User']['first_name'];
				if (!empty($user_name)) {
					$user_name .= ' ';
				}
				$user_name .= $note['User']['last_name'];
				$date = czech_date($note['BlackboardNote']['created']);
			
				echo str_replace("\n", '<br/>', $note['BlackboardNote']['text']);
			?>
				<br/>
				<em>Vložil <?php echo $user_name?> dne <?php echo $date?></em> 
			</td>
			<td><?php if (empty($note['BlackboardNoteDocument'])) { ?>
				&nbsp;
				<?php } else { ?>
				<ul>
					<?php foreach ($note['BlackboardNoteDocument'] as $document) { ?>
					<li><?php 
						$file_name = str_replace($document_folder, '', $document['name']);
						echo $this->Html->link($file_name, '/' . $document['name']);
					?></li>
					<?php } ?>
				</ul>
				<?php } ?>
			</td>
			<td nowrap><?php
				$links = array();
				if (
					// pokud ma uzivatel pravo
					isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BlackboardNotes/user_edit')
				) {
					$links[] = $this->Html->link('Upravit', array('controller' => 'blackboard_notes', 'action' => 'edit', $note['BlackboardNote']['id']));
				}
				if (
					// pokud ma uzivatel pravo
					isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BlackboardNotes/user_delete')
				) {
					$links[] = $this->Html->link('Smazat', array('controller' => 'blackboard_notes', 'action' => 'delete', $note['BlackboardNote']['id']), null, 'Opravdu chcete příspěvek smazat?');
				}
				echo implode(' | ', $links);
				$odd = !$odd;
			?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } ?>