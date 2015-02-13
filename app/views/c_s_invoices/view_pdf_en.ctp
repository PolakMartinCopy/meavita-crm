<?php
App::import('Vendor','xtcpdf');
// celkova sirka
$w = 190;
// sirka leveho sirokeho sloupce
$lw = 90;
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
		<td colspan="2">Supplier</td>
	</tr>
	<tr>
		<td style="width:' . $llw . '">Company name</td>
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
		<td>+420 602 773 453</td>
	</tr>
	<tr>
		<td>Email</td>
		<td>meavita@meavita.cz</td>
	</tr>
</table>';

$dates_table = '
<table cellspacing="0" cellpadding="1" border="0" width="100%">
	<tr>
		<td style="width:' . $rlw . '">Date</td>
		<td style="width:' . $rrw . '">' . $date_of_issue . '</td>
	</tr>
	<tr>
		<td>Order no.:</td>
		<td>' . $invoice['CSInvoice']['order_number'] . '</td>
	</tr>
	<tr>
		<td>Country of origin</td>
		<td>EU</td>
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
		<td>Contact person</td>
		<td><strong>' . $contact_person . '</strong></td>
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
		<td>Bankname:</td>
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
<table cellspacing="0" cellpadding="1" border="0" width="100%">
    <tr>
        <th style="width:50%">Description</th>
        <th align="right" style="width:10%">Quantity</th>
        <th align="right" style="width:20%">Unit price without VAT</th>
		<th align="right" style="width:20%">Subtotal price without VAT</th>
    </tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
';
foreach ($invoice['CSTransactionItem'] as $transaction_item) {
	$products_table .= '
	<tr>
		<td nowrap="nowrap">' . $transaction_item['product_en_name'] . '</td>
		<td align="right">' . $transaction_item['quantity'] . '</td>
		<td align="right">' . format_price($transaction_item['price']) . '&nbsp;' . $invoice['Currency']['shortcut'] . '</td>
		<td align="right">' . format_price($transaction_item['price'] * $transaction_item['quantity']) . '&nbsp;' . $invoice['Currency']['shortcut'] . '</td>
	</tr>
';
}
$products_table .= '
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align="right"><strong>Total price without VAT</strong></td>
		<td align="right"><strong>' . format_price($invoice['CSInvoice']['amount']) . ' ' . $invoice['Currency']['shortcut'] . '</strong></td>
	</tr>
</table>
';

$signature_table = '
<table cellspacing="0" cellpadding="1" border="0" style="width:100%">
	<tr>
		<td align="center" valign="middle"><img src="img/podpis2.jpg" width="150"/></td>
	</tr>
	<tr>
		<td align="center" valign="bottom">Signature, stamp</td>
	</tr>
</table>';

$main_table = '
<table cellspacing="0" cellpadding="5" border="0">
	<tr>
		<td align="left" style="font-size:40px"><strong>Invoice</strong></td>
		<td align="right" style="font-size:40px"><strong>' . $invoice['CSInvoice']['code'] . '</strong></td>
	</tr>
	<tr>
		<td align="center" style="border-top:0.5px solid black;border-left:0.5px solid black;border-right:0.5px solid black;padding-top:5px">' . $supplier_logo . '</td>
		<td style="border:0.5px solid black">' . $dates_table . '</td>
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
	<tr>
		<td style="border:0.5px solid black" align="center">Delivery of goods to another EU member country.</td>
		<td style="border:0.5px solid black">' . $signature_table . '</td>
	</tr>
</table>';
$tcpdf->writeHTML($main_table, true, false, false, false, '');

echo $tcpdf->Output('meavita_invoice_' . $invoice['CSInvoice']['code'] . '.pdf', 'D');

?>