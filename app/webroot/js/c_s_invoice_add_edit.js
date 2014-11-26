$(function() {
	$("#CSInvoiceDueDate").datepicker({
		changeMonth: false,
		numberOfMonths: 1
	});
	
	$('#CSInvoiceLanguageId').change(function() {
		languageId = $(this).val();
	});
	
	var modelName = 'CSInvoice';
});