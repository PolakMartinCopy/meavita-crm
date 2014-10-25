<?php
if (isset($this->params['named']['tab'])) {
	$tab_pos = $this->params['named']['tab'];
?>
	<script>
		$(function() {
			$( "#tabs" ).tabs("select", "#tabs-<?php echo $tab_pos?>");
		});
	</script>
<?php } ?>

<h1><?php echo (!empty($business_partner['BusinessPartner']['branch_name']) ? $business_partner['BusinessPartner']['branch_name'] . ', ' : '') . $business_partner['BusinessPartner']['name']?></h1>

<div id="tabs">
	<ul>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_view')) { ?>
		<li><a href="#tabs-1">Info</a></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses')) { ?>
		<li><a href="#tabs-2">Adresy</a></li>
<?php } ?>
<?php
/* if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses')) { ?>
		<li><a href="#tabs-5">Pobočky</a></li>
<?php } */ ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Documents')) { ?>
		<li><a href="#tabs-6">Dokumenty</a></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ContactPeople/user_view')) { ?>
		<li><a href="#tabs-7">Kont. osoby</a></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_view')) { ?>
		<li><a href="#tabs-8">Obch. jednání</a></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/StoreItems/index')) { ?>
		<li><a href="#tabs-9">Sklad</a></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/DeliveryNotes/index')) { ?>
		<li><a href="#tabs-10">Dod. listy</a></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Sales/index')) { ?>
		<li><a href="#tabs-11">Prodeje</a></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Transactions/index')) { ?>
		<li><a href="#tabs-12">Pohyby</a></li>
<?php }?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSStorings/index')) { ?>
		<li><a href="#tabs-17">Mea Naskladnění</a>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSInvoices/index')) { ?>
		<li><a href="#tabs-14">Mea Faktury</a>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSCreditNotes/index')) { ?>
		<li><a href="#tabs-15">Mea Dobropisy</a></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSTransactions/index')) { ?>
		<li><a href="#tabs-16">Mea Pohyby</a>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepSales/index')) { ?>
		<li><a href="#tabs-21">Nákupy od Mea repů</a>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepPurchases/index')) { ?>
		<li><a href="#tabs-22">Prodeje Mea repům</a>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepTransactions/index')) { ?>
		<li><a href="#tabs-23">Transakce s Mea repy</a>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCStorings/index')) { ?>
		<li><a href="#tabs-24">MC Naskladnění</a>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCInvoices/index')) { ?>
		<li><a href="#tabs-25">MC Faktury</a>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCCreditNotes/index')) { ?>
		<li><a href="#tabs-26">MC Dobropisy</a></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCTransactions/index')) { ?>
		<li><a href="#tabs-27">MC Pohyby</a>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepSales/index')) { ?>
		<li><a href="#tabs-18">Nákupy od MC repů</a>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepPurchases/index')) { ?>
		<li><a href="#tabs-19">Prodeje MC repům</a>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/RepTransactions/index')) { ?>
		<li><a href="#tabs-20">Transakce s MC repy</a>
<?php } ?>
		<li><a href="#tabs-13">Poznámky</a></li>
	</ul>
	
<?php /* TAB 1 ****************************************************************************************************************/ ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_view')) { ?>
	<div id="tabs-1">
		<h2>Základní informace</h2>
		<table class="left_heading">
			<tr>
				<th>ID</th>
				<td><?php echo $business_partner['BusinessPartner']['id']?></td>
			</tr>
			<tr>
				<th>Název pobočky</th>
				<td><?php echo $business_partner['BusinessPartner']['branch_name']?></td>
			</tr>
			<tr>
				<th>Název firmy</th>
				<td><?php echo $business_partner['BusinessPartner']['name']?></td>
			</tr>
			<tr>
				<th>Datum vložení</th>
				<td><?php echo $business_partner['BusinessPartner']['created']?></td>
			</tr>
			<tr>
				<th>IČO</th>
				<td><?php echo $business_partner['BusinessPartner']['ico']?></td>
			</tr>
			<tr>
				<th>DIČ</th>
				<td><?php echo $business_partner['BusinessPartner']['dic']?></td>
			</tr>
			<tr>
				<th>IČZ</th>
				<td><?php echo $business_partner['BusinessPartner']['icz']?></td>
			</tr>
			<tr>
				<th>Email</th>
				<td><?php echo $business_partner['BusinessPartner']['email']?></td>
			</tr>
			<tr>
				<th>Telefon</th>
				<td><?php echo $business_partner['BusinessPartner']['phone']?></td>
			</tr>
			<tr>
				<th>Aktivní</th>
				<td><?php echo $business_partner['BusinessPartner']['active']?></td>
			</tr>
			<tr>
				<th>Poznámka</th>
				<td><?php echo $business_partner['BusinessPartner']['note']?></td>
			</tr>
			<tr>
				<th>Provozní doba</th>
				<td><?php echo $business_partner['BusinessPartner']['opening_hours']?></td>
			</tr>
			<tr>
				<th>Uživatel</th>
				<td><?php echo $business_partner['User']['last_name'] . ' ' . $business_partner['User']['first_name']?></td>
			</tr>
		</table>
		<ul>
			<li><?php echo $html->link('Upravit obchodního partnera', array('controller' => 'business_partners', 'action' => 'edit', $business_partner['BusinessPartner']['id']))?>
		</ul>
	</div>
<?php } ?>
<?php /* TAB 2 ****************************************************************************************************************/ ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses')) { ?>
	<div id="tabs-2">
		<h2>Adresa sídla</h2>
		<table class="left_heading">
			<tr>
				<th>Název</th>
				<td><?php echo $seat_address['Address']['name']?></td>
			</tr>
			<tr>
				<th>Křestní jméno osoby</th>
				<td><?php echo $seat_address['Address']['person_first_name']?></td>
			</tr>
			<tr>
				<th>Příjmení osoby</th>
				<td><?php echo $seat_address['Address']['person_last_name']?></td>
			</tr>
			<tr>
				<th>Ulice</th>
				<td><?php echo $seat_address['Address']['street']?></td>
			</tr>
			<tr>
				<th>Číslo popisné</th>
				<td><?php echo $seat_address['Address']['number']?></td>
			</tr>
			<tr>
				<th>Orientační číslo</th>
				<td><?php echo $seat_address['Address']['o_number']?></td>
			</tr>
			<tr>
				<th>Město</th>
				<td><?php echo $seat_address['Address']['city']?></td>
			</tr>
			<tr>
				<th>PSČ</th>
				<td><?php echo $seat_address['Address']['zip']?></td>
			</tr>
			<tr>
				<th>Okres</th>
				<td><?php echo $seat_address['Address']['region']?></td>
			</tr>
		</table>
		<ul>
			<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_edit')) { ?>
			<li><?php echo $html->link('Upravit adresu sídla', array('controller' => 'addresses', 'action' => 'edit', $seat_address['Address']['id']))?></li>
			<?php } ?>
		</ul>
		
		<h2>Fakturační adresa</h2>
		<?php if (empty($invoice_address)) { ?>
		<p><em>Obchodní partner nemá zadánu fakturační adresu.</em></p>
		<ul>
			<li><?php echo $html->link('Zadat fakturační adresu', array('controller' => 'addresses', 'action' => 'add', 'address_type_id' => 3, 'business_partner_id' => $business_partner['BusinessPartner']['id']))?>
		</ul>
		<?php } else { ?>
		<table class="left_heading">
			<tr>
				<th>Název</th>
				<td><?php echo $invoice_address['Address']['name']?></td>
			</tr>
			<tr>
				<th>Křestní jméno osoby</th>
				<td><?php echo $invoice_address['Address']['person_first_name']?></td>
			</tr>
			<tr>
				<th>Příjmení osoby</th>
				<td><?php echo $invoice_address['Address']['person_last_name']?></td>
			</tr>
			<tr>
				<th>Ulice</th>
				<td><?php echo $invoice_address['Address']['street']?></td>
			</tr>
			<tr>
				<th>Číslo popisné</th>
				<td><?php echo $invoice_address['Address']['number']?></td>
			</tr>
			<tr>
				<th>Orientační číslo</th>
				<td><?php echo $invoice_address['Address']['o_number']?></td>
			</tr>
			<tr>
				<th>Město</th>
				<td><?php echo $invoice_address['Address']['city']?></td>
			</tr>
			<tr>
				<th>PSČ</th>
				<td><?php echo $invoice_address['Address']['zip']?></td>
			</tr>
			<tr>
				<th>Okres</th>
				<td><?php echo $invoice_address['Address']['region']?></td>
			</tr>
		</table>
		<ul>
			<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/edit')) { ?>
			<li><?php echo $html->link('Upravit fakturační adresu', array('controller' => 'addresses', 'action' => 'edit', $invoice_address['Address']['id']))?></li>
			<?php } ?>
			<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_delete')) { ?>
			<li><?php echo $html->link('Smazat fakturační adresu', array('controller' => 'addresses', 'action' => 'delete', $invoice_address['Address']['id']), null, 'Opravdu chcete smazat fakturační adresu ' . $invoice_address['Address']['name'] . '?')?></li>
			<?php } ?>
		</ul>
		<?php } // end if?>
		
				<h2>Doručovací adresa</h2>
		<?php if (empty($delivery_address)) { ?>
		<p><em>Obchodní partner nemá zadánu doručovací adresu.</em></p>
		<ul>
			<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_add')) { ?>
			<li><?php echo $html->link('Zadat doručovací adresu', array('controller' => 'addresses', 'action' => 'add', 'address_type_id' => 4, 'business_partner_id' => $business_partner['BusinessPartner']['id']))?>
			<?php } ?>
		</ul>
		<?php } else { ?>
		<table class="left_heading">
			<tr>
				<th>Název</th>
				<td><?php echo $delivery_address['Address']['name']?></td>
			</tr>
			<tr>
				<th>Křestní jméno osoby</th>
				<td><?php echo $delivery_address['Address']['person_first_name']?></td>
			</tr>
			<tr>
				<th>Příjmení osoby</th>
				<td><?php echo $delivery_address['Address']['person_last_name']?></td>
			</tr>
			<tr>
				<th>Ulice</th>
				<td><?php echo $delivery_address['Address']['street']?></td>
			</tr>
			<tr>
				<th>Číslo popisné</th>
				<td><?php echo $delivery_address['Address']['number']?></td>
			</tr>
			<tr>
				<th>Orientační číslo</th>
				<td><?php echo $delivery_address['Address']['o_number']?></td>
			</tr>
			<tr>
				<th>Město</th>
				<td><?php echo $delivery_address['Address']['city']?></td>
			</tr>
			<tr>
				<th>PSČ</th>
				<td><?php echo $delivery_address['Address']['zip']?></td>
			</tr>
			<tr>
				<th>Okres</th>
				<td><?php echo $delivery_address['Address']['region']?></td>
			</tr>
		</table>
		<ul>
			<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_edit')) { ?>
			<li><?php echo $html->link('Upravit doručovací adresu', array('controller' => 'addresses', 'action' => 'edit', $delivery_address['Address']['id']))?></li>
			<?php } ?>
			<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_delete')) { ?>
			<li><?php echo $html->link('Smazat doručovací adresu', array('controller' => 'addresses', 'action' => 'delete', $delivery_address['Address']['id']), null, 'Opravdu chcete smazat doručovací adresu ' . $delivery_address['Address']['name'] . '?')?></li>
			<?php } ?>
		</ul>
		<?php } // end if?>
	</div>
<?php } ?>
<?php /* TAB 5 ****************************************************************************************************************/ ?>
<?php /* ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses')) { ?>
	<div id="tabs-5">
		<h2>Adresy poboček</h2>
		
		<button id="search_form_show_addresses">vyhledávací formulář</button>
		<?php
			$hide = ' style="display:none"';
			if ( isset($this->data['AddressSearch']) ){
				$hide = '';
			}
		?>
		<div id="search_form_addresses"<?php echo $hide?>>
			<?php
			echo $form->create('Address', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 5))); ?>
			<table class="left_heading">
				<tr>
					<th>Název</th>
					<td><?php echo $form->input('AddressSearch.Address.name', array('label' => false))?></td>
					<th>Jméno osoby</th>
					<td><?php echo $form->input('AddressSearch.Address.person_first_name', array('label' => false))?></td>
					<th>Příjmení osoby</th>
					<td><?php echo $form->input('AddressSearch.Address.person_last_name', array('label' => false))?></td>
				</tr>
				<tr>
					<th>Ulice</th>
					<td><?php echo $form->input('AddressSearch.Address.street', array('label' => false))?></td>
					<th>Č. p.</th>
					<td><?php echo $form->input('AddressSearch.Address.number', array('label' => false))?></td>
					<th>O. č.</th>
					<td><?php echo $form->input('AddressSearch.Address.o_number', array('label' => false))?></td>
				</tr>
				<tr>
					<th>Město</th>
					<td><?php echo $form->input('AddressSearch.Address.city', array('label' => false))?></td>
					<th>PSČ</th>
					<td><?php echo $form->input('AddressSearch.Address.zip', array('label' => false))?></td>
					<th>Okres</th>
					<td><?php echo $form->input('AddressSearch.Address.region', array('label' => false))?></td>
				</tr>
				<tr>
					<td colspan="6"><?php echo $html->link('reset filtru', $_SERVER['REQUEST_URI'] . '/reset:address')?></td>
				</tr>
			</table>
			<?php
				echo $form->hidden('AddressSearch.Address.search_form', array('value' => 1));
				echo $form->submit('Vyhledávat');
				echo $form->end();
			?>
		</div>
		
		<script>
			$("#search_form_show_addresses").click(function () {
				if ($('#search_form_addresses').css('display') == "none"){
					$("#search_form_addresses").show("slow");
				} else {
					$("#search_form_addresses").hide("slow");
				}
			});
		</script>
		
		<?php
		echo $form->create('CSV', array('url' => array('controller' => 'addresses', 'action' => 'xls_export')));
		echo $form->hidden('data', array('value' => serialize($branch_addresses_find)));
		echo $form->hidden('fields', array('value' => serialize($branch_addresses_export_fields)));
		echo $form->submit('CSV');
		echo $form->end();
		?>
		<ul>
			<li><?php echo $html->link('Zadat adresu pobočky', array('controller' => 'addresses', 'action' => 'add', 'address_type_id' => 5, 'business_partner_id' => $business_partner['BusinessPartner']['id']))?>
		</ul>
		<?php if (empty($branch_addresses)) { ?>
		<p><em>Obchodní partner nemá zadánu adresy poboček.</em></p>
		<?php } else {
		$paginator->options(array(
			'url' => array('tab' => 5, 0 => $business_partner['BusinessPartner']['id'])
		));
		$paginator->params['paging'] = $branch_addresses_paging;
		$paginator->__defaultModel = 'Address'; ?>
		<table class="top_heading">
			<tr>
				<th><?php echo $paginator->sort('Název', 'Address.name')?></th>
				<th><?php echo $paginator->sort('Křestní jméno osoby' , 'Address.person_first_name') ?></th>
				<th><?php echo $paginator->sort('Příjmení osoby', 'Address.person_last_name') ?></th>
				<th><?php echo $paginator->sort('Ulice', 'Address.street') ?></th>
				<th><?php echo $paginator->sort('Číslo popisné', 'Address.number') ?></th>
				<th><?php echo $paginator->sort('Orientační číslo', 'Address.o_number') ?></th>
				<th><?php echo $paginator->sort('Město', 'Address.city') ?></th>
				<th><?php echo $paginator->sort('PSČ', 'Address.zip') ?></th>
				<th><?php echo $paginator->sort('Okres', 'Address.region') ?></th>
				<th>&nbsp;</th>
			</tr>
		<?php
			$odd = '';
			foreach ($branch_addresses as $branch_address) {
				$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		?>
			<tr<?php echo $odd?>>
				<td><?php echo $branch_address['Address']['name']?></td>
				<td><?php echo $branch_address['Address']['person_first_name']?></td>
				<td><?php echo $branch_address['Address']['person_last_name']?></td>
				<td><?php echo $branch_address['Address']['street']?></td>
				<td><?php echo $branch_address['Address']['number']?></td>
				<td><?php echo $branch_address['Address']['o_number']?></td>
				<td><?php echo $branch_address['Address']['city']?></td>
				<td><?php echo $branch_address['Address']['zip']?></td>
				<td><?php echo $branch_address['Address']['region']?></td>
				<td class="actions"><?php 
					$links = array();
					if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_edit')) {
						$links[] = $html->link('Upravit', array('controller' => 'addresses', 'action' => 'edit', $branch_address['Address']['id']));
					}
					if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Addresses/user_delete')) {
						$links = $html->link('Smazat', array('controller' => 'addresses', 'action' => 'delete', $branch_address['Address']['id']), null, 'Opravdu chcete adresu pobočky ' . $branch_address['Address']['name'] . ' smazat?');
					}
					echo implode(' | ', $links);
				?></td>
			</tr>
		<?php } // end foreach ?>
		</table>
		<?php }
		echo $paginator->numbers();
		echo $paginator->prev('« Předchozí ', null, null, array('class' => 'disabled'));
		echo $paginator->next(' Další »', null, null, array('class' => 'disabled'));
	?>
	</div>
<?php } ?>
<?php */ ?>
<?php /* TAB 6 ****************************************************************************************************************/ ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Documents')) { ?>
	<div id="tabs-6">
		<h2>Dokumenty</h2>
		<button id="search_form_show_documents">vyhledávací formulář</button>
		<?php
			$hide = ' style="display:none"';
			if ( isset($this->data['DocumentForm2']) ){
				$hide = '';
			}
		?>
		<div id="search_form_documents"<?php echo $hide?>>
			<?php echo $form->create('Document', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 6))); ?>
			<table class="left_heading">
				<tr>
					<th>Název</th>
					<td><?php echo $form->input('DocumentForm2.Document.title', array('label' => false))?></td>
					<th>Vloženo</th>
					<td><?php echo $form->input('DocumentForm2.Document.created', array('label' => false, 'type' => 'text'))?></td>
				</tr>
				<tr>
					<td colspan="4"><?php echo $html->link('reset filtru', array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'reset' => 'documents'))?></td>
				</tr>
			</table>
			<?php
				echo $form->hidden('DocumentForm2.Document.search_form', array('value' => 1));
				echo $form->submit('Vyhledávat');
				echo $form->end();
			?>
		</div>
		
		<script>
			$("#search_form_show_documents").click(function () {
				if ($('#search_form_documents').css('display') == "none"){
					$("#search_form_documents").show("slow");
				} else {
					$("#search_form_documents").hide("slow");
				}
			});
		
			$(function() {
				var dates = $( "#DocumentForm2DocumentCreated" ).datepicker({
					defaultDate: "+1w",
					changeMonth: false,
					numberOfMonths: 1,
					onSelect: function( selectedDate ) {
						var option = this.id == "DocumentForm2DocumentCreated" ? "minDate" : "maxDate",
							instance = $( this ).data( "datepicker" ),
							date = $.datepicker.parseDate(
								instance.settings.dateFormat ||
								$.datepicker._defaults.dateFormat,
								selectedDate, instance.settings );
						dates.not( this ).datepicker( "option", option, date );
					}
				});
			});
			$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
		</script>
		
		<?php if (empty($documents)) { ?>
		<p><em>K tomuto obchodnímu partnerovi nejsou přiděleny žádné dokumenty.</em></p>
		<?php } else { ?>
		<table class="top_heading">
			<tr>
				<th>ID</th>
				<th>Vloženo</th>
				<th>Název</th>
				<th>&nbsp;</th>
			</tr>
		<?php
			$odd = '';
			foreach ($documents as $document) {
				$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		?>
			<tr<?php echo $odd?>>
				<td><?php echo $document['Document']['id']?></td>
				<td><?php echo $document['Document']['created']?></td>
				<td><?php echo $html->link($document['Document']['title'], '/files/documents/' . $document['Document']['name'])?></td>
				<td class="actions">
					<?php echo $html->link('Přejmenovat', array('controller' => 'documents', 'action' => 'rename', $document['Document']['id']))?>
					<?php echo $html->link('Smazat', array('controller' => 'documents', 'action' => 'delete', $document['Document']['id']), null, 'Opravdu chcete dokument ' . $document['Document']['title'] . ' smazat?')?>
				</td>
			</tr>
		<?php } ?>
		</table>
		<?php }	?>
		
		<h3>Nahrát dokument z disku</h3>
<?
		echo $form->create('BusinessPartner', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'])));
		echo $form->submit('Zobrazit', array('div' => false));
		echo $form->text('BusinessPartner.document_fields', array('size' => '1')) . ' polí';
		echo $form->end();
	
		echo $form->Create('Document', array('url' => array('controller' => 'documents', 'action' => 'add'), 'type' => 'file')); ?>
		<fieldset>
			<legend>Nový dokument z disku</legend>
			<table class="leftHeading" cellpadding="5" cellspacing="3">
				<tr>
					<th>&nbsp;</th>
					<td>
						<?
							if ( !isset($this->data['BusinessPartner']['document_fields']) OR $this->data['BusinessPartner']['document_fields'] > 10 OR $this->data['BusinessPartner']['document_fields'] < 1 ) {
								$this->data['BusinessPartner']['document_fields'] = 1;
							}
							for ( $i = 0; $i < $this->data['BusinessPartner']['document_fields']; $i++ ){
						?>
								<input type="file" name="data[Document][document<?php echo $i?>]" />
								<br />
						<?php 	echo $form->input('Document.document' . $i . '.title', array('label' => 'Titulek:', 'size' => 40));
							}
						?>
					</td>
				</tr>
			</table>
<?
		echo $form->hidden('Document.document_fields', array('value' => $this->data['BusinessPartner']['document_fields']));
		echo $form->hidden('Document.business_partner_id', array('value' => $business_partner['BusinessPartner']['id']));
?>
		</fieldset>
<?
		echo $form->submit('Nahrát dokument');
		echo $form->end();
?>

		<h3>Nahrát dokument z webu</h3>
<?php 
		echo $form->create('BusinessPartner', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'])));
		echo $form->submit('Zobrazit', array('div' => false));
		echo $form->text('BusinessPartner.web_document_fields', array('size' => '1')) . ' polí';
		echo $form->end();
?>
		
		<?php echo $form->create('Document', array('url' => array('controller' => 'documents', 'action' => 'add_from_web')))?>
		<fieldset>
			<legend>Nový dokument z webu</legend>
			<table class="leftHeading" cellpadding="5" cellspacing="5">
			<?
				if ( !isset($this->data['BusinessPartner']['web_document_fields']) OR $this->data['BusinessPartner']['web_document_fields'] > 10 OR $this->data['BusinessPartner']['web_document_fields'] < 1 ) {
					$this->data['BusinessPartner']['web_document_fields'] = 1;
				}
				for ( $i = 0; $i < $this->data['BusinessPartner']['web_document_fields']; $i++ ){
					if ($i > 0) {
			?>
				<tr>
					<td colspan="2">
						<hr/>
					</td>
				</tr>
			<?php 
					}
			?>
				
				<tr>
					<th>URL</th>
					<td><?php echo $form->input('Document.data.' . $i . '.url', array('label' => false, 'size' => 100))?></td>
				</tr>
				<tr>
					<th>Název souboru</th>
					<td><?php echo $form->input('Document.data.' . $i . '.name', array('label' => false, 'size' => 50))?></td>
				</tr>
				<tr>
					<th>Titulek dokumentu</th>
					<td><?php echo $form->input('Document.data.' . $i . '.title', array('label' => false, 'size' => 50))?></td>
				</tr>
			<?php } ?>
			</table>
		<?php
			echo $form->hidden('Document.business_partner_id', array('value' => $business_partner['BusinessPartner']['id']));
		?>
		</fieldset>
<?php 	
	echo $form->submit('Nahrát dokument z webu');
	echo $form->end();
?>
	</div>
<?php } ?>
<?php /* TAB 7 ****************************************************************************************************************/ ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ContactPeople/user_view')) { ?>
	<div id="tabs-7">
		<h2>Kontaktní osoby</h2>
		<button id="search_form_show_contact_people">vyhledávací formulář</button>
		<?php
			$hide = ' style="display:none"';
			if ( isset($this->data['ContactPersonSearch']) ){
				$hide = '';
			}
		?>
		<div id="search_form_contact_people"<?php echo $hide?>>
			<?php
			echo $form->create('ContactPerson', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 7))); ?>
			<table class="left_heading">
				<tr>
					<th>Jméno</th>
					<td><?php echo $form->input('ContactPersonSearch.ContactPerson.first_name', array('label' => false))?></td>
					<th>Příjmení</th>
					<td><?php echo $form->input('ContactPersonSearch.ContactPerson.last_name', array('label' => false))?></td>
					<th>Email</th>
					<td><?php echo $form->input('ContactPersonSearch.ContactPerson.email', array('label' => false))?></td>
				</tr>
				<tr>
					<th>Telefon</th>
					<td><?php echo $form->input('ContactPersonSearch.ContactPerson.phone', array('label' => false))?></td>
					<th>Mobil</th>
					<td><?php echo $form->input('ContactPersonSearch.ContactPerson.cellular', array('label' => false))?></td>
					<th>Je hlavní?</th>
					<td><?php echo $this->Form->input('ContactPersonSearch.ContactPerson.is_main', array('label' => false, 'options' => array(0 => 'Ne', 'Ano'), 'empty' => true))?>
				</tr>
				<tr>
					<th>Třída kampaní</th>
					<td><?php echo $this->Form->input('ContactPersonSearch.ContactPerson.mailing_campaign_id', array('label' => false, 'options' => $mailing_campaigns, 'empty' => true))?></td>
					<th>Pobočka</th>
					<td><?php echo $this->Form->input('ContactPersonSearch.BusinessPartner.branch_name', array('label' => false))?></td>
					<th>Obchodní partner</th>
					<td><?php echo $form->input('ContactPersonSearch.BusinessPartner.name', array('label' => false))?></td>
				</tr>
				<tr>
					<td colspan="6">
						<?php echo $html->link('reset filtru', $_SERVER['REQUEST_URI'] . '/reset:contact_people') ?>
					</td>
				</tr>
			</table>
			<?php
				echo $form->hidden('ContactPersonSearch.ContactPerson.search_form', array('value' => 1));
				echo $form->submit('Vyhledávat');
				echo $form->end();
			?>
		</div>
		
		<script>
			$("#search_form_show_contact_people").click(function () {
				if ($('#search_form_contact_people').css('display') == "none"){
					$("#search_form_contact_people").show("slow");
				} else {
					$("#search_form_contact_people").hide("slow");
				}
			});
		</script>

		<?php
		echo $form->create('CSV', array('url' => array('controller' => 'contact_people', 'action' => 'xls_export')));
		echo $form->hidden('data', array('value' => serialize($contact_people_find)));
		echo $form->hidden('fields', array('value' => serialize($contact_people_export_fields)));
		echo $form->submit('CSV');
		echo $form->end();
		?>
		<ul>
			<li><?php echo $html->link('Zadat kontaktní osobu', array('controller' => 'contact_people', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?>
		</ul>
		<?php if (empty($contact_people)) { ?>
		<p><em>Obchodní partner nemá zadané žádné kontaktní osoby.</em></p>
		<?php } else {
		$paginator->options(array(
			'url' => array('tab' => 7, 0 => $business_partner['BusinessPartner']['id'])
		));
		$paginator->params['paging'] = $contact_people_paging;
		$paginator->__defaultModel = 'ContactPerson'; ?>
		<table class="top_heading">
			<tr>
				<th><?php echo $paginator->sort('ID', 'ContactPerson.id')?></th>
				<th><?php echo $paginator->sort('Křestní jméno', 'ContactPerson.first_name')?></th>
				<th><?php echo $paginator->sort('Příjmení', 'ContactPerson.last_name')?></th>
				<th><?php echo $paginator->sort('Titul', 'ContactPerson.prefix')?></th>
				<th><?php echo $paginator->sort('Telefon', 'ContactPerson.phone')?></th>
				<th><?php echo $paginator->sort('Mobilní telefon', 'ContactPerson.cellular')?></th>
				<th><?php echo $paginator->sort('Email', 'ContactPerson.email')?></th>
				<th><?php echo $paginator->sort('Pobočka', 'BusinessPartner.branch_name')?></th>
				<th><?php echo $paginator->sort('Obchodní partner', 'BusinessPartner.name')?></th>
				<th><?php echo $this->Paginator->sort('Je hlavní?', 'ContactPerson.is_main')?></th>
				<th><?php echo $this->Paginator->sort('Třída kampaní', 'MailingCampaign.name')?></th>
				<th>&nbsp;</th>
			</tr>
		<?php
			$odd = '';
			foreach ($contact_people as $contact_person) {
				$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		?>
			<tr<?php echo $odd?>>
				<td><?php echo $contact_person['ContactPerson']['id']?></td>
				<td><?php echo $contact_person['ContactPerson']['first_name']?></td>
				<td><?php echo $html->link($contact_person['ContactPerson']['last_name'], array('controller' => 'contact_people', 'action' => 'view', $contact_person['ContactPerson']['id']))?></td>
				<td><?php echo $contact_person['ContactPerson']['prefix']?></td>
				<td><?php echo $contact_person['ContactPerson']['phone']?></td>
				<td><?php echo $contact_person['ContactPerson']['cellular']?></td>
				<td><?php echo $html->link($contact_person['ContactPerson']['email'], 'mailto:' . $contact_person['ContactPerson']['email'])?></td>
				<td><?php echo $html->link($contact_person['BusinessPartner']['branch_name'], array('controller' => 'business_partners', 'action' => 'view', $contact_person['BusinessPartner']['id']))?></td>
				<td><?php echo $html->link($contact_person['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $contact_person['BusinessPartner']['id']))?></td>
				<td><?php echo ($contact_person['ContactPerson']['is_main']) ? 'ano' : 'ne'?></td>
				<td><?php echo $contact_person['MailingCampaign']['name']?></td>
				<td class="actions"><?php 
					$links = array();
					if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ContactPeople/user_edit')) {
						$links[] = $html->link('Upravit', array('controller' => 'contact_people', 'action' => 'edit', $contact_person['ContactPerson']['id'], 'business_partner_id' => $business_partner['BusinessPartner']['id']));
					}
					if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ContactPeople/user_delete')) {
						$links[] = $html->link('Smazat', array('controller' => 'contact_people', 'action' => 'delete', $contact_person['ContactPerson']['id'], 'business_partner_id' => $business_partner['BusinessPartner']['id']), null, 'Opravdu chcete smazat kontatní osobu ' . $contact_person['ContactPerson']['first_name'] . ' ' . $contact_person['ContactPerson']['last_name'] . '?');
					}
					echo implode(' | ', $links);
				?></td>
			</tr>
		<?php } // end foreach ?>
		</table>
		<?php } // end if ?>
	</div>
<?php } ?>
<?php /* TAB 8 ****************************************************************************************************************/ ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_view')) { ?>
	<div id="tabs-8">
		<h2>Obchodní jednání</h2>

		<button id="search_form_show_business_session">vyhledávací formulář</button>
		<?php
			$hide = ' style="display:none"';
			if ( isset($this->data['BusinessSessionSearch']) ){
				$hide = '';
			}
		?>
		<div id="search_form_business_session"<?php echo $hide?>>
			
			<?php echo $form->create('BusinessSession', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 8))); ?>
			<table class="left_heading">
				<tr>
					<th>Obchodní partner</th>
					<td><?php echo $form->input('BusinessSessionSearch.BusinessSession.business_partner_name', array('label' => false, 'type' => 'text'))?></td>
					<th>Datum od</th>
					<td><?php echo $form->input('BusinessSessionSearch.BusinessSession.date_from', array('label' => false, 'type' => 'text'))?></td>
					<th>Datum do</th>
					<td><?php echo $form->input('BusinessSessionSearch.BusinessSession.date_to', array('label' => false, 'type' => 'text'))?></td>
				</tr>
				<tr>
					<th>Typ jednání</th>
					<td><?php echo $form->input('BusinessSessionSearch.BusinessSession.business_session_type_id', array('options' => $business_session_types, 'empty' => true, 'label' => false))?></td>
					<th>Popis</th>
					<td><?php echo $form->input('BusinessSessionSearch.BusinessSession.description', array('label' => false, 'type' => 'text'))?></td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="6">
						<?php
							echo $html->link('reset filtru', $_SERVER['REQUEST_URI'] . '/reset:business_session')
						?>
					</td>
				</tr>
			</table>
			<?php
				echo $form->hidden('BusinessSessionSearch.BusinessSession.search_form', array('value' => 1));
				echo $form->submit('Vyhledávat');
				echo $form->end();
			?>
		</div>
		
		<script>
			$("#search_form_show_business_session").click(function () {
				if ($('#search_form_business_session').css('display') == "none"){
					$("#search_form_business_session").show("slow");
				} else {
					$("#search_form_business_session").hide("slow");
				}
			});
			$(function() {
				var dates = $( "#BusinessSessionSearchBusinessSessionDateFrom, #BusinessSessionSearchBusinessSessionDateTo" ).datepicker({
					defaultDate: "+1w",
					changeMonth: false,
					numberOfMonths: 1,
					onSelect: function( selectedDate ) {
						var option = this.id == "BusinessSessionSearchBusinessSessionDateFrom" ? "minDate" : "maxDate",
							instance = $( this ).data( "datepicker" ),
							date = $.datepicker.parseDate(
								instance.settings.dateFormat ||
								$.datepicker._defaults.dateFormat,
								selectedDate, instance.settings );
						dates.not( this ).datepicker( "option", option, date );
					}
				});
			});
			$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
		</script>

		<?php 
		echo $form->create('CSV', array('url' => array('controller' => 'business_sessions', 'action' => 'xls_export')));
		echo $form->hidden('data', array('value' => serialize($business_sessions_find)));
		echo $form->hidden('fields', array('value' => serialize($business_sessions_export_fields)));
		echo $form->submit('CSV');
		echo $form->end();
		?>
		<ul>
			<li><?php echo $html->link('Zadat obchodní jednání', array('controller' => 'business_sessions', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
		</ul>
		
		<?php if (empty($business_sessions)) { ?>
		<p><em>K obchodnímu partnerovi se nevztahují žádná jednání.</em></p>
		<?php } else { 
		$paginator->options(array(
			'url' => array('tab' => 8, 0 => $business_partner['BusinessPartner']['id'])
		));
		$paginator->params['paging'] = $business_sessions_paging;
		$paginator->__defaultModel = 'BusinessSession'; ?>
		
		<table class="top_heading">
			<tr>
				<th><?php echo $paginator->sort('ID', 'BusinessSession.id')?></th>
				<th><?php echo $paginator->sort('Datum jednání', 'BusinessSession.date')?></th>
				<th><?php echo $paginator->sort('Obchodní partner', 'BusinessPartner.name')?></th>
				<th><?php echo $paginator->sort('Typ jednání', 'BusinessSessionType.name')?></th>
				<th><?php echo $paginator->sort('Stav jednání', 'BusinessSessionState.name')?></th>
				<th><?php echo $paginator->sort('Datum vložení', 'BusinessSession.created')?></th>
				<th><?php echo $paginator->sort('Založil', 'User.last_name')?></th>
				<th><?php echo $paginator->sort('Náklady', 'celkem')?></th>
				<th>&nbsp;</th>
			</tr>
		<?php
			$odd = '';
			foreach ($business_sessions as $business_session) {
				$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		?>
			<tr<?php echo $odd?>>
				<td><?php echo $business_session['BusinessSession']['id']?></td>
				<td><?php echo $business_session['BusinessSession']['date']?></td>
				<td><?php echo $html->link($business_session['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $business_session['BusinessPartner']['id']))?></td>
				<td><?php echo $business_session['BusinessSessionType']['name']?></td>
				<td><?php echo $business_session['BusinessSessionState']['name']?></td>
				<td><?php echo $business_session['BusinessSession']['created']?></td>
				<td><?php echo $business_session['User']['last_name']?></td>
				<td><?php echo floatval($business_session[0]['celkem'])?></td>
				<td class="actions"><?php 
					$links = array();
					if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_view')) {
						$links[] = $html->link('Detail', array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id']));
					}
					if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_edit')) {
						$links[] = $html->link('Upravit', array('controller' => 'business_sessions', 'action' => 'edit', $business_session['BusinessSession']['id']));
					}
					if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_close')) {
						$links[] = $html->link('Uzavřít', array('controller' => 'business_sessions', 'action' => 'close', $business_session['BusinessSession']['id']), null, 'Opravdu chcete obchnodní jednání ' . $business_session['BusinessSession']['id'] . ' označit jako uzavřené?');
					}
					if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessSessions/user_storno')) {
						$links[] = $html->link('Storno', array('controller' => 'business_sessions', 'action' => 'storno', $business_session['BusinessSession']['id']), null, 'Opravdu chcete obchodní jednání ' . $business_session['BusinessSession']['id'] . ' stornovat?');
					}
					echo implode(' | ', $links);
				?></td>
			</tr>
		<?php } ?>
		</table>
		<?php } // end if ?>
	</div>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/StoreItems/index')) { ?>
<?php /* TAB 9 ****************************************************************************************************************/ ?>
	<div id="tabs-9">
		<h2>Sklad</h2>

		<button id="search_form_show_store_item">vyhledávací formulář</button>
		<?php
			$hide = ' style="display:none"';
			if ( isset($this->data['StoreItemForm2']) ){
				$hide = '';
			}
		?>
		<div id="search_form_store_item"<?php echo $hide?>>
			
			<?php echo $form->create('StoreItem', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 9))); ?>
			<table class="left_heading">
				<tr>
					<td colspan="6">Zboží</td>
				</tr>
				<tr>
					<th>VZP kód</th>
					<td><?php echo $this->Form->input('StoreItemForm2.Product.vzp_code', array('label' => false))?></td>
					<th>Název</th>
					<td><?php echo $this->Form->input('StoreItemForm2.Product.name', array('label' => false))?></td>
					<th>Kód skupiny</th>
					<td><?php echo $this->Form->input('StoreItemForm2.Product.group_code', array('label' => false))?></td>
				</tr>
				<tr>
					<td colspan="6">
						<?php echo $html->link('reset filtru', $_SERVER['REQUEST_URI'] . '/reset:store_items') ?>
					</td>
				</tr>
			</table>
			<?php
				echo $form->hidden('StoreItemForm2.StoreItem.search_form', array('value' => 1));
				echo $form->submit('Vyhledávat');
				echo $form->end();
			?>
		</div>
		
		<script>
			$("#search_form_show_store_item").click(function () {
				if ($('#search_form_store_item').css('display') == "none"){
					$("#search_form_store_item").show("slow");
				} else {
					$("#search_form_store_item").hide("slow");
				}
			});
		</script>

		<?php 
		echo $form->create('CSV', array('url' => array('controller' => 'store_items', 'action' => 'xls_export')));
		echo $form->hidden('data', array('value' => serialize($store_items_find)));
		echo $form->hidden('fields', array('value' => serialize($store_items_export_fields)));
		echo $form->submit('CSV');
		echo $form->end();
		
		echo $form->create('PDF', array('url' => array('controller' => 'store_items', 'action' => 'pdf_export')));
		echo $form->hidden('business_partner_id', array('value' => $business_partner['BusinessPartner']['id']));
		echo $form->submit('PDF');
		echo $form->end();

		if (empty($store_items)) { ?>
		<p><em>Sklad obchodního partnera je prázdný.</em></p>
		<?php } else { 
		$paginator->options(array(
			'url' => array('tab' => 9, 0 => $business_partner['BusinessPartner']['id'])
		));

		$paginator->params['paging'] = $store_items_paging;
		$paginator->__defaultModel = 'StoreItem'; ?>
		
		<table class="top_heading">
			<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('Název produktu', 'Product.name')?></th>
					<th><?php echo $this->Paginator->sort('Kód VZP', 'Product.vzp_code')?></th>
					<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
					<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
					<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
					<th><?php echo $this->Paginator->sort('Množství', 'StoreItem.quantity')?></th>
					<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
					<th><?php echo $this->Paginator->sort('Kč/J', 'ProductVariant.meavita_price')?></th>
					<th><?php echo $this->Paginator->sort('Kč', 'StoreItem.item_total_price')?></th>
					<th>Posl. prodej</th>
				</tr>
			</thead>
			<tbody>
		<?php
			$odd = '';
			foreach ($store_items as $store_item) {
				$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		?>
				<tr<?php echo $odd?>>
					<td><?php echo $store_item['Product']['name']?></td>
					<td><?php echo $store_item['Product']['vzp_code']?></td>
					<td><?php echo $store_item['Product']['group_code']?></td>
					<td><?php echo $store_item['ProductVariant']['exp']?></td>
					<td><?php echo $store_item['ProductVariant']['lot']?></td>
					<td><?php echo $store_item['StoreItem']['quantity']?></td>
					<td><?php echo $store_item['Unit']['shortcut']?></td>
					<td><?php echo $store_item['ProductVariant']['meavita_price']?></td>
					<td><?php echo $store_item['StoreItem']['item_total_price']?></td>
					<td><?php echo czech_date($store_item['StoreItem']['last_sale_date'])?></td>
				</tr>
			</tbody>
		<?php } ?>
			<tfoot>
				<tr>
					<th>Celkem</th>
					<th colspan="4">&nbsp;</th>
					<th><?php echo $store_items_quantity?></th>
					<th colspan="2">&nbsp;</th>
					<th><?php echo $store_items_price?></td>
					<th colspan="2">&nbsp;</th>
				</tr>
			</tfoot>
		</table>
		<?php } // end if ?>
	</div>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/DeliveryNotes/index')) { ?>
<?php /* TAB 10 ****************************************************************************************************************/ ?>
	<div id="tabs-10">
		<h2>Dodací listy</h2>
		
		<button id="search_form_show_delivery_note">vyhledávací formulář</button>
		<?php
			$hide = ' style="display:none"';
			if ( isset($this->data['DeliveryNoteForm2']) ){
				$hide = '';
			}
		?>
		<div id="search_form_delivery_note"<?php echo $hide?>>
			
			<?php echo $form->create('DeliveryNote', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 10))); ?>
			<table class="left_heading">
				<tr>
					<td colspan="6">Odběratel</td>
				</tr>
				<tr>
					<th>Jméno</th>
					<td><?php echo $form->input('DeliveryNoteForm2.BusinessPartner.name', array('label' => false))?></td>
					<th>IČO</th>
					<td><?php echo $form->input('DeliveryNoteForm2.BusinessPartner.ico', array('label' => false))?></td>
					<th>DIČ</th>
					<td><?php echo $form->input('DeliveryNoteForm2.BusinessPartner.dic', array('label' => false))?></td>
				</tr>
				<tr>
					<th>Ulice</th>
					<td><?php echo $this->Form->input('DeliveryNoteForm2.Address.street', array('label' => false))?></td>
					<th>Město</th>
					<td><?php echo $this->Form->input('DeliveryNoteForm2.Address.city', array('label' => false))?></td>
					<th>Okres</th>
					<td><?php echo $this->Form->input('DeliveryNoteForm2.Address.region', array('label' => false))?></td>
				</tr>
				<tr>
					<td colspan="4">Dodací listy</td>
					<td colspan="2">Zboží</td>
				</tr>
				<tr>
					<th>Datum od</th>
					<td><?php echo $this->Form->input('DeliveryNoteForm2.DeliveryNote.date_from', array('label' => false))?></td>
					<th>Datum do</th>
					<td><?php echo $this->Form->input('DeliveryNoteForm2.DeliveryNote.date_to', array('label' => false))?></td>
					<th>Kód skupiny</th>
					<td><?php echo $this->Form->input('DeliveryNoteForm2.Product.group_code', array('label' => false))?></td>
				</tr>
				<tr>
					<th>Číslo dokladu</th>
					<td><?php echo $this->Form->input('DeliveryNoteForm2.DeliveryNote.code', array('label' => false))?></td>
					<th>Obchodník</th>
					<td><?php echo $this->Form->input('DeliveryNoteForm2.DeliveryNote.user_id', array('label' => false, 'empty' => true, 'options' => $delivery_notes_users))?></td>
					<th>Kód VZP</th>
					<td><?php echo $this->Form->input('DeliveryNoteForm2.Product.vzp_code', array('label' => false))?></td>
				</tr>
				<tr>
					<td colspan="6">
						<?php echo $html->link('reset filtru', $_SERVER['REQUEST_URI'] . '/reset:delivery_notes') ?>
					</td>
				</tr>
			</table>											
			<?php
				echo $form->hidden('DeliveryNoteForm2.DeliveryNote.search_form', array('value' => 1));
				echo $form->submit('Vyhledávat');
				echo $form->end();
			?>
		</div>
		
		<script>
			$("#search_form_show_delivery_note").click(function () {
				if ($('#search_form_delivery_note').css('display') == "none"){
					$("#search_form_delivery_note").show("slow");
				} else {
					$("#search_form_delivery_note").hide("slow");
				}
			});
			$(function() {
				var model = 'DeliveryNote';
				var dateFromId = model + 'Form2' + model + 'DateFrom';
				var dateToId = model + 'Form2' + model + 'DateTo';
				var dates = $('#' + dateFromId + ',#' + dateToId).datepicker({
					changeMonth: false,
					numberOfMonths: 1,
					onSelect: function( selectedDate ) {
						var option = this.id == dateFromId ? "minDate" : "maxDate",
							instance = $( this ).data( "datepicker" ),
							date = $.datepicker.parseDate(
								instance.settings.dateFormat ||
								$.datepicker._defaults.dateFormat,
								selectedDate, instance.settings );
						dates.not( this ).datepicker( "option", option, date );
					}
				});
			});
			$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
		</script>

		<?php 
		echo $form->create('CSV', array('url' => array('controller' => 'delivery_notes', 'action' => 'xls_export')));
		echo $form->hidden('data', array('value' => serialize($delivery_notes_find)));
		echo $form->hidden('fields', array('value' => serialize($delivery_notes_export_fields)));
		echo $form->submit('CSV');
		echo $form->end();
		?>
		
		<ul>
			<li><?php echo $this->Html->link('Přidat dodací list', array('controller' => 'delivery_notes', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
		</ul>
		
		<?php if (empty($delivery_notes)) { ?>
		<p><em>V systému nejsou žádné dodací listy.</em></p>
		<?php } else { 
			$paginator->options(array(
					'url' => array('tab' => 10, 0 => $business_partner['BusinessPartner']['id'])
			));
			
			$paginator->params['paging'] = $delivery_notes_paging;
			$paginator->__defaultModel = 'DeliveryNote'; 
		?>
		<table class="top_heading">
			<tr>
				<th><?php echo $this->Paginator->sort('Datum vys.', 'DeliveryNote.date')?></th>
				<th><?php echo $this->Paginator->sort('Číslo dokladu', 'DeliveryNote.code')?></th>
				<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
				<th><?php echo $this->Paginator->sort('Název zboží', 'Product.name')?></th>
				<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
				<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
				<th><?php echo $this->Paginator->sort('Mn.', 'ProductsTransaction.quantity')?></th>
				<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
				<th><?php echo $this->Paginator->sort('Kč/J', 'ProductVariantsTransaction.unit_price')?></th>
				<th><?php echo $this->Paginator->sort('Marže produktu', 'ProductVariantsTransaction.product_margin')?></th>
				<th><?php echo $this->Paginator->sort('Celkem', 'DeliveryNote.total_price')?></th>
				<th><?php echo $this->Paginator->sort('Marže', 'DeliveryNote.margin')?></th>
				<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
				<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
				<th>&nbsp;</th>
			</tr>
			<?php 
			$odd = '';
			foreach ($delivery_notes as $transaction) {
				$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
			?>
			<tr<?php echo $odd?>>
				<td><?php echo czech_date($transaction['DeliveryNote']['date'])?></td>
				<td><?php echo $this->Html->link($transaction['DeliveryNote']['code'], '/' . DL_FOLDER . $transaction['DeliveryNote']['id'] . '.pdf', array('target' => '_blank')); ?></td>
				<td><?php echo $transaction['BusinessPartner']['name']?></td>
				<td><?php echo $transaction['Product']['name']?></td>
				<td><?php echo $transaction['ProductVariant']['exp']?></td>
				<td><?php echo $transaction['ProductVariant']['lot']?></td>
				<td><?php echo $transaction['ProductVariantsTransaction']['quantity']?></td>
				<td><?php echo $transaction['Unit']['shortcut']?></td>
				<td><?php echo $transaction['ProductVariantsTransaction']['unit_price']?></td>
				<td><?php echo $transaction['ProductVariantsTransaction']['product_margin']?></td>
				<td><?php echo $transaction['DeliveryNote']['total_price']?></td>
				<td><?php echo $transaction['DeliveryNote']['margin']?></td>
				<td><?php echo $transaction['Product']['vzp_code']?></td>
				<td><?php echo $transaction['Product']['group_code']?></td>
				<td><?php 
					echo $this->Html->link('Upravit', array('controller' => 'delivery_notes', 'action' => 'edit', $transaction['DeliveryNote']['id'], 'business_partner_id' => $business_partner['BusinessPartner']['id'])) . ' | ';
					echo $this->Html->link('Smazat', array('controller' => 'delivery_notes', 'action' => 'delete', $transaction['DeliveryNote']['id'], 'business_partner_id' => $business_partner['BusinessPartner']['id']), array(), 'Opravdu chcete transakci smazat?') . ' | ';
					echo $this->Html->link('Smazat položku', array('controller' => 'products_variants_transactions', 'action' => 'delete', $transaction['ProductVariantsTransaction']['id'], 'business_partner_id' => $business_partner['BusinessPartner']['id']));
				?></td>
			</tr>
			<?php } ?>
		</table>
		<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
		<?php } ?>
		
	</div>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Sales/index')) { ?>
<?php /* TAB 11 ****************************************************************************************************************/ ?>
	<div id="tabs-11">
		<h2>Prodeje</h2>
		
		<button id="search_form_show_sale">vyhledávací formulář</button>
		<?php
			$hide = ' style="display:none"';
			if ( isset($this->data['SaleForm2']) ){
				$hide = '';
			}
		?>
		<div id="search_form_sale"<?php echo $hide?>>
			
			<?php echo $form->create('Sale', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 11))); ?>
			<table class="left_heading">
				<tr>
					<td colspan="6">Odběratel</td>
				</tr>
				<tr>
					<th>Jméno</th>
					<td><?php echo $form->input('SaleForm2.BusinessPartner.name', array('label' => false))?></td>
					<th>IČO</th>
					<td><?php echo $form->input('SaleForm2.BusinessPartner.ico', array('label' => false))?></td>
					<th>DIČ</th>
					<td><?php echo $form->input('SaleForm2.BusinessPartner.dic', array('label' => false))?></td>
				</tr>
				<tr>
					<th>Ulice</th>
					<td><?php echo $this->Form->input('SaleForm2.Address.street', array('label' => false))?></td>
					<th>Město</th>
					<td><?php echo $this->Form->input('SaleForm2.Address.city', array('label' => false))?></td>
					<th>Okres</th>
					<td><?php echo $this->Form->input('SaleForm2.Address.region', array('label' => false))?></td>
				</tr>
				<tr>
					<td colspan="4">Prodeje</td>
					<td colspan="2">Zboží</td>
				</tr>
				<tr>
					<th>Datum od</th>
					<td><?php echo $this->Form->input('SaleForm2.Sale.date_from', array('label' => false, 'type' => 'text'))?></td>
					<th>Datum do</th>
					<td><?php echo $this->Form->input('SaleForm2.Sale.date_to', array('label' => false, 'type' => 'text'))?></td>
					<th>Kód skupiny</th>
					<td><?php echo $this->Form->input('SaleForm2.Product.group_code', array('label' => false))?></td>
				</tr>
				<tr>
					<th>Číslo dokladu</th>
					<td><?php echo $this->Form->input('SaleForm2.Sale.code', array('label' => false))?></td>
					<th>Obchodník</th>
					<td><?php echo $this->Form->input('SaleForm2.Sale.user_id', array('label' => false, 'empty' => true, 'options' => $delivery_notes_users))?></td>
					<th>Kód VZP</th>
					<td><?php echo $this->Form->input('SaleForm2.Product.vzp_code', array('label' => false))?></td>
				</tr>
				<tr>
					<td colspan="6">
						<?php echo $html->link('reset filtru', $_SERVER['REQUEST_URI'] . '/reset:sales') ?>
					</td>
				</tr>
			</table>											
			<?php
				echo $form->hidden('SaleForm2.Sale.search_form', array('value' => 1));
				echo $form->submit('Vyhledávat');
				echo $form->end();
			?>
		</div>
		
		<script>
			$("#search_form_show_sale").click(function () {
				if ($('#search_form_sale').css('display') == "none"){
					$("#search_form_sale").show("slow");
				} else {
					$("#search_form_sale").hide("slow");
				}
			});
			$(function() {
				var model = 'Sale';
				var dateFromId = model + 'Form2' + model + 'DateFrom';
				var dateToId = model + 'Form2' + model + 'DateTo';
				var dates = $('#' + dateFromId + ',#' + dateToId).datepicker({
					changeMonth: false,
					numberOfMonths: 1,
					onSelect: function( selectedDate ) {
						var option = this.id == dateFromId ? "minDate" : "maxDate",
							instance = $( this ).data( "datepicker" ),
							date = $.datepicker.parseDate(
								instance.settings.dateFormat ||
								$.datepicker._defaults.dateFormat,
								selectedDate, instance.settings );
						dates.not( this ).datepicker( "option", option, date );
					}
				});
			});
			$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
		</script>

		<?php 
		echo $form->create('CSV', array('url' => array('controller' => 'sales', 'action' => 'xls_export')));
		echo $form->hidden('data', array('value' => serialize($sales_find)));
		echo $form->hidden('fields', array('value' => serialize($sales_export_fields)));
		echo $form->submit('CSV');
		echo $form->end();
		?>
		
		<ul>
			<li><?php echo $this->Html->link('Přidat prodej', array('controller' => 'sales', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
		</ul>
		
		<?php if (empty($sales)) { ?>
		<p><em>V systému nejsou žádné prodeje.</em></p>
		<?php } else { 
			$paginator->options(array(
				'url' => array('tab' => 11, 0 => $business_partner['BusinessPartner']['id'])
			));
			
			$paginator->params['paging'] = $sales_paging;
			$paginator->__defaultModel = 'Sale'; 
		?>
		<table class="top_heading">
			<tr>
				<th><?php echo $this->Paginator->sort('Datum vys.', 'Sale.date')?></th>
				<th><?php echo $this->Paginator->sort('Číslo dokladu', 'Sale.code')?></th>
				<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
				<th><?php echo $this->Paginator->sort('Název zboží', 'Product.name')?></th>
				<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
				<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
				<th><?php echo $this->Paginator->sort('Mn.', 'Sale.abs_quantity')?></th>
				<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
				<th><?php echo $this->Paginator->sort('Kč/J', 'ProductVariantsTransaction.unit_price')?></th>
				<th><?php echo $this->Paginator->sort('Marže produktu', 'ProductVariantsTransaction.product_margin')?></th>
				<th><?php echo $this->Paginator->sort('Celkem', 'Sale.abs_total_price')?></th>
				<th><?php echo $this->Paginator->sort('Marže', 'Sale.abs_margin')?></th>
				<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
				<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
				<th>&nbsp;</th>
			</tr>
			<?php
			$odd = '';
			foreach ($sales as $transaction) {
				$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
			?>
			<tr<?php echo $odd?>>
				<td><?php echo czech_date($transaction['Sale']['date'])?></td>
				<td><?php echo $transaction['Sale']['code']?></td>
				<td><?php echo $transaction['BusinessPartner']['name']?></td>
				<td><?php echo $transaction['Product']['name']?></td>
				<td><?php echo $transaction['ProductVariant']['exp']?></td>
				<td><?php echo $transaction['ProductVariant']['lot']?></td>
				<td><?php echo $transaction['Sale']['abs_quantity']?></td>
				<td><?php echo $transaction['Unit']['shortcut']?></td>
				<td><?php echo $transaction['ProductVariantsTransaction']['unit_price']?></td>
				<td><?php echo $transaction['ProductVariantsTransaction']['product_margin']?></td>
				<td><?php echo $transaction['Sale']['abs_total_price']?></td>
				<td><?php echo $transaction['Sale']['abs_margin']?></td>
				<td><?php echo $transaction['Product']['vzp_code']?></td>
				<td><?php echo $transaction['Product']['group_code']?></td>
				<td><?php
					echo $this->Html->link('Upravit', array('controller' => 'sales', 'action' => 'edit', $transaction['Sale']['id'], 'business_partner_id' => $business_partner['BusinessPartner']['id'])) . ' | ';
					echo $this->Html->link('Smazat', array('controller' => 'sales', 'action' => 'delete', $transaction['Sale']['id'], 'business_partner_id' => $business_partner['BusinessPartner']['id']), array(), 'Opravdu chcete transakci smazat?') . ' | ';
					echo $this->Html->link('Smazat položku', array('controller' => 'products_variants_transactions', 'action' => 'delete', $transaction['ProductVariantsTransaction']['id'], 'business_partner_id' => $business_partner['BusinessPartner']['id']));
				?></td>
			</tr>
			<?php } ?>
		</table>
		<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
		<?php } ?>
	</div>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Transactions/index')) { ?>
<?php /* TAB 12 ****************************************************************************************************************/ ?>
	<div id="tabs-12">
		<h2>Pohyby</h2>
	
		<button id="search_form_show_transaction">vyhledávací formulář</button>
		<?php
			$hide = ' style="display:none"';
			if ( isset($this->data['TransactionForm2']) ){
				$hide = '';
			}
		?>
		<div id="search_form_transaction"<?php echo $hide?>>
			
			<?php echo $form->create('Transaction', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 12))); ?>
			<table class="left_heading">
				<tr>
					<td colspan="6">Odběratel</td>
				</tr>
				<tr>
					<th>Jméno</th>
					<td><?php echo $form->input('TransactionForm2.BusinessPartner.name', array('label' => false))?></td>
					<th>IČO</th>
					<td><?php echo $form->input('TransactionForm2.BusinessPartner.ico', array('label' => false))?></td>
					<th>DIČ</th>
					<td><?php echo $form->input('TransactionForm2.BusinessPartner.dic', array('label' => false))?></td>
				</tr>
				<tr>
					<th>Ulice</th>
					<td><?php echo $this->Form->input('TransactionForm2.Address.street', array('label' => false))?></td>
					<th>Město</th>
					<td><?php echo $this->Form->input('TransactionForm2.Address.city', array('label' => false))?></td>
					<th>Okres</th>
					<td><?php echo $this->Form->input('TransactionForm2.Address.region', array('label' => false))?></td>
				</tr>
				<tr>
					<td colspan="4">Prodeje</td>
					<td colspan="2">Zboží</td>
				</tr>
				<tr>
					<th>Datum od</th>
					<td><?php echo $this->Form->input('TransactionForm2.Transaction.date_from', array('label' => false, 'type' => 'text'))?></td>
					<th>Datum do</th>
					<td><?php echo $this->Form->input('TransactionForm2.Transaction.date_to', array('label' => false, 'type' => 'text'))?></td>
					<th>Kód skupiny</th>
					<td><?php echo $this->Form->input('TransactionForm2.Product.group_code', array('label' => false))?></td>
				</tr>
				<tr>
					<th>Číslo dokladu</th>
					<td><?php echo $this->Form->input('TransactionForm2.Transaction.code', array('label' => false))?></td>
					<th>Obchodník</th>
					<td><?php echo $this->Form->input('TransactionForm2.Transaction.user_id', array('label' => false, 'empty' => true, 'options' => $delivery_notes_users))?></td>
					<th>Kód VZP</th>
					<td><?php echo $this->Form->input('TransactionForm2.Product.vzp_code', array('label' => false))?></td>
				</tr>
				<tr>
					<td colspan="6">
						<?php echo $html->link('reset filtru', $_SERVER['REQUEST_URI'] . '/reset:transactions') ?>
					</td>
				</tr>
			</table>											
			<?php
				echo $form->hidden('TransactionForm2.Transaction.search_form', array('value' => 1));
				echo $form->submit('Vyhledávat');
				echo $form->end();
			?>
		</div>
		
		<script>
			$("#search_form_show_transaction").click(function () {
				if ($('#search_form_transaction').css('display') == "none"){
					$("#search_form_transaction").show("slow");
				} else {
					$("#search_form_transaction").hide("slow");
				}
			});
			$(function() {
				var model = 'Transaction';
				var dateFromId = model + 'Form2' + model + 'DateFrom';
				var dateToId = model + 'Form2' + model + 'DateTo';
				var dates = $('#' + dateFromId + ',#' + dateToId).datepicker({
					changeMonth: false,
					numberOfMonths: 1,
					onSelect: function( selectedDate ) {
						var option = this.id == dateFromId ? "minDate" : "maxDate",
							instance = $( this ).data( "datepicker" ),
							date = $.datepicker.parseDate(
								instance.settings.dateFormat ||
								$.datepicker._defaults.dateFormat,
								selectedDate, instance.settings );
						dates.not( this ).datepicker( "option", option, date );
					}
				});
			});
			$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
		</script>

		<?php 
		echo $form->create('CSV', array('url' => array('controller' => 'transactions', 'action' => 'xls_export')));
		echo $form->hidden('data', array('value' => serialize($transactions_find)));
		echo $form->hidden('fields', array('value' => serialize($transactions_export_fields)));
		echo $form->submit('CSV');
		echo $form->end();

		if (empty($transactions)) { ?>
		<p><em>V systému nejsou žádné pohyby.</em></p>
		<?php } else { 
			$paginator->options(array(
				'url' => array('tab' => 12, 0 => $business_partner['BusinessPartner']['id'])
			));
			
			$paginator->params['paging'] = $transactions_paging;
			$paginator->__defaultModel = 'Transaction'; 
		?>
		<table class="top_heading">
			<tr>
				<th><?php echo $this->Paginator->sort('Datum vys.', 'Transaction.date')?></th>
				<th><?php echo $this->Paginator->sort('Číslo dokladu', 'Transaction.code')?></th>
				<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
				<th><?php echo $this->Paginator->sort('Název zboží', 'Product.name')?></th>
				<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
				<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
				<th><?php echo $this->Paginator->sort('Mn.', 'Transaction.quantity')?></th>
				<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
				<th><?php echo $this->Paginator->sort('Kč/J', 'ProductVariantsTransaction.unit_price')?></th>
				<th><?php echo $this->Paginator->sort('Marže produktu', 'ProductVariantsTransaction.product_margin')?></th>
				<th><?php echo $this->Paginator->sort('Celkem', 'Transaction.total_price')?></th>
				<th><?php echo $this->Paginator->sort('Marže', 'Transaction.margin')?></th>
				<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
				<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
				<th>&nbsp;</th>
			</tr>
			<?php
			$odd = '';
			foreach ($transactions as $transaction) {
				$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
			?>
			<tr<?php echo $odd?>>
				<td><?php echo czech_date($transaction['Transaction']['date'])?></td>
				<td><?php
				if ($transaction['TransactionType']['id'] == 1) {
					echo $this->Html->link($transaction['Transaction']['code'], '/' . DL_FOLDER . $transaction['Transaction']['id'] . '.pdf', array('target' => '_blank'));
				} else {
					echo $transaction['Transaction']['code'];
				} ?></td>
				<td><?php echo $transaction['BusinessPartner']['name']?></td>
				<td><?php echo $transaction['Product']['name']?></td>
				<td><?php echo $transaction['ProductVariant']['exp']?></td>
				<td><?php echo $transaction['ProductVariant']['lot']?></td>
				<td><?php echo $transaction['Transaction']['quantity']?></td>
				<td><?php echo $transaction['Unit']['shortcut']?></td>
				<td><?php echo $transaction['ProductVariantsTransaction']['unit_price']?></td>
				<td><?php echO $transaction['ProductVariantsTransaction']['product_margin']?></td>
				<td><?php echo $transaction['Transaction']['total_price']?></td>
				<td><?php echo $transaction['Transaction']['margin']?></td>
				<td><?php echo $transaction['Product']['vzp_code']?></td>
				<td><?php echo $transaction['Product']['group_code']?></td>
				<td><?php
					echo $this->Html->link('Upravit', array('controller' => 'transactions', 'action' => 'edit', $transaction['Transaction']['id'], 'business_partner_id' => $business_partner['BusinessPartner']['id'])) . ' | ';
					echo $this->Html->link('Smazat', array('controller' => 'transactions', 'action' => 'delete', $transaction['Transaction']['id'], 'business_partner_id' => $business_partner['BusinessPartner']['id']), array(), 'Opravdu chcete transakci smazat?') . ' | ';
					echo $this->Html->link('Smazat položku', array('controller' => 'products_variants_transactions', 'action' => 'delete', $transaction['ProductVariantsTransaction']['id'], 'business_partner_id' => $business_partner['BusinessPartner']['id']));
				?></td>
			</tr>
			<?php } ?>
		</table>
		<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
		<?php } ?>
	</div>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSStorings/index')) { ?>
<?php /* TAB 17 CS Naskladneni ****************************************************************************************************************/ ?>
	<div id="tabs-17">
		<h2>CS Naskladnění</h2>
		<button id="search_form_show_c_s_storings">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/c_s_storings', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 17)));
		
			echo $form->create('CSV', array('url' => array('controller' => 'c_s_storings', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($c_s_storings_find)));
			echo $form->hidden('fields', array('value' => serialize($c_s_storings_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		
			if (empty($c_s_storings)) { ?>
		<p><em>V systému nejsou žádné naskladnění.</em></p>
			<?php } else {
				$paginator->options(array(
					'url' => array('tab' => 17, 0 => $business_partner['BusinessPartner']['id'])
				));
					
				$paginator->params['paging'] = $c_s_storings_paging;
				$paginator->__defaultModel = 'CSStoring';
		
				echo $this->element('c_s_storings/index_table');
			} ?>
		
		<script>
			$("#search_form_show_c_s_storings").click(function () {
				if ($('#search_form_c_s_storings').css('display') == "none"){
					$("#search_form_c_s_storings").show("slow");
				} else {
					$("#search_form_c_s_storings").hide("slow");
				}
			});
		</script>
	</div>
<?php } // konec bloku, ktery se zobrazi jen v pripade, ze uzivatel ma pravo pro zobrazeni faktur v centralnim sklade?>

<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSInvoices/index')) { ?>
<?php /* TAB 14 CS Faktury ****************************************************************************************************************/ ?>
	<div id="tabs-14">
		<h2>CS Faktury</h2>
		<button id="search_form_show_c_s_invoices">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/c_s_invoices', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 14)));
		
			echo $form->create('CSV', array('url' => array('controller' => 'c_s_invoices', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($c_s_invoices_find)));
			echo $form->hidden('fields', array('value' => serialize($c_s_invoices_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		
			if (empty($c_s_invoices)) { ?>
		<p><em>V systému nejsou žádné faktury.</em></p>
			<?php } else {
				$paginator->options(array(
					'url' => array('tab' => 14, 0 => $business_partner['BusinessPartner']['id'])
				));
					
				$paginator->params['paging'] = $c_s_invoices_paging;
				$paginator->__defaultModel = 'CSInvoice';
		
				echo $this->element('c_s_invoices/index_table');
			} ?>
		
		<script>
			$("#search_form_show_c_s_invoices").click(function () {
				if ($('#search_form_c_s_invoices').css('display') == "none"){
					$("#search_form_c_s_invoices").show("slow");
				} else {
					$("#search_form_c_s_invoices").hide("slow");
				}
			});
		</script>
	</div>
<?php } // konec bloku, ktery se zobrazi jen v pripade, ze uzivatel ma pravo pro zobrazeni faktur v centralnim sklade?>

<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSCreditNotes/index')) { ?>
<?php /* TAB 15 CS Dobropisy ****************************************************************************************************************/ ?>
	<div id="tabs-15">
		<h2>CS Dobropisy</h2>
		<button id="search_form_show_c_s_credit_notes">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/c_s_credit_notes', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 15)));
			
			echo $form->create('CSV', array('url' => array('controller' => 'c_s_credit_notes', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($c_s_credit_notes_find)));
			echo $form->hidden('fields', array('value' => serialize($c_s_credit_notes_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();

			if (empty($c_s_credit_notes)) { ?>
		<p><em>V systému nejsou žádné dobropisy.</em></p>
		<?php } else {
			$paginator->options(array(
				'url' => array('tab' => 15, 0 => $business_partner['BusinessPartner']['id'])
			));
				
			$paginator->params['paging'] = $c_s_credit_notes_paging;
			$paginator->__defaultModel = 'CSCreditNote';
			
			echo $this->element('c_s_credit_notes/index_table');
		} ?>
		<script>
			$("#search_form_show_c_s_credit_notes").click(function () {
				if ($('#search_form_c_s_credit_notes').css('display') == "none"){
					$("#search_form_c_s_credit_notes").show("slow");
				} else {
					$("#search_form_c_s_credit_notes").hide("slow");
				}
			});
		</script>
	</div>
<?php } // konec bloku, ktery se zobrazi pouze uzivateli s opravneni pro vypis dobropisu?>

<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSTransactions/index')) { ?>
<?php /* TAB 16 CS Pohyby ****************************************************************************************************************/ ?>
	<div id="tabs-16">
		<h2>CS Pohyby</h2>
		<button id="search_form_show_c_s_transactions">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/c_s_transactions', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 16)));
			echo $form->create('CSV', array('url' => array('controller' => 'c_s_transactions', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($c_s_transactions_find)));
			echo $form->hidden('fields', array('value' => serialize($c_s_transactions_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();

			if (empty($c_s_transactions)) { ?>
		<p><em>V systému nejsou žádné pohyby.</em></p>
		<?php } else {
			$paginator->options(array(
				'url' => array('tab' => 16, 0 => $business_partner['BusinessPartner']['id'])
			));
				
			$paginator->params['paging'] = $c_s_transactions_paging;
			$paginator->__defaultModel = 'CSTransaction';
			
			echo $this->element('c_s_transactions/index_table');
		} ?>
		<script>
			$("#search_form_show_c_s_transactions").click(function () {
				if ($('#search_form_c_s_transactions').css('display') == "none"){
					$("#search_form_c_s_transactions").show("slow");
				} else {
					$("#search_form_c_s_transactions").hide("slow");
				}
			});
		</script>
	</div>	
<?php } // konec bloku, ktery se zobrazi jen uzivatelum s pravem pro vypis pohybu na centralnim sklade?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepSales/index')) { ?>
<?php /* TAB 21 BC REP nakupy - nakupy obchodniho partnera od mea repu ****************************************************************************************************************/ ?>
	<div id="tabs-21">
		<h2>Nákupy od Mea repů</h2>
		<button id="search_form_show_b_p_c_s_rep_sales">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/b_p_c_s_rep_sales', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 21)));
			echo $form->create('CSV', array('url' => array('controller' => 'b_p_c_s_rep_sales', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($b_p_rep_sales_find)));
			echo $form->hidden('fields', array('value' => serialize($b_p_rep_sales_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();

			if (empty($b_p_c_s_rep_sales)) { ?>
		<p><em>V systému nejsou žádné nákupy.</em></p>
		<?php } else {
			$paginator->options(array(
				'url' => array('tab' => 21, 0 => $business_partner['BusinessPartner']['id'])
			));
				
			$paginator->params['paging'] = $b_p_c_s_rep_sales_paging;
			$paginator->__defaultModel = 'BPCSRepSale';
			
			echo $this->element('b_p_c_s_rep_sales/index_table');
		} ?>
		<script>
			$("#search_form_show_b_p_c_s_rep_sales").click(function () {
				if ($('#search_form_b_p_c_s_rep_sales').css('display') == "none"){
					$("#search_form_b_p_c_s_rep_sales").show("slow");
				} else {
					$("#search_form_b_p_c_s_rep_sales").hide("slow");
				}
			});
		</script>
	</div>	
<?php } // konec bloku, ktery se zobrazi jen uzivatelum s pravem pro vypis pohybu na centralnim sklade?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPCSRepPurchases/index')) { ?>
<?php /* TAB 22 BC REP prodeje - prodeje obchodniho partnera mea repům ****************************************************************************************************************/ ?>
	<div id="tabs-22">
		<h2>Prodeje Mea repům</h2>
		<button id="search_form_show_b_p_c_s_rep_purchases">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/b_p_c_s_rep_purchases', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 22)));
			echo $form->create('CSV', array('url' => array('controller' => 'b_p_c_s_rep_purchases', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($b_p_c_s_rep_purchases_find)));
			echo $form->hidden('fields', array('value' => serialize($b_p_c_s_rep_purchases_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();

			if (empty($b_p_c_s_rep_purchases)) { ?>
		<p><em>V systému nejsou žádné prodeje.</em></p>
		<?php } else {
			$paginator->options(array(
				'url' => array('tab' => 22, 0 => $business_partner['BusinessPartner']['id'])
			));
				
			$paginator->params['paging'] = $b_p_c_s_rep_purchases_paging;
			$paginator->__defaultModel = 'BPCSRepPurchase';
			
			echo $this->element('b_p_c_s_rep_purchases/index_table');
		} ?>
		<script>
			$("#search_form_show_b_p_c_s_rep_purchases").click(function () {
				if ($('#search_form_b_p_c_s_rep_purchases').css('display') == "none"){
					$("#search_form_b_p_c_s_rep_purchases").show("slow");
				} else {
					$("#search_form_b_p_c_s_rep_purchases").hide("slow");
				}
			});
		</script>
	</div>	
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSRepTransactions/index')) { ?>
<?php /* TAB 23 BC REP transakce - transakce mezi obchodnim partnerem a repy ****************************************************************************************************************/ ?>
	<div id="tabs-23">
		<h2>Transakce s Mea repy</h2>
		<?php
			echo $this->element('search_forms/c_s_rep_transactions', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 23)));
			echo $form->create('CSV', array('url' => array('controller' => 'c_s_rep_transactions', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($c_s_rep_transactions_find)));
			echo $form->hidden('fields', array('value' => serialize($c_s_rep_transactions_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();

			if (empty($c_s_rep_transactions)) { ?>
		<p><em>V systému nejsou žádné transakce.</em></p>
		<?php } else {
			$paginator->options(array(
				'url' => array('tab' => 23, 0 => $business_partner['BusinessPartner']['id'])
			));
				
			$paginator->params['paging'] = $c_s_rep_transactions_paging;
			$paginator->__defaultModel = 'CSRepTransaction';
			
			echo $this->element('c_s_rep_transactions/index_table');
		} ?>
	</div>	
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCStorings/index')) { ?>
<?php /* TAB 24 MC Naskladneni ****************************************************************************************************************/ ?>
	<div id="tabs-24">
		<h2>CS Naskladnění</h2>
		<button id="search_form_show_m_c_storings">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/m_c_storings', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 24)));
		
			echo $form->create('CSV', array('url' => array('controller' => 'm_c_storings', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($m_c_storings_find)));
			echo $form->hidden('fields', array('value' => serialize($m_c_storings_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		
			if (empty($m_c_storings)) { ?>
		<p><em>V systému nejsou žádné naskladnění.</em></p>
			<?php } else {
				$paginator->options(array(
					'url' => array('tab' => 24, 0 => $business_partner['BusinessPartner']['id'])
				));
					
				$paginator->params['paging'] = $m_c_storings_paging;
				$paginator->__defaultModel = 'MCStoring';
		
				echo $this->element('m_c_storings/index_table');
			} ?>
		
		<script>
			$("#search_form_show_m_c_storings").click(function () {
				if ($('#search_form_m_c_storings').css('display') == "none"){
					$("#search_form_m_c_storings").show("slow");
				} else {
					$("#search_form_m_c_storings").hide("slow");
				}
			});
		</script>
	</div>
<?php } // konec bloku, ktery se zobrazi jen v pripade, ze uzivatel ma pravo pro zobrazeni faktur v centralnim sklade?>

<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCInvoices/index')) { ?>
<?php /* TAB 25 MC Faktury ****************************************************************************************************************/ ?>
	<div id="tabs-25">
		<h2>MC Faktury</h2>
		<button id="search_form_show_m_c_invoices">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/m_c_invoices', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 25)));
		
			echo $form->create('CSV', array('url' => array('controller' => 'm_c_invoices', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($m_c_invoices_find)));
			echo $form->hidden('fields', array('value' => serialize($m_c_invoices_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		
			if (empty($m_c_invoices)) { ?>
		<p><em>V systému nejsou žádné faktury.</em></p>
			<?php } else {
				$paginator->options(array(
					'url' => array('tab' => 25, 0 => $business_partner['BusinessPartner']['id'])
				));
					
				$paginator->params['paging'] = $m_c_invoices_paging;
				$paginator->__defaultModel = 'MCInvoice';
		
				echo $this->element('m_c_invoices/index_table');
			} ?>
		
		<script>
			$("#search_form_show_m_c_invoices").click(function () {
				if ($('#search_form_m_c_invoices').css('display') == "none"){
					$("#search_form_m_c_invoices").show("slow");
				} else {
					$("#search_form_m_c_invoices").hide("slow");
				}
			});
		</script>
	</div>
<?php } // konec bloku, ktery se zobrazi jen v pripade, ze uzivatel ma pravo pro zobrazeni faktur v centralnim sklade?>

<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCCreditNotes/index')) { ?>
<?php /* TAB 26 MC Dobropisy ****************************************************************************************************************/ ?>
	<div id="tabs-26">
		<h2>MC Dobropisy</h2>
		<button id="search_form_show_m_c_credit_notes">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/m_c_credit_notes', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 26)));
			
			echo $form->create('CSV', array('url' => array('controller' => 'm_c_credit_notes', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($m_c_credit_notes_find)));
			echo $form->hidden('fields', array('value' => serialize($m_c_credit_notes_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();

			if (empty($m_c_credit_notes)) { ?>
		<p><em>V systému nejsou žádné dobropisy.</em></p>
		<?php } else {
			$paginator->options(array(
				'url' => array('tab' => 26, 0 => $business_partner['BusinessPartner']['id'])
			));
				
			$paginator->params['paging'] = $m_c_credit_notes_paging;
			$paginator->__defaultModel = 'MCCreditNote';
			
			echo $this->element('m_c_credit_notes/index_table');
		} ?>
		<script>
			$("#search_form_show_m_c_credit_notes").click(function () {
				if ($('#search_form_m_c_credit_notes').css('display') == "none"){
					$("#search_form_m_c_credit_notes").show("slow");
				} else {
					$("#search_form_m_c_credit_notes").hide("slow");
				}
			});
		</script>
	</div>
<?php } // konec bloku, ktery se zobrazi pouze uzivateli s opravneni pro vypis dobropisu?>

<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/MCTransactions/index')) { ?>
<?php /* TAB 27 CS Pohyby ****************************************************************************************************************/ ?>
	<div id="tabs-27">
		<h2>MC Pohyby</h2>
		<button id="search_form_show_m_c_transactions">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/m_c_transactions', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 27)));
			echo $form->create('CSV', array('url' => array('controller' => 'm_c_transactions', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($m_c_transactions_find)));
			echo $form->hidden('fields', array('value' => serialize($m_c_transactions_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();

			if (empty($m_c_transactions)) { ?>
		<p><em>V systému nejsou žádné pohyby.</em></p>
		<?php } else {
			$paginator->options(array(
				'url' => array('tab' => 16, 0 => $business_partner['BusinessPartner']['id'])
			));
				
			$paginator->params['paging'] = $m_c_transactions_paging;
			$paginator->__defaultModel = 'MCTransaction';
			
			echo $this->element('m_c_transactions/index_table');
		} ?>
		<script>
			$("#search_form_show_m_c_transactions").click(function () {
				if ($('#search_form_m_c_transactions').css('display') == "none"){
					$("#search_form_m_c_transactions").show("slow");
				} else {
					$("#search_form_m_c_transactions").hide("slow");
				}
			});
		</script>
	</div>	
<?php } // konec bloku, ktery se zobrazi jen uzivatelum s pravem pro vypis pohybu na centralnim sklade?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepSales/index')) { ?>
<?php /* TAB 18 BC REP nakupy - nakupy obchodniho partnera od repu ****************************************************************************************************************/ ?>
	<div id="tabs-18">
		<h2>Nákupy od repů</h2>
		<button id="search_form_show_b_p_rep_sales">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/b_p_rep_sales', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 18)));
			echo $form->create('CSV', array('url' => array('controller' => 'b_p_rep_sales', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($b_p_rep_sales_find)));
			echo $form->hidden('fields', array('value' => serialize($b_p_rep_sales_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();

			if (empty($b_p_rep_sales)) { ?>
		<p><em>V systému nejsou žádné nákupy.</em></p>
		<?php } else {
			$paginator->options(array(
				'url' => array('tab' => 18, 0 => $business_partner['BusinessPartner']['id'])
			));
				
			$paginator->params['paging'] = $b_p_rep_sales_paging;
			$paginator->__defaultModel = 'BPRepSale';
			
			echo $this->element('b_p_rep_sales/index_table');
		} ?>
		<script>
			$("#search_form_show_b_p_rep_sales").click(function () {
				if ($('#search_form_b_p_rep_sales').css('display') == "none"){
					$("#search_form_b_p_rep_sales").show("slow");
				} else {
					$("#search_form_b_p_rep_sales").hide("slow");
				}
			});
		</script>
	</div>	
<?php } // konec bloku, ktery se zobrazi jen uzivatelum s pravem pro vypis pohybu na centralnim sklade?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BPRepPurchases/index')) { ?>
<?php /* TAB 19 BC REP prodeje - prodeje obchodniho partnera repům ****************************************************************************************************************/ ?>
	<div id="tabs-19">
		<h2>Prodeje repům</h2>
		<button id="search_form_show_b_p_rep_purchases">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/b_p_rep_purchases', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 19)));
			echo $form->create('CSV', array('url' => array('controller' => 'b_p_rep_purchases', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($b_p_rep_purchases_find)));
			echo $form->hidden('fields', array('value' => serialize($b_p_rep_purchases_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();

			if (empty($b_p_rep_purchases)) { ?>
		<p><em>V systému nejsou žádné nákupy.</em></p>
		<?php } else {
			$paginator->options(array(
				'url' => array('tab' => 19, 0 => $business_partner['BusinessPartner']['id'])
			));
				
			$paginator->params['paging'] = $b_p_rep_purchases_paging;
			$paginator->__defaultModel = 'BPRepPurchase';
			
			echo $this->element('b_p_rep_purchases/index_table');
		} ?>
		<script>
			$("#search_form_show_b_p_rep_purchases").click(function () {
				if ($('#search_form_b_p_rep_purchases').css('display') == "none"){
					$("#search_form_b_p_rep_purchases").show("slow");
				} else {
					$("#search_form_b_p_rep_purchases").hide("slow");
				}
			});
		</script>
	</div>	
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/RepTransactions/index')) { ?>
<?php /* TAB 20 BC REP transakce - transakce mezi obchodnim partnerem a repy ****************************************************************************************************************/ ?>
	<div id="tabs-20">
		<h2>Transakce s repy</h2>
		<?php
			echo $this->element('search_forms/rep_transactions', array('url' => array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 20)));
			echo $form->create('CSV', array('url' => array('controller' => 'rep_transactions', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($rep_transactions_find)));
			echo $form->hidden('fields', array('value' => serialize($rep_transactions_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();

			if (empty($rep_transactions)) { ?>
		<p><em>V systému nejsou žádné nákupy.</em></p>
		<?php } else {
			$paginator->options(array(
				'url' => array('tab' => 20, 0 => $business_partner['BusinessPartner']['id'])
			));
				
			$paginator->params['paging'] = $rep_transactions_paging;
			$paginator->__defaultModel = 'RepTransaction';
			
			echo $this->element('rep_transactions/index_table');
		} ?>
	</div>	
<?php } ?>
<?php /* TAB 13 ****************************************************************************************************************/ ?>
	<div id="tabs-13">
		<h2>Poznámky</h2>
		<?php echo $this->Form->create('BusinessPartnerNote', array('action' => 'add'))?>
		<table>
			<tr>
				<td><?php echo $this->Form->input('BusinessPartnerNote.text', array('label' => false, 'cols' => 70, 'rows' => 5))?></td>
				<td>
					<?php echo $this->Form->hidden('BusinessPartnerNote.business_partner_id', array('value' => $business_partner['BusinessPartner']['id']))?>
					<?php echo $this->Form->submit('Uložit')?>
				</td>
			</tr>
		</table>
		<?php echo $this->Form->end()?>
		
		<?php if (empty($business_partner_notes)) { ?>
		<p><em>žádné poznánky</em></p>
		<?php } else { ?>
			<table>
			<?php foreach ($business_partner_notes as $note) {?>
			<tr>
				<td><?php echo $note['BusinessPartnerNote']['created']?></td>
				<td><?php echo $note['BusinessPartnerNote']['text']?></td>
				<td><?php 
					echo $this->Html->link('Upravit', array('controller' => 'business_partner_notes', 'action' => 'edit', $note['BusinessPartnerNote']['id'])) . ' | ';
					echo $this->Html->link('Smazat', array('controller' => 'business_partner_notes', 'action' => 'delete', $note['BusinessPartnerNote']['id']), null, 'Opravdu chcete poznámku odstranit?');
				?></td>
			</tr>
			<?php } ?>
			</table>
		<?php } ?>
	</div>
</div>