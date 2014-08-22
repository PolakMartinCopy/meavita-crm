<script type="text/javascript" src="/js/m_c_rep_purchase_add_edit.js"></script>
<h1>Schválit převod ze skladu repa do MC</h1>
<?php
	$form_options = array();
	if (isset($this->params['named']['rep_id'])) {
?>
<ul>
	<li><?php echo $this->Html->link('Žádosti o převod repa', array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 4))?></li>
</ul>
<?php 
		$form_options = array('url' => array('rep_id' => $this->params['named']['rep_id']));
	}
	echo $this->Form->create('MCRepPurchase', $form_options);
	echo $this->element('m_c_rep_purchases/add_edit_form');
	echo $this->Form->hidden('MCRepPurchase.id');
	echo $this->Form->hidden('MCRepPurchase.confirmed', array('value' => true));
	echo $this->Form->hidden('MCRepPurchase.user_id', array('value' => $this->Session->read('Auth.User.id')));
	echo $this->Form->submit('Schválit');
	echo $this->Form->end();
?>