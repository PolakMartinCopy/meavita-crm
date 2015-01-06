<?php
App::import('Vendor','xtcpdf');
// celkova sirka
$w = 190;
// sirka leveho sirokeho sloupce
$lw = 100;
// sirka praveho sirokeho sloupce
$rw = 90;
// sirka leveho podsloupce v levem sloupci
$llw = 40;
// sirka praveho podsloupce v levem sloupci
$lrw = 60;
// sirka leveho podsloupce v pravem sloupci
$rlw = 35;
// sirka praveho podsloupce v pravem sloupci
$rrw = 55;
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

$tcpdf->SetFont($textfont, 'B', 14);
$tcpdf->Cell($lw, 0, 'Dodací list', 0, 0, 'L', false);
$tcpdf->Cell($rw, 0, $invoice['CSInvoice']['code'], 0, 1, 'C', false);

// mezera
$tcpdf->Cell($w, 5, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'B', 8);
$tcpdf->Cell($lw, 0, 'Dodavatel:', 0, 0, 'L', false);
$tcpdf->Cell($rw, 0, '', 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell($llw, 0, 'Název:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, 'MeaVita s.r.o.', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'Datum:', 0, 0, 'L', false);
$date_of_issue = explode(' ', $invoice['CSInvoice']['date_of_issue']);
$date_of_issue = $date_of_issue[0];
$date_of_issue_info = db2cal_date($date_of_issue);
$tcpdf->Cell($rrw, 0, $date_of_issue_info, 0, 1, 'L', false);

$tcpdf->Cell($llw, 0, 'Adresa:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, 'Fillova 260/1', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'Č. objednávky:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $invoice['CSInvoice']['order_number'], 0, 1, 'L', false);

$tcpdf->Cell($llw, 0, 'Místo, PSČ:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, '638 00 Brno-Lesna', 0, 0, 'L', false);
$tcpdf->Cell($rw, 0, '', 0, 0, 'L', false);

$tcpdf->Cell($lw, 0, '', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'Č. faktury', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $invoice['CSInvoice']['code'], 0, 1, 'L', false);

$tcpdf->Cell($llw, 0, 'IČO:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, '29248400', 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B', 8);
$tcpdf->Cell($rw, 0, 'Odběratel:', 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell($llw, 0, 'DIČ:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, 'CZ29248400', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'Název:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $invoice['BusinessPartner']['name'], 0, 1, 'L', false);

$street_info = '';
$city_info = '';
if (!empty($invoice['Address'])) {
	$street_info = $invoice['Address']['street'] . ' ' . $invoice['Address']['number'];
	if (!empty($invoice['Address']['o_number'])) {
		$street_info .= '/' . $invoice['Address']['o_number'];
	}
	
	$city_info = array();
	if (!empty($invoice['Address']['zip'])) {
		$city_info[] = $invoice['Address']['zip'];
	}
	if (!empty($invoice['Address']['city'])) {
		$city_info[] = $invoice['Address']['city'];
	}
	$city_info = implode(', ', $city_info);
}

$tcpdf->Cell($llw, 0, 'Telefon:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, '+420 722 779 110', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'Adresa:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $street_info, 0, 1, 'L', false);

$tcpdf->Cell($llw, 0, 'E-mail:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, 'objednavky@meavita.cz', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'PSČ, Místo::', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $city_info, 0, 1, 'L', false);

$tcpdf->Cell($lw, 0, '', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'DIČ:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $invoice['BusinessPartner']['dic'], 0, 1, 'L', false);

$tcpdf->Cell($w, 5, "", 0, 1, 'L', false);

$payment_tbl = '
	<table cellspacing="0" cellpadding="1" border="0">
		<tr>
			<td style="width:' . $llw . 'mm">Forma úhrady:</td>
			<td style="width:' . $lrw . 'mm">převodem</td>
			<td style="width:' . $rlw . 'mm">Poznámka:</td>
			<td style="width:' . $rrw . 'mm" rowspan="4">' . $invoice['CSInvoice']['note'] . '</td>
		</tr>
		<tr>
			<td>Bankovní spojení::</td>
			<td>Fio banka, a.s.</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Číslo účtu:</td>
			<td>2200096026 / 2010</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Variabilní symbol::</td>
			<td>141110</td>
			<td>&nbsp;</td>
		</tr>
	</table>
';

$tcpdf->writeHTML($payment_tbl, true, false, false, false, '');

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tbl = '
<table cellspacing="0" cellpadding="1" border="0">
    <tr>
        <th style="width:100mm"><strong>Popis zboží</strong></th>
        <th style="width:20mm" align="center"><strong>Množství</strong></th>
        <th style="width:35mm" align="center"><strong>LOT</strong></th>
		<th style="width:35mm" align="center"><strong>Datum expirace</strong></th>
    </tr>
';

foreach ($invoice['CSTransactionItem'] as $transaction_item) {
	$tbl .= '
	<tr>
		<td nowrap="nowrap">' . $transaction_item['product_name'] . '</td>
		<td align="center">' . $transaction_item['quantity'] . '</td>
		<td align="center">' . $transaction_item['ProductVariant']['lot'] . '</td>
		<td align="center">' . $transaction_item['ProductVariant']['exp'] . '</td>
	</tr>
';
}
$tbl .= '
</table>
';
				
$tcpdf->writeHTML($tbl, true, false, false, false, '');

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$foot_tbl = '
<table cellspacing="0" cellpadding="1" border="1">
	<tr>
		<td style="width:100mm">Typ balení:</td>
		<td style="width:90mm" rowspan="2" align="center"><img src="' . WWW_ROOT . '/img/podpis2.jpg" width="150"/><br/>Razítko a podpis dodavatele</td>
	</tr>
	<tr>
		<td>' . str_replace("\n", '<br/>', $invoice['CSInvoice']['package_type']) . '</td>
	</tr>
	<tr>
		<td><strong>Převzal (jméno + příjmení):</strong><br/><br/><br/><br/><br/><br/></td>
		<td align="center"><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>podpis zákazníka/razítko<br/>Podpisem zákazník potvrzuje, že zboží bylo doručeno v neporušeném stavu.</td>
	</tr>
</table>
';

$tcpdf->writeHTML($foot_tbl, true, false, false, false, '');

echo $tcpdf->Output('meavita_delivery_note_' . $invoice['CSInvoice']['code'] . '.pdf', 'D');

?>