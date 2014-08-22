	$(function() {
		var rowCount = 1; 

		$("#BPCSRepSaleDueDate").datepicker({
			changeMonth: false,
			numberOfMonths: 1
		});

		$("#BPCSRepSaleDateOfIssue").datepicker({
			changeMonth: false,
			numberOfMonths: 1
		});

		$('#BPCSRepSaleCSRepName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/c_s_reps/autocomplete_list',
			select: function(event, ui) {
				$('#BPCSRepSaleCSRepName').val(ui.item.label);
				$('#BPCSRepSaleCSRepId').val(ui.item.value);
				return false;
			}
		});

		$('#BPCSRepSaleBusinessPartnerName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/business_partners/autocomplete_list',
			select: function(event, ui) {
				$('#BPCSRepSaleBusinessPartnerName').val(ui.item.label);
				$('#BPCSRepSaleBusinessPartnerId').val(ui.item.value);
				return false;
			}
		});

		$('table').delegate('.BPCSRepTransactionItemProductName', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/user/product_variants/autocomplete_list',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#BPCSRepTransactionItem' + count + 'ProductVariantId').val(ui.item.value);
					$('#BPCSRepTransactionItem' + count + 'ProductName').val(ui.item.name);
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
		rowData += '<input name="data[BPCSRepTransactionItem][' + count + '][product_name]" type="text" class="BPCSRepTransactionItemProductName" size="50" id="BPCSRepTransactionItem' + count + 'ProductName" />';
		rowData += '<input type="hidden" name="data[BPCSRepTransactionItem][' + count + '][product_variant_id]" id="BPCSRepTransactionItem' + count + 'ProductVariantId" />';
		rowData += '</td>';
		rowData += '<th>Množství</th>';
		rowData += '<td><input name="data[BPCSRepTransactionItem][' + count + '][quantity]" type="text" size="3" maxlength="10" id="BPCSRepTransactionItem' + count + 'Quantity" />';
		rowData += '<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>';
		rowData += '<td><input name="data[BPCSRepTransactionItem][' + count + '][price_total]" type="text" size="5" maxlength="10" id="BPCSRepTransactionItem' + count + 'Price" />';
		rowData += '</td>';
		rowData += '<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>';
		rowData += '</tr>';
		return rowData;
	}