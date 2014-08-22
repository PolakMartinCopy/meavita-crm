<?php 
class BPRepSalePayment extends AppModel {
	var $name = 'BPRepSalePayment';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('BPRepSale');
}
?>