<script type="text/javascript" src="/js/m_c_rep_sale_add_edit.js"></script>

<h1>Přidat žádost o převod</h1>
<?php
	$form_options = array();
	if (isset($this->params['named']['rep_id'])) {
?>
<ul>
	<li><?php echo $this->Html->link('Žádosti o převod repa', array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 5))?></li>
</ul>
<?php 
		$form_options = array('url' => array('rep_id' => $this->params['named']['rep_id']));
	}
	echo $this->Form->create('MCRepSale', $form_options);
	echo $this->element('m_c_rep_sales/add_edit_form');
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>