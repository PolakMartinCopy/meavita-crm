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
		<td colspan="2">Dodavatel</td>
	</tr>
	<tr>
		<td style="width:' . $llw . '">Název</td>
		<td style="width:' . $lrw . '">MeaVita s.r.o.</td>
	</tr>
	<tr>
		<td>Adresa</td>
		<td>Fillova 260/1</td>
	</tr>
	<tr>
		<td>Místo, PSČ</td>
		<td>Brno, 638 00</td>
	</tr>
	<tr>
		<td>IČO</td>
		<td>29248400</td>
	</tr>
	<tr>
		<td>DIČ</td>
		<td>CZ29248400</td>
	</tr>
	<tr>
		<td>Telefon</td>
		<td>+420 722 779 110</td>
	</tr>
	<tr>
		<td>Email</td>
		<td>objednavky@meavita.cz</td>
	</tr>
</table>';

$dates_table = '
<table cellspacing="0" cellpadding="1" border="0" width="100%">
	<tr>
		<td style="width:' . $rlw . '">Datum:</td>
		<td style="width:' . $rrw . '">' . $date_of_issue . '</td>
	</tr>
	<tr>
		<td>Č. objednávky</td>
		<td>' . $invoice['CSInvoice']['order_number'] . '</td>
	</tr>
</table>
';

$customer_table = '
<table cellspacing="0" cellpadding="1" border="0" width="100%">
	<tr>
		<td colspan="2"><strong>Odběratel</strong></td>
	</tr>
	<tr>
		<td style="width:' . $rlw . '">Název</td>
		<td style="width:' . $rrw . '"><strong>' . $customer_name . '</strong></td>
	</tr>
	<tr>
		<td>Adresa</td>
		<td><strong>' . $customer_street . '</strong></td>
	</tr>
	<tr>
		<td>Místo, PSČ</td>
		<td><strong>' . $customer_city . '</strong></td>
	</tr>
	<tr>
		<td>DIČ</td>
		<td>' . $customer_dic . '</td>
	</tr>
</table>';

$payment_table = '
<table cellspacing="0" cellpadding="1" border="0" width="100%">
	<tr>
		<td style="width:' . $llw . '">Forma úhrady:</td>
		<td style="width:' . $lrw . '">' . $payment_type . '</td>
	</tr>
	<tr>
		<td>Bankovní spojení:</td>
		<td>Fio banka, a.s.</td>
	</tr>
	<tr>
		<td>Číslo účtu:</td>
		<td><strong>2100811168 / 2010</strong></td>
	</tr>
	<tr>
		<td>Variabilní symbol:</td>
		<td><strong>' . $variable_symbol . '</strong></td>
	</tr>
</table>
';

$products_table = '
<table cellspacing="0" cellpadding="1" border="0">
    <tr>
        <th style="width:90mm"><strong>Popis zboží</strong></th>
        <th style="width:20mm" align="right"><strong>Množství</strong></th>
        <th style="width:35mm" align="right"><strong>LOT</strong></th>
		<th style="width:30mm" align="right"><strong>Datum expirace</strong></th>
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
		<td align="left" style="font-size:40px"><strong>Dodací list č.</strong></td>
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
		<td style="border:0.5px solid black">Poznámka: ' . $note . '</td>
	</tr>
	<tr>
		<td colspan="2" style="border:0.5px solid black">' . $products_table . '</td>
	</tr>
</table>
<table cellspacing="0" cellpadding="5" border="0">
	<tr>
		<td style="width:100mm;border:0.5px solid black;">Typ balení:</td>
		<td style="width:86mm;border:0.5px solid black;" rowspan="2" align="center"><img src="img/podpis2.jpg" width="150"/><br/>Razítko a podpis dodavatele</td>
	</tr>
	<tr>
		<td style="border:0.5px solid black;">' . str_replace("\n", '<br/>', $invoice['CSInvoice']['package_type']) . '</td>
	</tr>
	<tr>
		<td style="border:0.5px solid black;">Převzal (jméno + příjmení):<br/><br/><br/><br/><br/><br/></td>
		<td align="center" style="border:0.5px solid black;"><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>podpis zákazníka/razítko<br/>Podpisem zákazník potvrzuje, že zboží bylo doručeno v neporušeném stavu.</td>
	</tr>
</table>				
';
$tcpdf->writeHTML($main_table, true, false, false, false, '');

echo $tcpdf->Output('meavita_delivery_note_' . $invoice['CSInvoice']['code'] . '.pdf', 'D');

?>