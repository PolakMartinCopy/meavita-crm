$(function() {
	// pamatuje si pocet radku ve formulari pro vlozeni/editaci naskladneni
	var rowCount = $('.product_row').length;
	var productVariantListRow = null;
	
	$("#CSStoringDate").datepicker({
		changeMonth: false,
		numberOfMonths: 1
	});
	
	$('#BusinessPartnerSelectDiv').dialog({
		autoOpen: false,
		resizable: false,
		width: 800,
		/*height:140, */
		modal: true,
		create: function() {
			$(this).attr('style', 'font-size:12px');
		}
	});
	
	// nastaveni jQuery UI Modal Dialog pro zobrazeni tabulky pro vyber produktu
	$(document).delegate('.BusinessPartnerSelectShow', 'click', function(e) {
		e.preventDefault();
		productVariantListRow = $(this).attr('data-row-number');
		 $("#BusinessPartnerSelectDiv").dialog("open");
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
    	$('#CSTransactionItem' + productVariantListRow + 'BusinessPartnerName').remove();
    	$('#CSTransactionItem' + productVariantListRow + 'BusinessPartnerId').remove();
    	
    	// pred odkaz pro vlozeni obchodniho partnera dam input, kam vlozim nazev
    	$('#BusinessPartner' + productVariantListRow + 'SelectShow').before('<input type="text" size="30" value="' + businessPartnerName + '" id="CSTransactionItem' + productVariantListRow + 'BusinessPartnerName" class="CSTransactionItemBusinessPartnerName" name="data[CSTransactionItem][' + productVariantListRow + '][business_partner_name]"/>');
    	
    	// k tomuto inputu dam hidden field pro zapamatovani id
    	$('#BusinessPartner' + productVariantListRow + 'SelectShow').before('<input type="hidden" value="' + businessPartnerId + '" id="CSTransactionItem' + productVariantListRow + 'BusinessPartnerId" class="CSTransactionItemBusinessPartnerId" name="data[CSTransactionItem][' + productVariantListRow + '][business_partner_id]"/>');
    });
    
	$('#ProductSelectDiv').dialog({
		autoOpen: false,
		resizable: false,
		width: 800,
		/*height:140, */
		modal: true,
		create: function() {
			$(this).attr('style', 'font-size:12px');
		}
	});
	
	// nastaveni jQuery UI Modal Dialog pro zobrazeni tabulky pro vyber produktu
	$(document).delegate('.ProductSelectShow', 'click', function(e) {
		e.preventDefault();
		productVariantListRow = $(this).attr('data-row-number');
		 $("#ProductSelectDiv").dialog("open");
	});
	
	// nastaveni tabulky pro vyhledani a oznaceni obchodniho partnera
    $('#ProductSelectTable').DataTable({
    	'ajax': '/user/products/ajax_list',
    	'info': false,
    	'ordering': false,
    	'paging': false,
    	'language': {
    		'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Czech.json'
    	}
    });
    
    // akce po vyberu obchodniho partnera
    $(document).delegate('.ProductSelectLink', 'click', function(e) {
    	e.preventDefault();
    	// mam id obchodniho partnera
    	var productId = $(this).attr('data-p-id');
    	// nactu nazev obchodniho partnera
    	var productName = $(this).attr('data-p-name');
    	
    	// zavru okno pro vyhledani obchodniho partnera
    	$('#ProductSelectDiv').dialog('close');
    	
    	// odstranim elementy obsahujici info o zvolenem OP (pokud nejaky je)
    	$('#CSTransactionItem' + productVariantListRow + 'ProductName').remove();
    	$('#CSTransactionItem' + productVariantListRow + 'ProductId').remove();
    	
    	// pred odkaz pro vlozeni obchodniho partnera dam input, kam vlozim nazev
    	$('#Product' + productVariantListRow + 'SelectShow').before('<input type="text" size="30" value="' + productName + '" id="CSTransactionItem' + productVariantListRow + 'ProductName" class="CSTransactionItemProductName" name="data[CSTransactionItem][' + productVariantListRow + '][product_name]"/>');
    	
    	// k tomuto inputu dam hidden field pro zapamatovani id
    	$('#Product' + productVariantListRow + 'SelectShow').before('<input type="hidden" value="' + productId + '" id="CSTransactionItem' + productVariantListRow + 'ProductId" class="CSTransactionItemProductId" name="data[CSTransactionItem][' + productVariantListRow + '][product_id]"/>');
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
	var rowData = '<tr rel="' + count + '" class="product_row">';
	rowData += '<td nowrap nowrap style="width:29%">';
	rowData += '<a href="#" id="Product' + count + 'SelectShow" class="ProductSelectShow" data-row-number="' + count + '">vybrat</a>';
//	rowData += '<input name="data[CSTransactionItem][' + count + '][product_name]" type="text" class="CSTransactionItemProductName" size="70" id="CSTransactionItem' + count + 'ProductName" />';
	rowData += '<input type="hidden" name="data[CSTransactionItem][' + count + '][product_id]" id="CSTransactionItem' + count + 'ProductId" />';
	//rowData += '<a href="#new_product_form" class="new_product_link"><img src="/images/icons/add.png" alt="Novy" /></a>';
	rowData += '</td>';
	rowData += '<td style="width:5%"><input name="data[CSTransactionItem][' + count + '][product_variant_lot]" type="text" size="7" id="CSTransactionItem' + count + 'Lot"></td>';
	rowData += '<td style="width:5%"><input name="data[CSTransactionItem][' + count + '][product_variant_exp]" type="text" size="7" id="CSTransactionItem' + count + 'Exp"></td>';
	rowData += '<td style="width:28%">';
	rowData += '<a href="#" id="BusinessPartner' + count + 'SelectShow" class="BusinessPartnerSelectShow" data-row-number="' + count + '">vybrat</a>';
	rowData += '<input type="hidden" name="data[CSTransactionItem][' + count + '][business_partner_id]" id="CSTransactionItem' + count + 'BusinessPartnerId" />';
	rowData += '</td>';
	rowData += '<td style="width:5%" align="right"><input name="data[CSTransactionItem][' + count + '][quantity]" type="text" size="2" maxlength="11" id="CSTransactionItem' + count + 'Quantity" /></td>';
	rowData += '<td style="width:12%"><input name="data[CSTransactionItem][' + count + '][price_total]" type="text" size="20" maxlength="11" id="CSTransactionItem' + count + 'Price" /></td>';
	rowData += '<td style="width:5%">';
	rowData += '<select name="data[CSTransactionItem][' + count + '][currency_id]" id="CSTransactionItem' + count + 'CurrencyId" class="CSTransactionItemCurrency">';
	jQuery.each(currencies, function(index, value) {
		rowData += '<option value="' + index + '">' + value + '</option>';
	});
	rowData += '</select>';
	rowData += '<td style="width:5%"><input name="data[CSTransactionItem][' + count + '][exchange_rate] type="text" size="3" maxlength="8" id="CSTransactionItem' + count + 'ExchangeRate' + '"  value="1"/></td>';
	rowData += '<td style="width:6%"><a href="#" class="addRowButton"></a>&nbsp;<a href="#" class="removeRowButton"></a></td>';
	rowData += '</tr>';
	return rowData;
}
