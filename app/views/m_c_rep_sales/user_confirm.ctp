<script type="text/javascript" src="/js/m_c_rep_sale_add_edit.js"></script>
<h1>Schválit převod ze skladu MC do skladu repa</h1>
<?php
	$form_options = array();
	if (isset($this->params['named']['rep_id'])) {
?>
<ul>
	<li><?php echo $this->Html->link('Žádosti o převod repa', array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 4))?></li>
</ul>
<?php 
		$form_options = array('url' => array('rep_id' => $this->params['named']['rep_id']));
	} elseif (isset($this->params['named']['unconfirmed_list'])) {
		$form_options = array('url' => array('unconfirmed_list' => true));
	}
	echo $this->Form->create('MCRepSale', $form_options);
	echo $this->element('m_c_rep_sales/add_edit_form', array('disabled' => true));
	echo $this->Form->hidden('MCRepSale.id');
	echo $this->Form->hidden('MCRepSale.confirmed', array('value' => true));
	echo $this->Form->hidden('MCRepSale.user_id', array('value' => $this->Session->read('Auth.User.id')));
	echo $this->Form->hidden('MCRepSale.confirm_date', array('value' => date('Y-m-d H:i:s')));
	echo $this->Form->submit('Schválit');
	echo $this->Form->end();
?>