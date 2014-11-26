<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>CRM</title>

	<link href="/favicon.ico" type="image/x-icon" rel="icon" />
	<link href="/favicon.ico" type="image/x-icon" rel="shortcut icon" />
	<link rel="stylesheet" type="text/css" href="/css/debug.css" />
<!--	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/black-tie/jquery-ui.css"> -->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css">
<!-- 	<link rel="stylesheet" type="text/css" href="/css/black-tie/jquery-ui-1.8.10.custom.css" /> -->
	<link rel="stylesheet" type="text/css" href="/css/admin.css" />
	
	<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/jquery.ui.datepicker-cs.js"></script>
<!-- 	<script type="text/javascript" src="/js/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="/js/jquery.cookie.js"></script> -->
	
	<script type="text/javascript" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/plug-ins/725b2a2115b/integration/jqueryui/dataTables.jqueryui.js"></script>
	
	<script type="text/javascript">
		function showloader() {
			document.getElementById('loading').style.display='';
			document.getElementById("darkLayer").style.display = "";
		}

		function hideloader() {
			document.getElementById('loading').style.display='none';
			document.getElementById("darkLayer").style.display = "none";
		} 
	</script>

	<script type="text/javascript">
		$(function() {
			$( "#tabs" ).tabs({
				cookie : {
					expires : 1
				}
			});
		});
	</script>
</head>
<body onload="hideloader()">
	<div id="darkLayer" class="darkClass" style="display:none"></div>
	<div id="loading" style="position:absolute; width:100%; text-align:center; top:100px; display:none">
		<img src="/images/loading.gif" border="0" alt="" />
	</div>

	<div id="content_container_center">
		<div id="top_lista_left">CRM</div>
		<div id="top_lista_right">
		<?php if (in_array($this->params['controller'], $meavita_controllers) || ($this->params['controller'] == 'product_variants' && $this->params['action'] == 'user_meavita_index')) {?>
			Nacházíte se v <em>MEAVITA</em> sekci -
			<script type="text/javascript">
				$(document).ready(function() {
					$('body').css('background-color', '#C0D0FE');
				});
			</script>
		<?php } elseif (in_array($this->params['controller'], $m_c_controllers) || ($this->params['controller'] == 'product_variants' && $this->params['action'] == 'user_m_c_index')) { ?>
		Nacházíte se v <em>MEDICALCORP</em> sekci -
		<script type="text/javascript">
			$(document).ready(function() {
				$('body').css('background-color', '#F5C9D0');
			});
		</script>
		<?php
		}
		
		if (!empty($logged_in_user)) {?>
		Jste přihlášen(a) jako: <em><?php echo $logged_in_user['User']['first_name'] . ' ' . $logged_in_user['User']['last_name']?> | <?php echo $html->link('odhlásit se', array('controller' => 'users', 'action' => 'logout'))?></em>
		<?php } else { ?>
		Nejste přihlášen(a), <?php echo $html->link('přihlašte se', array('controller' => 'users', 'action' => 'login'))?>
		<?php } ?>
		</div>
		<div class="clearer"></div>
		
		<?php 
		if ($session->check('Auth.User')) {
			echo $this->element('admin_menu');
		} ?>

		<div id="container">
			<div id="content">
				<div id="subMenuContent">
					<?php echo $this->element('admin_left_menu'); ?>
				</div>
				<div class="clearer"></div>
				<?php if (
					// jsou nevyrizene pozadavky?
					isset($is_unconfirmed_request) && ($is_unconfirmed_request === true)
					// mam pristup ke schvalovani pozadavku?
					&& isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Pages/user_list_unconfirmed_requests')
				) { ?>
				<div id="unconfirmed_request_message">
					<?php $list_unconfirmed_requests_url = array('controller' => 'pages', 'action' => 'list_unconfirmed_requests')?>
					<p>V systému jsou <?php echo $this->Html->link('nepotvrzené požadavky na převod', $list_unconfirmed_requests_url)?>. Potvrďte je prosím <?php echo $this->Html->link('zde', $list_unconfirmed_requests_url)?>.</p>
				</div>
				<?php } ?>
				<?php if (
					// jsou nevyrizene pozadavky?
					isset($is_exchange_rate_downloaded) && $is_exchange_rate_downloaded === false
					// mam pristup ke schvalovani pozadavku?
					&& isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/Tools/user_exchange_rate_download')
				) { ?>
				<div id="exchange_rate_download_message">
					<?php
						$back_url = $this->params['url']['url'];
						$back_url = urlencode(base64_encode($back_url));
						$exchange_rate_download_url = array('controller' => 'tools', 'action' => 'exchange_rate_download', 'back' => $back_url);
					?>
					<p>V systému není stažen aktuální kurz. Stáhněte jej prosím <?php echo $this->Html->link('zde', $exchange_rate_download_url)?>.</p>
				</div>
				<?php } ?>
				<div id="rightContent">
<!-- 			<img src="/images/loading.gif" id="loading" style="display:none"/> -->

				<?php echo $session->flash('auth'); ?>
				<?php echo $session->flash(); ?>
	
				<?php echo $content_for_layout; ?>
				</div>
				<div class="clearer"></div>
			</div>
			<div id="footer">
				<a href="http://www.cakephp.org/" target="_blank"><img src="/img/cake.power.gif" alt="CakePHP: the rapid development php framework" border="0" /></a>		</div>
		 </div>
		<?php echo $this->element('sql_dump')?>
	</div>
</body>
</html>