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

<h1><?php echo $rep['Rep']['name']?></h1>

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Info</a></li>
		<li><a href="#tabs-2">Peněženka</a></li>
		<li><a href="#tabs-3">Sklad</a></li>
		<li><a href="#tabs-4">Nákupy</a></li>
		<li><a href="#tabs-6">Prodeje</a></li>
		<li><a href="#tabs-5">Převod z MC</a></li>
		<li><a href="#tabs-7">Převod do MC</a></li>
		<li><a href="#tabs-8">Všechny pohyby</a></li>
	</ul>
	
	<?php /* TAB 1 ****************************************************************************************************************/ ?>
	<div id="tabs-1">
		<h2>Základní informace</h2>
		<?php
			echo $form->create('Rep', array('url' => array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id'], 'tab' => 1)));
			echo $this->element('reps/add_edit_table');
			echo $form->hidden('Rep.id');
			echo $this->Form->hidden('Rep.edit_rep_form', array('value' => true));
			echo $form->submit('Uložit');
			echo $form->end();
		?>
	</div>
	
	<?php /* TAB 2 ****************************************************************************************************************/ ?>
	<div id="tabs-2">
		<h2>Transakce v peněžence</h2>
		<button id="search_form_show_wallet_transactions">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/wallet_transactions', array('url' => array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id'], 'tab' => 2)));
		
			echo $form->create('CSV', array('url' => array('controller' => 'wallet_transactions', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($wallet_transactions_find)));
			echo $form->hidden('fields', array('value' => serialize($wallet_transactions_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		
			if (empty($wallet_transactions)) { ?>
		<p><em>V systému nejsou žádné transakce v peněžence.</em></p>
			<?php } else {
				$paginator->options(array(
					'url' => array('tab' => 2, 0 => $rep['Rep']['id'])
				));
					
				$paginator->params['paging'] = $wallet_transactions_paging;
				$paginator->__defaultModel = 'WalletTransaction';
		
				echo $this->element('wallet_transactions/index_table');
			} ?>
		
		<script>
			$("#search_form_show_wallet_transactions").click(function () {
				if ($('#search_form_wallet_transactions').css('display') == "none"){
					$("#search_form_wallet_transactions").show("slow");
				} else {
					$("#search_form_wallet_transactions").hide("slow");
				}
			});
		</script>
	</div>
	<?php /* TAB 3 ****************************************************************************************************************/ ?>
	<div id="tabs-3">
		<h2>Sklad</h2>
		<button id="search_form_show_rep_store_items">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/rep_store_items', array('url' => array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id'], 'tab' => 3)));
		
			echo $form->create('CSV', array('url' => array('controller' => 'rep_store_items', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($rep_store_items_find)));
			echo $form->hidden('fields', array('value' => serialize($rep_store_items_export_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		?>
		
		<?php if (empty($rep_store_items)) { ?>
		<p><em>Sklad je prázdný</em></p>
		<?php } else {
			$paginator->options(array(
				'url' => array('tab' => 3, 0 => $rep['Rep']['id'])
			));
					
			$paginator->params['paging'] = $rep_store_items_paging;
			$paginator->__defaultModel = 'RepStoreItem';
			echo $this->element('rep_store_items/index_table');
		} ?>
		
		<script>
			$("#search_form_show_rep_store_items").click(function () {
				if ($('#search_form_rep_store_items').css('display') == "none"){
					$("#search_form_rep_store_items").show("slow");
				} else {
					$("#search_form_rep_store_items").hide("slow");
				}
			});
		</script>
	</div>
	<?php /* TAB 4 ****************************************************************************************************************/ ?>
	<div id="tabs-4">
		<h2>Nákupy</h2>
		<button id="search_form_show_b_p_rep_purchases">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/b_p_rep_purchases', array('url' => array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id'], 'tab' => 4)));
		
			echo $form->create('CSV', array('url' => array('controller' => 'b_p_rep_purchases', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($b_p_rep_purchases_find)));
			echo $form->hidden('fields', array('value' => serialize($b_p_rep_purchases_export_fields)));
			echo $this->Form->hidden('virtual_fields', array('value' => serialize($b_p_rep_purchases_virtual_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		?>
		
		<?php if (empty($b_p_rep_purchases)) { ?>
		<p><em>V systému nejsou žádné nákupy.</em></p>
		<?php } else { ?>
			<?php echo $this->element('b_p_rep_purchases/index_table')?>
		
		<?php } ?>
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
	<?php /* TAB 6 ****************************************************************************************************************/ ?>
	<div id="tabs-6">
		<h2>Prodeje</h2>
		<button id="search_form_show_b_p_rep_sales">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/b_p_rep_sales', array('url' => array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id'], 'tab' => 6)));
		
			echo $form->create('CSV', array('url' => array('controller' => 'b_p_rep_sales', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($b_p_rep_sales_find)));
			echo $form->hidden('fields', array('value' => serialize($b_p_rep_sales_export_fields)));
			echo $this->Form->hidden('virtual_fields', array('value' => serialize($b_p_rep_sales_virtual_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		?>
		
		<?php if (empty($b_p_rep_sales)) { ?>
		<p><em>V systému nejsou žádné prodeje.</em></p>
		<?php } else { ?>
			<?php echo $this->element('b_p_rep_sales/index_table')?>
		
		<?php } ?>
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
	<?php /* TAB 5 ****************************************************************************************************************/ ?>
	<div id="tabs-5">
		<h2>Převody z MC</h2>
		<button id="search_form_show_m_c_rep_sales">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/m_c_rep_sales', array('url' => array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id'], 'tab' => 5)));
		
			echo $form->create('CSV', array('url' => array('controller' => 'm_c_rep_sales', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($m_c_rep_sales_find)));
			echo $form->hidden('fields', array('value' => serialize($m_c_rep_sales_export_fields)));
			echo $this->Form->hidden('virtual_fields', array('value' => serialize($m_c_rep_sales_virtual_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		?>
		
		<?php if (empty($m_c_rep_sales)) { ?>
		<p><em>V systému nejsou žádné převody.</em></p>
		<?php } else { ?>
			<?php echo $this->element('m_c_rep_sales/index_table')?>
		
		<?php } ?>
		<script>
			$("#search_form_show_m_c_rep_sales").click(function () {
				if ($('#search_form_m_c_rep_sales').css('display') == "none"){
					$("#search_form_m_c_rep_sales").show("slow");
				} else {
					$("#search_form_m_c_rep_sales").hide("slow");
				}
			});
		</script>
	</div>
	<?php /* TAB 7 ****************************************************************************************************************/ ?>
	<div id="tabs-7">
		<h2>Převody do MC</h2>
		<button id="search_form_show_m_c_rep_purchases">vyhledávací formulář</button>
		<?php
			echo $this->element('search_forms/m_c_rep_purchases', array('url' => array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id'], 'tab' => 7)));
		
			echo $form->create('CSV', array('url' => array('controller' => 'm_c_rep_purchases', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($m_c_rep_purchases_find)));
			echo $form->hidden('fields', array('value' => serialize($m_c_rep_purchases_export_fields)));
			echo $this->Form->hidden('virtual_fields', array('value' => serialize($m_c_rep_purchases_virtual_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		?>
		
		<?php if (empty($m_c_rep_purchases)) { ?>
		<p><em>V systému nejsou žádné převody.</em></p>
		<?php } else { ?>
			<?php echo $this->element('m_c_rep_purchases/index_table')?>
		
		<?php } ?>
		<script>
			$("#search_form_show_m_c_rep_purchases").click(function () {
				if ($('#search_form_m_c_rep_purchases').css('display') == "none"){
					$("#search_form_m_c_rep_purchases").show("slow");
				} else {
					$("#search_form_m_c_rep_purchases").hide("slow");
				}
			});
		</script>
	</div>
		<?php /* TAB 8 ****************************************************************************************************************/ ?>
	<div id="tabs-8">
		<h2>Pohyby na skladu repa</h2>
		<?php
			echo $this->element('search_forms/rep_transactions', array('url' => array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id'], 'tab' => 8)));
		
			echo $form->create('CSV', array('url' => array('controller' => 'rep_transactions', 'action' => 'xls_export')));
			echo $form->hidden('data', array('value' => serialize($rep_transactions_find)));
			echo $form->hidden('fields', array('value' => serialize($rep_transactions_export_fields)));
			echo $this->Form->hidden('virtual_fields', array('value' => serialize($rep_transactions_virtual_fields)));
			echo $form->submit('CSV');
			echo $form->end();
		?>
		
		<?php if (empty($rep_transactions)) { ?>
		<p><em>V systému nejsou žádné převody.</em></p>
		<?php } else { ?>
			<?php echo $this->element('rep_transactions/index_table')?>
		
		<?php } ?>
	</div>
</div>