<button id="search_form_show">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['BusinessPartner']) ){
		$hide = '';
	}
?>
<div id="search_form"<?php echo $hide?>>

	<?php echo $form->create('BusinessPartner', array('url' => array('controller' => 'business_partners', 'action' => 'index')))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Společnost</td>
		</tr>
		<tr>
			<th>Název pobočky</th>
			<td><?php echo $this->Form->input('BusinessPartner.branch_name', array('label' => false))?></td>
			<th>Název firmy</th>
			<td><?php echo $form->input('BusinessPartner.name', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th>IČO</th>
			<td><?php echo $form->input('BusinessPartner.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $form->input('BusinessPartner.dic', array('label' => false))?></td>
			<th>IČZ</th>
			<td><?php echo $this->Form->input('BusinessPartner.icz', array('label' => false))?></td>
		<tr>
			<td colspan="6">Adresa sídla</td>
		</tr>
		<tr>
			<th>Název v adrese</th>
			<td><?php echo $form->input('Address.name', array('label' => false))?></td>
			<th>Jméno osoby</th>
			<td><?php echo $form->input('Address.person_first_name', array('label' => false))?></td>
			<th>Příjmení osoby</th>
			<td><?php echo $form->input('Address.person_last_name', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $form->input('Address.street', array('label' => false))?></td>
			<th>Číslo popisné</th>
			<td><?php echo $form->input('Address.number', array('label' => false))?></td>
			<th>Orientační číslo</th>
			<td><?php echo $form->input('Address.o_number', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Město</th>
			<td><?php echo $form->input('Address.city', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $form->input('Address.zip', array('label' => false))?></td>
			<th>Okres</th>
			<td><?php echo $form->input('Address.region', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Uživatel</td>
		</tr>
		<tr>
			<th>Vlastník</th>
			<td><?php echo $this->Form->input('BusinessPartner.owner_id', array('label' => false, 'options' => $owners, 'empty' => true))?></td>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6">
				<?php
					echo $html->link('reset filtru', array('controller' => 'business_partners', 'reset' => true))
				?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('BusinessPartner.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$("#search_form_show").click(function () {
		if ($('#search_form').css('display') == "none"){
			$("#search_form").show("slow");
		} else {
			$("#search_form").hide("slow");
		}
	});
</script>