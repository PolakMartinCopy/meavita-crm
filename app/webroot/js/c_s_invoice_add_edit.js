$(function() {
	$("#CSInvoiceDueDate,#CSInvoiceTaxableFillingDate").datepicker({
		changeMonth: false,
		numberOfMonths: 1
	});
	
	$('#CSInvoiceLanguageId').change(function() {
		languageId = $(this).val();
	});
	
	window.modelName = 'CSInvoice';
});