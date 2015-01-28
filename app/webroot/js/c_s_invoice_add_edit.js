	var fieldId = 'CSInvoicePaymentType';
	var tmpFieldId = fieldId + 'Tmp';
	var csPaymentTypes = {};

$(function() {
	$.ajax({
		url: '/c_s_invoices/ajax_cs_payment_types',
		dataType: 'json',
		async: false,
		success: function(data) {
			// pole zmenim na select, kde options jsou tyto stazene moznosti platby
			csPaymentTypes = data;
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		},
	});
	
	// pokud vykresluju na zacatku obrazovku pro ceskou fakturu
	if ($('#CSInvoiceLanguageId').val() == 1) {
		var value = $('#' + fieldId).val();
		// natahnu pole s moznostma platby do selectu
		switchPaymentType2Cs(value);
	}
	
	$("#CSInvoiceDueDate,#CSInvoiceTaxableFillingDate,#CSInvoiceDateOfIssue").datepicker({
		changeMonth: false,
		numberOfMonths: 1
	});
	
	$('#CSInvoiceLanguageId').change(function() {
		languageId = $(this).val();
		if (languageId == 1) {
			// natahnu pole s moznostma platby do selectu
			switchPaymentType2Cs(0);
		} else if (languageId == 2) {
			// zmenim pole na textove
			switchPaymentType2En();
		}
	});
	
	window.modelName = 'CSInvoice';
});

function switchPaymentType2Cs(value) {
	var combo = $('<select></select>').attr('id', tmpFieldId).attr('name', 'data[CSInvoice][payment_type]');
	$.each(csPaymentTypes, function (i, el) {
	    combo.append('<option value=' + i + '>' + el + '</option>');
	});
	$('#' + fieldId).after(combo);
	$('#' + fieldId).remove();
	$('#' + tmpFieldId).attr('id', fieldId);

	if (value != undefined) {
		value = 0;
	}
	$('#' + fieldId).val(value);
	
	return false;
}

function switchPaymentType2En() {
	var input = '<input name="data[CSInvoice][payment_type]" maxlength="50" id="' + tmpFieldId + '" type="text" />';
	$('#' + fieldId).after(input);
	$('#' + fieldId).remove();
	$('#' + tmpFieldId).attr('id', fieldId);
	
	return false;
}