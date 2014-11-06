<div class="menu_header">
	Nástěnka
</div>
<ul class="menu_links">
<?php if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/BlackboardNotes/user_index')) { ?>
	<li><?php echo $html->link('Nástěnka', array('controller' => 'blackboard_notes', 'action' => 'index'))?></li>
<?php }
	if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/BlackboardNotes/user_add')) {
?>
	<li><?php echo $html->link('Přidat příspěvek', array('controller' => 'blackboard_notes', 'action' => 'add'))?></li>
<?php } ?>
</ul>