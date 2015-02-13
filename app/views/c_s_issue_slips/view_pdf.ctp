<?php
App::import('Vendor','xtcpdf');
// sirka leveho podsloupce v levem sloupci
$llw = '40%';
// sirka praveho podsloupce v levem sloupci
$lrw = '60%';

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
		<td colspan="2">Firma</td>
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

$header_table = '
<table cellspacing="0" cellpadding="1" border="0" width="100%">
	<tr>
		<td style="font-size:32px"><strong>Výdejka</strong></td>
		<td style="text-align:right">Číslo</td>
		<td style="text-align:right">' . $issue_slip['CSIssueSlip']['id'] . '</td>
	</tr>
</table>
';

$partners_table = '
<table cellspacing="0" cellpadding="1" border="0" width="100%">
	<tr>
		<td>Sklad:</td>
		<td>MeaVita<br/>Fillova 260/1<br/>63800 Brno</td>
	</tr>
	<tr>
		<td>Komu vydáno:</td>
		<td>' . $customer_name . '<br/>' . $customer_street . '<br/>' . $customer_city . '</td>
	</tr>
</table>
';

$products_table = '
<table cellspacing="0" cellpadding="1" border="0" width="100%">
    <tr>
        <th style="width:50%">Popis zboží</th>
        <th align="right" style="width:20%">LOT</th>
		<th align="right" style="width:20%">EXP</th>
		<th align="right" style="width:10%">Množství</th>
    </tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
';
foreach ($issue_slip['CSTransactionItem'] as $transaction_item) {
	$products_table .= '
	<tr>
		<td nowrap="nowrap">' . $transaction_item['product_name'] . '</td>
		<td align="right">' . $transaction_item['ProductVariant']['lot'] . '</td>
		<td align="right">' . $transaction_item['ProductVariant']['exp'] . '</td>
		<td align="right">' . $transaction_item['quantity'] . '</td>
	</tr>
';
}
$products_table .= '
</table>
';

$signature_table = '
<table cellspacing="0" cellpadding="1" border="0" style="width:100%">
	<tr>
		<td align="center" valign="middle"><img src="img/podpis2.jpg" width="150"/></td>
	</tr>
	<tr>
		<td align="center" valign="bottom">podpis/razítko</td>
	</tr>
</table>';

$main_table = '
<table cellspacing="0" cellpadding="5" border="0">
	<tr>
		<td align="center" style="border-top:0.5px solid black;border-left:0.5px solid black;border-right:0.5px solid black;padding-top:5px">' . $supplier_logo . '</td>
		<td style="border:0.5px solid black">' . $header_table . '</td>
	</tr>
	<tr>
		<td style="border-bottom:0.5px solid black;border-left:0.5px solid black;border-right:0.5px solid black">' . $supplier_table . '</td>
		<td style="border:0.5px solid black">' . $partners_table . '</td>
	</tr>
	<tr>
		<td style="border:0.5px solid black">Účel výdeje: ' . $purpose . '</td>
		<td style="border:0.5px solid black">Datum: ' . $date . '</td>
	</tr>
	<tr>
		<td colspan="2" style="border:0.5px solid black">' . $products_table . '</td>
	</tr>
	<tr>
		<td style="border:0.5px solid black">' . $signature_table . '</td>
		<td style="border:0.5px solid black">Přijal:</td>
	</tr>
</table>';
//debug($main_table); die();
$tcpdf->writeHTML($main_table, true, false, false, false, '');

echo $tcpdf->Output('vydejka_' . $issue_slip['CSIssueSlip']['id'] . '.pdf', 'D');

?>