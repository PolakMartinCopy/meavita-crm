<h1>Upravit prodej</h1>
<?php
$form_options = array();
if (isset($this->params['named']['c_s_rep_id'])) {
?>
<ul>
	<li><?php echo $this->Html->link('Nákupy repa', array('controller' => 'c_s_reps', 'action' => 'view', $this->params['named']['c_s_rep_id'], 'tab' => 4))?></li>
</ul>
<?php 
	$form_options = array('url' => array('c_s_rep_id' => $this->params['named']['c_s_rep_id']));
}
echo $this->Form->create('BPCSRepSale', $form_options);
?>
<?php echo $this->element('b_p_c_s_rep_sales/add_edit_form')?>
<?php echo $this->Form->hidden('BPCSRepSale.id')?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>