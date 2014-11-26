<?php
class BPCSRepPurchasePayment extends AppModel {
	var $name = 'BPCSRepPurchasePayment';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('BPCSRepPurchase');
}