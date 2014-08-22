	$(function() {
		var rowCount = 1; 

		$("#BPRepSaleDueDate").datepicker({
			changeMonth: false,
			numberOfMonths: 1
		});

		$("#BPRepSaleDateOfIssue").datepicker({
			changeMonth: false,
			numberOfMonths: 1
		});

		$('#BPRepSaleRepName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/reps/autocomplete_list',
			select: function(event, ui) {
				$('#BPRepSaleRepName').val(ui.item.label);
				$('#BPRepSaleRepId').val(ui.item.value);
				return false;
			}
		});

		$('#BPRepSaleBusinessPartnerName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/business_partners/autocomplete_list',
			select: function(event, ui) {
				$('#BPRepSaleBusinessPartnerName').val(ui.item.label);
				$('#BPRepSaleBusinessPartnerId').val(ui.item.value);
				return false;
			}
		});

		$('table').delegate('.BPRepTransactionItemProductName', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/user/product_variants/autocomplete_list',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#BPRepTransactionItem' + count + 'ProductVariantId').val(ui.item.value);
					$('#BPRepTransactionItem' + count + 'ProductName').val(ui.item.name);
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
		rowData += '<input name="data[BPRepTransactionItem][' + count + '][product_name]" type="text" class="BPRepTransactionItemProductName" size="50" id="BPRepTransactionItem' + count + 'ProductName" />';
		rowData += '<input type="hidden" name="data[BPRepTransactionItem][' + count + '][product_variant_id]" id="BPRepTransactionItem' + count + 'ProductVariantId" />';
		rowData += '</td>';
		rowData += '<th>Množství</th>';
		rowData += '<td><input name="data[BPRepTransactionItem][' + count + '][quantity]" type="text" size="3" maxlength="10" id="BPRepTransactionItem' + count + 'Quantity" />';
		rowData += '<th><abbr title="Celková cena za položku včetně DPH">Cena</abbr></th>';
		rowData += '<td><input name="data[BPRepTransactionItem][' + count + '][price_total]" type="text" size="5" maxlength="10" id="BPRepTransactionItem' + count + 'Price" />';
		rowData += '</td>';
		rowData += '<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>';
		rowData += '</tr>';
		return rowData;
	}