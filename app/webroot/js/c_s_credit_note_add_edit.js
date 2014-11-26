$(function() {
	$("#CSCreditNoteDueDate").datepicker({
		changeMonth: false,
		numberOfMonths: 1
	});
	
	$('#CSCreditNoteLanguageId').change(function() {
		languageId = $(this).val();
	});
	
	var modelName = 'CSCreditNote';
});