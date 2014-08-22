<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSCreditNoteForm']) ){
		$hide = '';
	}
?>
<div id="search_form_c_s_credit_notes"<?php echo $hide?>>
	<?php echo $form->create('CSCreditNote', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Odběratel</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $form->input('CSCreditNoteForm.BusinessPartner.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $form->input('CSCreditNoteForm.BusinessPartner.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $form->input('CSCreditNoteForm.BusinessPartner.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.Address.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.Address.city', array('label' => false))?></td>
			<th>Okres</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.Address.region', array('label' => false))?></td>
		</tr>
		<tr>
			<td>Dobropis</td>
		</tr>
		<tr>
			<th>Číslo dokladu</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.CSCreditNote.code', array('label' => false))?></td>
			<th>Jazyk</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.CSCreditNote.language_id', array('label' => false, 'empty' => true))?></td>
			<th>Měna</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.CSCreditNote.currency_id', array('label' => false, 'empty' => true))?></td>
		</tr>
		</tr>
			<th>Obchodník</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.CSCreditNote.user_id', array('label' => false, 'empty' => true))?></td>
			<th>Datum vystavení od</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.CSCreditNote.date_of_issue_from', array('label' => false))?></td>
			<th>Datum vystavení do</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.CSCreditNote.date_of_issue_to', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Datum splatnosti od</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.CSCreditNote.due_date_from', array('label' => false))?></td>
			<th>Datum splatnosti do</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.CSCreditNote.due_date_to', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td>Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.Product.name', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('CSCreditNoteForm.Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6"><?php
				$reset_url = $url + array('reset' => 'c_s_credit_notes');
				echo $html->link('reset filtru', $reset_url);
			?></td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('CSCreditNoteForm.CSCreditNote.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$(function() {
		var model = 'CSCreditNoteFormCSCreditNote';
		var dateOfIssueFromId = model + 'DateOfIssueFrom';
		var dateOfIssueToId = model + 'DateOfIssueTo';
		var datesOfIssue = $('#' + dateOfIssueFromId + ',#' + dateOfIssueToId).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == dateOfIssueFromId ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				datesOfIssue.not( this ).datepicker( "option", option, date );
			}
		});
		
		var dueDateFromId = model + 'DueDateFrom';
		var dueDateToId = model + 'DueDateTo';
		var dueDates = $('#' + dueDateFromId + ',#' + dueDateToId).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == dueDateFromId ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dueDates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
</script>