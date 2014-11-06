<h1>Nový příspěvek na nástěnku</h1>
<?php echo $this->Form->create('BlackboardNote', array('type' => 'file'))?>
<table class="left_heading">
	<tr>
		<th>Text</th>
		<td><?php echo $this->Form->input('BlackboardNote.text', array('label' => false, 'cols' => 70, 'rows' => 10))?></td>
	</tr>
	<tr>
		<th>Dokumenty</th>
		<td>
			Zobrazit <?php echo $this->Form->input('BlackboardNote.documents_count', array('label' => false, 'div' => false, 'size' => 1))?> polí <?php echo $this->Form->submit($show_str, array('div' => false, 'name' => 'data[BlackboardNote][action]'))?><br/>
			<?php for ($i=1; $i <= $this->data['BlackboardNote']['documents_count']; $i++) { ?>
			Dokument <?php echo $i?>: <?php echo $this->Form->input('BlackboardNoteDocument.' . $i . '.name', array('label' => false, 'type' => 'file', 'div' => false))?><br/>
			<?php } ?>
		</td>
	</tr>
</table>

<?php echo $this->Form->hidden('BlackboardNote.user_id')?>
<?php echo $this->Form->submit($send_str, array('name' => 'data[BlackboardNote][action]'))?>
<?php echo $this->Form->end()?>