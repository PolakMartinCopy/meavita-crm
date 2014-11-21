	$(function() {
		var rowCount = $('.product_row').length;
		var DEFAULT_VAT = 15;
		var vat = DEFAULT_VAT;
		
		var productVariantListRow = null;
		
		var languageId = null;
		
		$("#CSInvoiceDueDate").datepicker({
			changeMonth: false,
			numberOfMonths: 1
		});
		
		$('#CSInvoiceLanguageId').change(function() {
			languageId = $(this).val();
		});

		$('#BusinessPartnerSelectDiv,#ProductVariantSelectDiv').dialog({
			autoOpen: false,
			resizable: false,
			width: 800,
			/*height:140, */
			modal: true,
			create: function() {
				$(this).attr('style', 'font-size:12px')
			}
		});
		
		// nastaveni jQuery UI Modal Dialog pro zobrazeni tabulky pro vyber obchodniho partner
		$( "#BusinessPartnerSelectShow" ).click(function(e) {
			e.preventDefault();
			 $( "#BusinessPartnerSelectDiv" ).dialog( "open" );
		});
		
		// nastaveni jQuery UI Modal Dialog pro zobrazeni tabulky pro vyber produktu
		$(document).delegate('.ProductVariantSelectShow', 'click', function(e) {
			e.preventDefault();
			productVariantListRow = $(this).attr('data-row-number');
			 $("#ProductVariantSelectDiv").dialog("open");
		});

		// nastaveni tabulky pro vyhledani a oznaceni obchodniho partnera
	    $('#BusinessPartnerSelectTable').DataTable({
	    	'ajax': '/user/business_partners/ajax_list',
	    	'info': false,
	    	'ordering': false,
	    	'paging': false,
	    	'language': {
	    		'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Czech.json'
	    	}
	    });
	    
		// nastaveni tabulky pro vyhledani a oznaceni obchodniho partnera
	    $('#ProductVariantSelectTable').DataTable({
	    	'ajax': '/user/product_variants/ajax_list/',
    		'fnInitComplete': function() {
    	        $('#ProductVariantSelectTable tbody tr').each(function() {
    	        	// sloupec s nazvem produktu nechci zalamovat
    	        	$(this).find('td:eq(0)').attr('nowrap', 'nowrap');
    	        	// sloupec s cenou chci zarovnat doprava
    	            $(this).find('td:eq(6)').attr('align', 'right');
    	        });
    	    },
	    	'info': false,
	    	'ordering': false,
	    	'paging': false,
	    	'language': {
	    		'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Czech.json'
	    	}
	    });
	    
	    // akce po vyberu obchodniho partnera
	    $(document).delegate('.BusinessPartnerSelectLink', 'click', function(e) {
	    	e.preventDefault();
	    	// mam id obchodniho partnera
	    	var businessPartnerId = $(this).attr('data-bp-id');
	    	// nactu nazev obchodniho partnera
	    	var businessPartnerName = $(this).attr('data-bp-name');
	    	
	    	// zavru okno pro vyhledani obchodniho partnera
	    	$('#BusinessPartnerSelectDiv').dialog('close');
	    	
	    	// odstranim elementy obsahujici info o zvolenem OP (pokud nejaky je)
	    	$('#CSInvoiceBusinessPartnerName').remove();
	    	$('#CSInvoiceBusinessPartnerId').remove();
	    	
	    	// pred odkaz pro vlozeni obchodniho partnera dam input, kam vlozim nazev OP
	    	$('#BusinessPartnerSelectShow').before('<input type="text" size="50" value="' + businessPartnerName + '" id="CSInvoiceBusinessPartnerName" name="data[CSInvoice][business_partner_name]"/>');
	    	
	    	// k tomuto inputu dam hidden field pro zapamatovani id obchodniho partnera
	    	$('#BusinessPartnerSelectShow').before('<input type="hidden" value="' + businessPartnerId + '" id="CSInvoiceBusinessPartnerId" name="data[CSInvoice][business_partner_id]"/>');
	    }); 
	    
	    // akce po vyberu
	    $(document).delegate('.ProductVariantSelectLink', 'click', function(e) {
	    	e.preventDefault();
	    	// mam id
	    	var productVariantId = $(this).attr('data-pv-id');
	    	// nactu nazev
	    	var productVariantName = $(this).attr('data-pv-name');
	    	// en nazev
	    	var productVariantEnName = $(this).attr('data-pv-en-name');
	    	// lot
	    	var productVariantLot = $(this).attr('data-pv-lot');
	    	//exp
	    	var productVariantExp = $(this).attr('data-pv-exp');
	    	// mnozstvi na sklade
	    	var productVariantQuantity = $(this).attr('data-pv-quantity');
	    	// skladova cena
	    	var productVariantPrice = $(this).attr('data-pv-price');
	    	
	    	// zavru okno pro vyhledani obchodniho partnera
	    	$('#ProductVariantSelectDiv').dialog('close');
	    	
	    	// odstranim elementy obsahujici info o zvolenem OP (pokud nejaky je)
	    	$('#CSTransactionItem' + productVariantListRow + 'ProductName').remove();
	    	$('#CSTransactionItem' + productVariantListRow + 'ProductVariantId').remove();
	    	
	    	productName = productVariantName;
	    	if (languageId == 2) {
	    		productName = productVariantEnName;
	    	}
	    	
	    	// pred odkaz pro vlozeni obchodniho partnera dam input, kam vlozim nazev
	    	$('#ProductVariant' + productVariantListRow + 'SelectShow').before('<input type="text" size="70" value="' + productName + '" id="CSTransactionItem' + productVariantListRow + 'ProductName" class="CSTransactionItemProductName" name="data[CSTransactionItem][' + productVariantListRow + '][product_name]"/>');
	    	
	    	// k tomuto inputu dam hidden field pro zapamatovani id
	    	$('#ProductVariant' + productVariantListRow + 'SelectShow').before('<input type="hidden" value="' + productVariantId + '" id="CSTransactionItem' + productVariantListRow + 'ProductVariantId" class="CSTransactionItemProductVariantId" name="data[CSTransactionItem][' + productVariantListRow + '][product_variant_id]"/>');
	    	
	    	// exp a lot do spravnych poli v radku tabulky
	    	$('.product_row').each(function() {
	    		if ($(this).attr('rel') == productVariantListRow) {
	    			$(this).find('td:eq(1)').html('<input type="hidden" value="' + productVariantLot + '" name="data[CSTransactionItem][' + productVariantListRow + '][product_variant_lot]" id="CSTransactionItem' + productVariantListRow + 'ProductVariantLot"/>' + productVariantLot);
	    			$(this).find('td:eq(2)').html('<input type="hidden" value="' + productVariantExp + '" name="data[CSTransactionItem][' + productVariantListRow + '][product_variant_exp]" id="CSTransactionItem' + productVariantListRow + 'ProductVariantExp"/>' + productVariantExp);
	    			$(this).find('td:eq(3)').html('<input type="hidden" value="' + productVariantQuantity + '" name="data[CSTransactionItem][' + productVariantListRow + '][product_variant_quantity]" id="CSTransactionItem' + productVariantListRow + 'ProductVariantQuantity"/>' + productVariantQuantity);
	    			$(this).find('td:eq(3)').attr('align', 'right');
	    			$(this).find('td:eq(4)').html('<input type="hidden" value="' + productVariantPrice + '" name="data[CSTransactionItem][' + productVariantListRow + '][product_variant_price]" id="CSTransactionItem' + productVariantListRow + 'ProductVariantPrice"/>' + productVariantPrice);
	    			$(this).find('td:eq(4)').attr('align', 'right');
	    		} 
	    	})
	    }); 
		
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
		rowData += '<td width="57%">';
		rowData += '<a href="#" id="ProductVariant' + count + 'SelectShow" class="ProductVariantSelectShow" data-row-number="' + count + '">vybrat</a>';
//		rowData += '<input name="data[CSTransactionItem][' + count + '][product_name]" type="text" class="CSTransactionItemProductName" size="70" id="CSTransactionItem' + count + 'ProductName" />';
		rowData += '<input type="hidden" name="data[CSTransactionItem][' + count + '][product_variant_id]" id="CSTransactionItem' + count + 'ProductVariantId" />';
		rowData += '</td>';
		rowData += '<td style="width:5%">&nbsp;</td>';
		rowData += '<td style="width:5%">&nbsp;</td>';
		rowData += '<td style="width:5%">&nbsp;</td>';
		rowData += '<td style="width:5%">&nbsp;</td>';
		rowData += '<td style="width:5%"><input name="data[CSTransactionItem][' + count + '][quantity]" type="text" size="5" maxlength="11" id="CSTransactionItem' + count + 'Quantity" /></td>';
		rowData += '<td style="width:12%"><input name="data[CSTransactionItem][' + count + '][price_total]" type="text" size="20" maxlength="11" id="CSTransactionItem' + count + 'Price" class="price"/></td>';
		rowData += '<td style="width:6%"><a href="#" class="addRowButton"></a>&nbsp;<a href="#" class="removeRowButton"></a></td>';
		rowData += '</tr>';
		return rowData;
	}
