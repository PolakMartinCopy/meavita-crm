<h1>Sklad Meavita</h1>

<button id="search_form_show_meavita_product_variants">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['ProductVariantMeavitaStoringSearch']) ){
		$hide = '';
	}
?>
<div id="search_form_meavita_product_variants"<?php echo $hide?>>
	<?php echo $form->create('ProductVariant', array('url' => array('controller' => 'product_variants', 'action' => 'meavita_index'))); ?>
	<table class="left_heading">
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('ProductVariantMeavitaStoringSearch.Product.name', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $form->input('ProductVariantMeavitaStoringSearch.Product.vzp_code', array('label' => false))?></td>
			<th>Kód skupiny</th>
			<td><?php echo $form->input('ProductVariantMeavitaStoringSearch.Product.group_code', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('ProductVariantMeavitaStoringSearch.Product.referential_number', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('ProductVariantMeavitaStoringSearch.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('ProductVariantMeavitaStoringSearch.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', array('controller' => 'product_variants', 'action' => 'meavita_index', 'reset' => 'meavita_product_variants')) ?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('ProductVariantMeavitaStoringSearch.ProductVariant.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script>
	$("#search_form_show_meavita_product_variants").click(function () {
		if ($('#search_form_meavita_product_variants').css('display') == "none"){
			$("#search_form_meavita_product_variants").show("slow");
		} else {
			$("#search_form_meavita_product_variants").hide("slow");
		}
	});
</script>

<?php
echo $form->create('CSV', array('url' => array('controller' => 'product_variants', 'action' => 'xls_export')));
echo $form->hidden('data', array('value' => serialize($find)));
echo $form->hidden('fields', array('value' => serialize($export_fields)));
echo $form->submit('CSV');
echo $form->end();

if (empty($product_variants)) { ?>
<p><em>Seznam zboží je prázdný.</em></p>
<?php } else { 
	$this->Paginator->options(array('escape' => false));
	
	$meavita_quantity = 0;
	$meavita_reserved_quantity = 0;
	$meavita_future_quantity = 0;
?>
<table class="top_heading">
	<tbody>
		<tr>
			<th><?php echo $this->Paginator->sort('Kód VZP', 'Product.vzp_code')?></th>
			<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
			<th><?php echo $this->Paginator->sort('Referenční číslo', 'Product.referential_number')?></th>
			<th><?php echo $this->Paginator->sort('Název', 'Product.name')?></th>
			<th><?php echo $this->Paginator->sort('Název anglicky', 'Product.en_name')?></th>
			<th><?php echo $this->Paginator->sort('Jednotka', 'Unit.name')?></th>
			<th><?php echo $this->Paginator->sort('Cena', 'ProductVariant.meavita_price')?></th>
			<th><?php echo $this->Paginator->sort('<abbr title="Množství na skladě">Mn.</abbr>', 'ProductVariant.meavita_quantity', array('escape' => false))?></th>
			<th><?php echo $this->Paginator->sort('<abbr title="Množství, které žádají repové ze skladu">Mn. rezervováno</abbr>', 'ProductVariant.meavita_reserved_quantity', array('escape' => false))?></th>
			<th><?php echo $this->Paginator->sort('<abbr title="Množství, které přivezou repové do skladu">Mn. nakoupeno</abbr>', 'ProductVariant.meavita_future_quantity', array('escape' => false))?></th>
			<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
			<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
			<th>&nbsp;</th>
		</tr>
		<?php foreach ($product_variants as $product_variant) { ?>
		<tr>
			<td><?php echo $product_variant['Product']['vzp_code']?></td>
			<td><?php echo $product_variant['Product']['group_code']?></td>
			<td><?php echo $product_variant['Product']['referential_number']?></td>
			<td><?php echo $product_variant['Product']['name']?></td>
			<td><?php echo $product_variant['Product']['en_name']?></td>
			<td><?php echo $product_variant['Unit']['name']?></td>
			<td><?php echo $product_variant['ProductVariant']['meavita_price']?></td>
			<td><?php echo $product_variant['ProductVariant']['meavita_quantity']?></td>
			<td><?php echo $product_variant['ProductVariant']['meavita_reserved_quantity']?></td>
			<td><?php echo $product_variant['ProductVariant']['meavita_future_quantity']?></td>
			<td><?php echo $product_variant['ProductVariant']['lot']?></td>
			<td><?php echo $product_variant['ProductVariant']['exp']?></td>
			<td><?php 
			$links = array();
			if (
				// pokud ma uzivatel pravo
				isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSCorrections/user_add')
			) {
				$links[] = $this->Html->link('Upravit', array('controller' => 'c_s_corrections', 'action' => 'add', $product_variant['ProductVariant']['id']));
			}
			echo implode(' | ', $links);
			?>
		</tr>
		<?php 
			$meavita_quantity += $product_variant['ProductVariant']['meavita_quantity'];
			$meavita_reserved_quantity += $product_variant['ProductVariant']['meavita_reserved_quantity'];
			$meavita_future_quantity += $product_variant['ProductVariant']['meavita_future_quantity'];
		} ?>
	</tbody>
	<tfoot>
		<tr>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th><?php echo $meavita_quantity?></th>
			<th><?php echo $meavita_reserved_quantity?></th>
			<th><?php echo $meavita_future_quantity?></th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
	</tfoot>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
<?php } ?>