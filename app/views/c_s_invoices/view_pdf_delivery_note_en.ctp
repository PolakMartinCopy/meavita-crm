<?php
App::import('Vendor','xtcpdf');
// celkova sirka
$w = 190;
// sirka leveho sirokeho sloupce
$lw = 100;
// sirka praveho sirokeho sloupce
$rw = 90;
// sirka leveho podsloupce v levem sloupci
$llw = '40%';
// sirka praveho podsloupce v levem sloupci
$lrw = '60%';
// sirka leveho podsloupce v pravem sloupci
$rlw = '50%';
// sirka praveho podsloupce v pravem sloupci
$rrw = '50%';

$tcpdf = new XTCPDF();
$textfont = 'dejavusans'; // looks better, finer, and more condensed than 'dejavusans'

$tcpdf->SetAuthor(CUST_ROOT);
$tcpdf->SetAutoPageBreak( false );
$tcpdf->setHeaderFont(array($textfont,'',40));
$tcpdf->xheadercolor = array(150,0,0);
$tcpdf->xheadertext = CUST_NAME;
$tcpdf->xfootertext = 'Copyright © %d ' . CUST_NAME . '. All rights reserved.';

// add a page (required with recent versions of tcpdf)
$tcpdf->AddPage();

$tcpdf->SetFillColor(255,255,255);
$linestyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(0, 0, 0));

$tcpdf->SetFont($textfont, '', 8);

$supplier_logo = '<img src="img/meavita-small.png" width="100" height="25"/>';

$supplier_table = '
<table cellspacing="0" cellpadding="1" border="0" width="100%">
	<tr>
		<td colspan="2">Supplier (from)</td>
	</tr>
	<tr>
		<td style="width:' . $llw . '">Company Name</td>
		<td style="width:' . $lrw . '">MeaVita s.r.o.</td>
	</tr>
	<tr>
		<td>Address</td>
		<td>Fillova 260/1</td>
	</tr>
	<tr>
		<td>City, postal code</td>
		<td>Brno, 638 00</td>
	</tr>
	<tr>
		<td>ID (IČ)</td>
		<td>29248400</td>
	</tr>
	<tr>
		<td>VAT reg. no. (DIČ)</td>
		<td>CZ29248400</td>
	</tr>
	<tr>
		<td>Phone</td>
		<td>420 602 773 453</td>
	</tr>
	<tr>
		<td>Email</td>
		<td>sales@meavita.cz</td>
	</tr>
</table>';

$dates_table = '
<table cellspacing="0" cellpadding="1" border="0" width="100%">
	<tr>
		<td style="width:' . $rlw . '">Date</td>
		<td style="width:' . $rrw . '">' . $date_of_issue . '</td>
	</tr>
	<tr>
		<td>Order no.</td>
		<td>' . $invoice['CSInvoice']['order_number'] . '</td>
	</tr>
	<tr>
		<td>Country of origin</td>
		<td>EU</td>
	</tr>
	<tr>
		<td>Invoice no.</td>
		<td>' . $invoice['CSInvoice']['code'] . '</td>
	</tr>
</table>
';

$customer_table = '
<table cellspacing="0" cellpadding="1" border="0" width="100%">
	<tr>
		<td colspan="2"><strong>Customer (to)</strong></td>
	</tr>
	<tr>
		<td style="width:' . $rlw . '">Company name</td>
		<td style="width:' . $rrw . '"><strong>' . $customer_name . '</strong></td>
	</tr>
	<tr>
		<td>Address</td>
		<td><strong>' . $customer_street . '</strong></td>
	</tr>
	<tr>
		<td>City, postal code</td>
		<td><strong>' . $customer_city . '</strong></td>
	</tr>
	<tr>
		<td>VAT reg. no. (DIČ)</td>
		<td>' . $customer_dic . '</td>
	</tr>
</table>';

$payment_table = '
<table cellspacing="0" cellpadding="1" border="0" width="100%">
	<tr>
		<td style="width:' . $llw . '">Payment:</td>
		<td style="width:' . $lrw . '">' . $payment_type . '</td>
	</tr>
	<tr>
		<td>Bank name:</td>
		<td>Fio banka, a.s.</td>
	</tr>
	<tr>
		<td>IBAN Account no.:</td>
		<td><strong>CZ0620100000002000098174</strong></td>
	</tr>
	<tr>
		<td>SWIFT Code:</td>
		<td>FIOBCZPPXXX</td>
	</tr>
</table>
';

$products_table = '
<table cellspacing="0" cellpadding="1" border="0">
    <tr>
        <th style="width:90mm"><strong>Description</strong></th>
        <th style="width:20mm" align="right"><strong>Quantity</strong></th>
        <th style="width:35mm" align="right"><strong>LOT</strong></th>
		<th style="width:31mm" align="right"><strong>Expiry date</strong></th>
    </tr>
';

foreach ($invoice['CSTransactionItem'] as $transaction_item) {
	$products_table .= '
	<tr>
		<td nowrap="nowrap">' . $transaction_item['product_name'] . '</td>
		<td align="right">' . $transaction_item['quantity'] . '</td>
		<td align="right">' . $transaction_item['ProductVariant']['lot'] . '</td>
		<td align="right">' . $transaction_item['ProductVariant']['exp'] . '</td>
	</tr>
';
}
$products_table .= '
</table>
';

$main_table = '
<table cellspacing="0" cellpadding="5" border="0">
	<tr>
		<td align="left" style="font-size:40px"><strong>Delivery note</strong></td>
		<td align="right" style="font-size:40px"><strong>' . $invoice['CSInvoice']['code'] . '</strong></td>
	</tr>
	<tr>
		<td align="center" style="width:100mm;border-top:0.5px solid black;border-left:0.5px solid black;border-right:0.5px solid black;padding-top:5px">' . $supplier_logo . '</td>
		<td style="width:86mm;border:0.5px solid black">' . $dates_table . '</td>
	</tr>
	<tr>
		<td style="border-bottom:0.5px solid black;border-left:0.5px solid black;border-right:0.5px solid black">' . $supplier_table . '</td>
		<td style="border:2px solid black">' . $customer_table . '</td>
	</tr>
	<tr>
		<td style="border:0.5px solid black">' . $payment_table . '</td>
		<td style="border:0.5px solid black">Note: ' . $note . '</td>
	</tr>
	<tr>
		<td colspan="2" style="border:0.5px solid black">' . $products_table . '</td>
	</tr>
</table>
<table cellspacing="0" cellpadding="5" border="0">
	<tr>
		<td style="width:100mm;border:0.5px solid black;">Package type:</td>
		<td style="width:86mm;border:0.5px solid black;" rowspan="2" align="center"><img src="img/podpis2.jpg" width="150"/><br/>Supplier signature, stamp</td>
	</tr>
	<tr>
		<td style="border:0.5px solid black;">' . str_replace("\n", '<br/>', $invoice['CSInvoice']['package_type']) . '</td>
	</tr>
	<tr>
		<td style="border:0.5px solid black;">Car licence plate:<br/><br/><br/><br/><br/><br/></td>
		<td rowspan="2" align="center" style="border:0.5px solid black;"><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>Customer signature, stamp<br/>Customer signature certify, that goods have been delivered in good condition.</td>
	</tr>
	<tr>
		<td style="border:0.5px solid black;">Take over (name + surname):<br/><br/><br/><br/><br/><br/></td>
	</tr>		
</table>
';
$tcpdf->writeHTML($main_table, true, false, false, false, '');

echo $tcpdf->Output('meavita_delivery_note_' . $invoice['CSInvoice']['code'] . '.pdf', 'D');

?>