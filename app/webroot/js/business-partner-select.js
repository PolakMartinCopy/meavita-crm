$(function() {
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
	
	// nastaveni jQuery UI Modal Dialog pro zobrazeni tabulky pro vyber obchodniho partner
	$( "#BusinessPartnerSelectShow" ).click(function(e) {
		e.preventDefault();
		 $( "#BusinessPartnerSelectDiv" ).dialog( "open" );
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
		$('#' + modelName + 'BusinessPartnerName').remove();
		$('#' + modelName + 'BusinessPartnerId').remove();
		
		// pred odkaz pro vlozeni obchodniho partnera dam input, kam vlozim nazev OP
		$('#BusinessPartnerSelectShow').before('<input type="text" size="50" value="' + businessPartnerName + '" id="' + modelName + 'BusinessPartnerName" name="data[' + modelName + '][business_partner_name]"/>');
		
		// k tomuto inputu dam hidden field pro zapamatovani id obchodniho partnera
		$('#BusinessPartnerSelectShow').before('<input type="hidden" value="' + businessPartnerId + '" id="' + modelName + 'BusinessPartnerId" name="data[' + modelName + '][business_partner_id]"/>');
	}); 
});   