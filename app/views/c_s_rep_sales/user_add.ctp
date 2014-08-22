<script type="text/javascript" src="/js/c_s_rep_sale_add_edit.js"></script>

<h1>Přidat žádost o převod</h1>
<?php
	$form_options = array();
	if (isset($this->params['named']['c_s_rep_id'])) {
?>
<ul>
	<li><?php echo $this->Html->link('Žádosti o převod repa', array('controller' => 'c_s_reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => 5))?></li>
</ul>
<?php 
		$form_options = array('url' => array('c_s_rep_id' => $this->params['named']['c_s_rep_id']));
	}
	echo $this->Form->create('CSRepSale', $form_options);
	echo $this->element('c_s_rep_sales/add_edit_form');
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>