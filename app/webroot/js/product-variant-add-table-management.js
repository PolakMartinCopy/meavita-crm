$(function() {
	var rowCount = $('.product_row').length;
	var DEFAULT_VAT = 15;
	var vat = DEFAULT_VAT;
	
	$('table').delegate('.addRowButton', 'click', function(e) {
		e.preventDefault();
		// pridat radek s odpovidajicim indexem na konec tabulky s addRowButton
		var tableRow = $(this).closest('tr');
		tableRow.after(productRow(rowCount));
		// zvysim pocitadlo radku
		rowCount++;
		// globalni dph nastavim na zakladni sazbu
		vat = DEFAULT_VAT;
	});

	$('table').delegate('.removeRowButton', 'click', function(e) {
		e.preventDefault();
		var tableRow = $(this).closest('tr');
		tableRow.remove();
	});
	
	$('table').delegate('.price', 'blur', function(e) {
		e.preventDefault();
		var price = $(this).val();
		var price_vat = Math.round(100 * parseFloat(price) + Math.round(parseFloat(price) * parseFloat(vat))) / 100;
		if (!isNaN(price_vat)) {
			var price_id = $(this).attr('id');
			var price_vat_id = price_id + 'Vat';
			$('#' + price_vat_id).val(price_vat);
		}
	});
});

function productRow(count) {
	count++;
	var rowData = '<tr rel="' + count + '" class="product_row">';
	rowData += '<td width="52%">';
	rowData += '<a href="#" id="ProductVariant' + count + 'SelectShow" class="ProductVariantSelectShow" data-row-number="' + count + '">vybrat</a>';
//	rowData += '<input name="data[CSTransactionItem][' + count + '][product_name]" type="text" class="CSTransactionItemProductName" size="70" id="CSTransactionItem' + count + 'ProductName" />';
	rowData += '<input type="hidden" name="data[CSTransactionItem][' + count + '][product_variant_id]" id="CSTransactionItem' + count + 'ProductVariantId" />';
	rowData += '</td>';
	rowData += '<td style="width:5%">&nbsp;</td>';
	rowData += '<td style="width:5%">&nbsp;</td>';
	rowData += '<td style="width:5%">&nbsp;</td>';
	rowData += '<td style="width:5%">&nbsp;</td>';
	rowData += '<td style="width:5%">&nbsp;</td>';
	rowData += '<td style="width:5%"><input name="data[CSTransactionItem][' + count + '][quantity]" type="text" size="5" maxlength="11" id="CSTransactionItem' + count + 'Quantity" /></td>';
	rowData += '<td style="width:12%"><input name="data[CSTransactionItem][' + count + '][price]" type="text" size="20" maxlength="11" id="CSTransactionItem' + count + 'Price" class="price"/></td>';
	rowData += '<td style="width:6%"><a href="#" class="addRowButton"></a>&nbsp;<a href="#" class="removeRowButton"></a></td>';
	rowData += '</tr>';
	return rowData;
}
