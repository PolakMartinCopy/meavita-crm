	$(function() {
		var rowCount = 1; 

		$('#MCRepPurchaseRepName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/reps/autocomplete_list',
			select: function(event, ui) {
				$('#MCRepPurchaseRepName').val(ui.item.label);
				$('#MCRepPurchaseRepId').val(ui.item.value);
				return false;
			}
		});

		$('table').delegate('.MCRepTransactionItemProductName', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/user/products/autocomplete_list',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#MCRepTransactionItem' + count + 'ProductId').val(ui.item.value);
					$('#MCRepTransactionItem' + count + 'ProductName').val(ui.item.name);
					return false;
				}
			});
		});
		
		$('table').delegate('.addRowButton', 'click', function(e) {
			e.preventDefault();
			// pridat radek s odpovidajicim indexem na konec tabulky s addRowButton
			var tableRow = $(this).closest('tr');
			tableRow.after(productRow(rowCount));
			// zvysim pocitadlo radku
			rowCount++;
		});

		$('table').delegate('.removeRowButton', 'click', function(e) {
			e.preventDefault();
			var tableRow = $(this).closest('tr');
			tableRow.remove();
		});
	});

	function productRow(count) {
		count++;
		var rowData = '<tr rel="' + count + '">';
		rowData += '<th>Zboží</th>';
		rowData += '<td>';
		rowData += '<input name="data[MCRepTransactionItem][' + count + '][product_name]" type="text" class="MCRepTransactionItemProductName" size="50" id="MCRepTransactionItem' + count + 'ProductName" />';
		rowData += '<input type="hidden" name="data[MCRepTransactionItem][' + count + '][product_variant_id]" id="MCRepTransactionItem' + count + 'ProductId" />';
		rowData += '</td>';
		rowData += '<th>LOT</th>';
		rowData += '<td><input name="data[MCRepTransactionItem][' + count + '][product_variant_lot]" type="text" size="7" id="MCRepTransactionItem' + count + 'Lot"></td>';
		rowData += '<th>EXP</th>';
		rowData += '<td><input name="data[MCRepTransactionItem][' + count + '][product_variant_exp]" type="text" size="7" id="MCRepTransactionItem' + count + 'Exp"></td>';
		rowData += '<th>Množství</th>';
		rowData += '<td><input name="data[MCRepTransactionItem][' + count + '][quantity]" type="text" size="3" maxlength="10" id="MCRepTransactionItem' + count + 'Quantity" />';
		rowData += '<th>Cena</th>';
		rowData += '<td><input name="data[MCRepTransactionItem][' + count + '][price]" type="text" size="3" maxlength="10" id="MCRepTransactionItem' + count + 'Price" />';
		rowData += '</td>';
		rowData += '<td><a href="#" class="addRowButton"></a>&nbsp;<a href="#" class="removeRowButton"></a></td>';
		rowData += '</tr>';
		return rowData;
	}