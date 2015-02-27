$(function() {
	var productVariantListRow = null;
	var languageId = null;
	
	$('#ProductVariantSelectDiv').dialog({
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
	$(document).delegate('.ProductVariantSelectShow', 'click', function(e) {
		e.preventDefault();
		productVariantListRow = $(this).attr('data-row-number');
		 $("#ProductVariantSelectDiv").dialog("open");
	});
	
	// nastaveni tabulky pro vyhledani a oznaceni
	// chci zobrazit k vyberu i produkty s nulovym poctem na sklade?
	var ajaxUri = '/user/product_variants/ajax_list/';
	// pri vkladani na fakturu ne
	if (window.modelName == 'CSInvoice') {
		ajaxUri = '/user/product_variants/ajax_list/0';
	}
    $('#ProductVariantSelectTable').DataTable({
    	'ajax': ajaxUri,
		'fnInitComplete': function() {
	        $('#ProductVariantSelectTable tbody tr').each(function() {
	        	$(this).find('td:eq(0)').attr('style', 'text-align:left');
	        	// sloupce s cenou a mnozstvim chci zarovnat doprava
	        	$(this).find('td:eq(3)').attr('align', 'right');
	        	$(this).find('td:eq(4)').attr('align', 'right');
	            $(this).find('td:eq(5)').attr('align', 'right');
	        });
	    },
    	'info': false,
    	'ordering': false,
    	'paging': false,
    	'language': {
    		'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Czech.json'
    	}
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
    	// skladova cena bez dph
    	var productVariantPrice = $(this).attr('data-pv-price');
    	// skladova cena s DPH
    	var productVariantPriceVat = $(this).attr('data-pv-price-vat');
    	
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
    			$(this).find('td:eq(5)').html('<input type="hidden" value="' + productVariantPriceVat + '" name="data[CSTransactionItem][' + productVariantListRow + '][product_variant_price_vat]" id="CSTransactionItem' + productVariantListRow + 'ProductVariantPriceVat"/>' + productVariantPriceVat);
    			$(this).find('td:eq(5)').attr('align', 'right');
    		} 
    	});
    }); 
	
});