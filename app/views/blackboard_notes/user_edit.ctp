<h1>Upravit příspěvek</h1>
<?php echo $this->Form->create('BlackboardNote', array('type' => 'file'))?>
<table class="left_heading">
	<tr>
		<th>Text</th>
		<td><?php echo $this->Form->input('BlackboardNote.text', array('label' => false, 'cols' => 70, 'rows' => 10))?></td>
	</tr>
	<tr>
		<th>Dokumenty</th>
		<td>
			<?php if (!empty($note['BlackboardNoteDocument'])) { ?> 
			<ul>
				<?php foreach ($note['BlackboardNoteDocument'] as $index => $document) { ?>
				<li><?php echo str_replace($document_folder, '', $document['name'])?> - <?php echo $this->Html->link('Smazat', '#', array('class' => 'deleteDocumentLink', 'data-item-id' => $document['id']))?></li>
				<?php } ?>
			</ul>
			<?php } ?>
		
			<br/>
			Zobrazit <?php echo $this->Form->input('BlackboardNote.documents_count', array('label' => false, 'div' => false, 'size' => 1))?> polí <?php echo $this->Form->submit($show_str, array('div' => false, 'name' => 'data[BlackboardNote][action]'))?><br/>
			<?php for ($i=1; $i <= $this->data['BlackboardNote']['documents_count']; $i++) { ?>
			Dokument <?php echo $i?>: <?php echo $this->Form->input('BlackboardNoteDocument.' . $i . '.name', array('label' => false, 'type' => 'file', 'div' => false))?><br/>
			<?php } ?>
		</td>
	</tr>
</table>

<?php echo $this->Form->hidden('BlackboardNote.id')?>
<?php echo $this->Form->submit($send_str, array('name' => 'data[BlackboardNote][action]'))?>
<?php echo $this->Form->end()?>

<script type="text/javascript">
	$(document).ready(function() {
		$('.deleteDocumentLink').click(function(e) {
			var link = this;
			var documentId = $(this).attr('data-item-id');
//			console.log($(this).closest('li'));
			
			$.ajax({
				url: '/blackboard_note_documents/delete/' + documentId + '/1',
				dataType: 'json',
				success: function(data) {
					if (data.success) {
						$(link).closest('li').remove();
					} else {
						alert(data.message);
					}
				},
				error: function() {

				}
			});
			
		});
	});
</script>