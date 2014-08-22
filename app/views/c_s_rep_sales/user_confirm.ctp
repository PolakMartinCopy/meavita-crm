<script type="text/javascript" src="/js/c_s_rep_sale_add_edit.js"></script>
<h1>Schválit převod ze skladu Meavity do skladu repa</h1>
<?php
	$form_options = array();
	if (isset($this->params['named']['c_s_rep_id'])) {
?>
<ul>
	<li><?php echo $this->Html->link('Žádosti o převod repa', array('controller' => 'c_s_reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => 4))?></li>
</ul>
<?php 
		$form_options = array('url' => array('c_s_rep_id' => $this->params['named']['c_s_rep_id']));
	} else {
		$form_options = array('url' => array('unconfirmed_list' => true));
	}
	echo $this->Form->create('CSRepSale', $form_options);
	echo $this->element('c_s_rep_sales/add_edit_form', array('disabled' => true));
	echo $this->Form->hidden('CSRepSale.id');
	echo $this->Form->hidden('CSRepSale.confirmed', array('value' => true));
	echo $this->Form->hidden('CSRepSale.user_id', array('value' => $this->Session->read('Auth.User.id')));
	echo $this->Form->hidden('CSRepSale.confirm_date', array('value' => date('Y-m-d H:i:s')));
	echo $this->Form->submit('Schválit');
	echo $this->Form->end();
?>