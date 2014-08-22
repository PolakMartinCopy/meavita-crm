<h1>Upravit prodej</h1>
<?php
$form_options = array();
if (isset($this->params['named']['rep_id'])) {
?>
<ul>
	<li><?php echo $this->Html->link('Nákupy repa', array('controller' => 'reps', 'action' => 'view', $this->params['named']['rep_id'], 'tab' => 4))?></li>
</ul>
<?php 
	$form_options = array('url' => array('rep_id' => $this->params['named']['rep_id']));
}
echo $this->Form->create('BPRepSale', $form_options);
?>
<?php echo $this->element('b_p_rep_sales/add_edit_form')?>
<?php echo $this->Form->hidden('BPRepSale.id')?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>