<h1>Korekce skladu</h1>
<button id="search_form_show_c_s_corrections">vyhledávací formulář</button>
<?php echo $this->element('search_forms/c_s_corrections', array('url' => array('controller' => 'c_s_corrections', 'action' => 'index'))); ?>

<?php if (empty($corrections)) { ?>
<p><em>V systému nejsou žádné korekce skladu.</em></p>
<?php } else { ?>
	<?php echo $this->element('c_s_corrections/index_table')?>
<?php } ?>
<script type="text/javascript">
$("#search_form_show_c_s_corrections").click(function () {
	if ($('#search_form_c_s_corrections').css('display') == "none"){
		$("#search_form_c_s_corrections").show("slow");
	} else {
		$("#search_form_c_s_corrections").hide("slow");
	}
});
</script>