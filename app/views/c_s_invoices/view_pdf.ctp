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
$rlw = 50;
// sirka praveho podsloupce v pravem sloupci
$rrw = 40;

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
//$tcpdf->Line(10, 20, 200, 20, $linestyle);

$tcpdf->SetFont($textfont, 'B', 14);
$tcpdf->Cell($lw, 0, 'Faktura č. ', 0, 0, 'L', false);
$tcpdf->Cell($rw, 0, $invoice['CSInvoice']['code'], 0, 1, 'C', false);

// mezera
$tcpdf->Cell($w, 5, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'B', 8);
$tcpdf->Cell($lw, 0, 'Dodavatel', 0, 0, 'L', false);
$tcpdf->Cell($rw, 0, '', 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell($llw, 0, 'Název:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, 'MeaVita s.r.o.', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'Datum vystavení:', 0, 0, 'L', false);
$date_of_issue = explode(' ', $invoice['CSInvoice']['date_of_issue']);
$date_of_issue = $date_of_issue[0];
$date_of_issue_info = db2cal_date($date_of_issue);
$tcpdf->Cell($rrw, 0, $date_of_issue_info, 0, 1, 'L', false);

$tcpdf->Cell($llw, 0, 'Adresa:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, 'Fillova 260/1', 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B', 8);
$tcpdf->Cell($rlw, 0, 'Datum splatnosti:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, db2cal_date($invoice['CSInvoice']['due_date']), 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell($llw, 0, 'Místo, PSČ:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, 'Brno, 602 00', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'Datum zdanitelného plnění:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $date_of_issue_info, 0, 1, 'L', false);

$tcpdf->Cell($w, 8, '', 0, 1, 'L', false);

$tcpdf->Cell($llw, 0, 'IČO:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, '29248400', 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B', 8);
$tcpdf->Cell($rw, 0, 'Odběratel:', 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell($llw, 0, 'DIČ:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, 'CZ29248400', 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B', 8);
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

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell($llw, 0, 'Telefon:', 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B', 8);
$tcpdf->Cell($lrw, 0, '+420 722 779 110', 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell($rlw, 0, 'Adresa:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $street_info, 0, 1, 'L', false);

$tcpdf->Cell($llw, 0, 'E-mail:', 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B', 8);
$tcpdf->Cell($lrw, 0, 'objednavky@meavita.cz', 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell($rlw, 0, 'PSČ, Místo:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $city_info, 0, 1, 'L', false);

$tcpdf->Cell($lw, 0, '', 0, 0, 'L', false);
$contact_person_info = '';
if (!empty($invoice['ContactPerson'])) {
	$contact_person_info = $invoice['ContactPerson']['first_name'] . ' ' . $invoice['ContactPerson']['last_name'];
	if (!empty($invoice['ContactPerson']['prefix'])) {
		$contact_person_info = $invoice['ContactPerson']['prefix'] . ' ' . $contact_person_info;
	}
	if (!empty($invoice['ContactPerson']['suffix'])) {
		$contact_person_info = $contact_person_info . ' ' . $invoice['ContactPerson']['suffix'];
	}
}
$tcpdf->Cell($rlw, 0, 'Kontaktní osoba:', 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B', 8);
$tcpdf->Cell($rrw, 0, $contact_person_info, 0, 1, 'L', false);
$tcpdf->SetFont($textfont,'', 8);

$tcpdf->Cell($lw, 0, '', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'IČO:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $invoice['BusinessPartner']['ico'], 0, 1, 'L', false);

$tcpdf->Cell($lw, 0, '', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'DIČ:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $invoice['BusinessPartner']['dic'], 0, 1, 'L', false);

$tcpdf->Cell($w, 5, "", 0, 1, 'L', false);

$payment_tbl = '
	<table cellspacing="0" cellpadding="1" border="0">
		<tr>
			<td style="width:' . $llw . 'mm"><strong>Forma úhrady:</strong></td>
			<td style="width:' . $lrw . 'mm"><strong>převodem</strong></td>
			<td style="width:' . $rlw . 'mm">Poznámka:</td>
			<td style="width:' . $rrw . 'mm" rowspan="4">' . $invoice['CSInvoice']['note'] . '</td>
		</tr>
		<tr>
			<td>Bankovní spojení:</td>
			<td>Fio banka, a.s.</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Číslo účtu:</td>
			<td>2200096026 / 2010</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td><strong>Variabilní symbol:</strong></td>
			<td><strong>' . $invoice['CSInvoice']['code'] . '</strong></td>
			<td>&nbsp;</td>
		</tr>
	</table>
';

$tcpdf->writeHTML($payment_tbl, true, false, false, false, '');

$tcpdf->Cell($w, 5, "", 0, 1, 'L', false);
$tcpdf->SetFont($textfont,'', 8);
$tbl = '
<table cellspacing="0" cellpadding="1" border="0">
    <tr>
        <th style="width:100mm"><strong>Popis zboží</strong></th>
        <th style="width:20mm"><strong>Množství</strong></th>
        <th style="width:35mm"><strong>Cena za MJ bez DPH</strong></th>
		<th style="width:35mm"><strong>Cena za MJ vč. DPH</strong></th>
    </tr>
';

foreach ($invoice['CSTransactionItem'] as $transaction_item) {
	$tbl .= '
	<tr>
		<td nowrap="nowrap">' . $transaction_item['product_name'] . '</td>
		<td>' . $transaction_item['quantity'] . '</td>
		<td align="right">' . format_price($transaction_item['price']) . '&nbsp;' . $invoice['Currency']['shortcut'] . '</td>
		<td align="right">' . format_price($transaction_item['price_vat']) . '&nbsp;' . $invoice['Currency']['shortcut'] . '</td>
	</tr>
';
}
$tbl .= '
</table>
';
				
$tcpdf->writeHTML($tbl, true, false, false, false, '');

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tcpdf->Cell(100, 0, "", 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B', 8);
$tcpdf->Cell($rlw, 0, "Celkem k úhradě", 0, 0, 'C', false);
$tcpdf->Cell($rrw, 0, format_price($invoice['CSInvoice']['amount_vat']) . ' ' . $invoice['Currency']['shortcut'], 0, 1, 'R', false);

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);

$foot_tbl = '
<table cellspacing="0" cellpadding="1" border="0">
	<tr>
		<td rowspan="2" style="width:100mm">Zboží zůstává až do úplného uhrazení kupní ceny majetkem společnosti MeaVita s.r.o.  Dovolujeme si Vás upozornit, že v případě nedodržení data splatnosti uvedeného na faktuře, Vám budeme účtovat úrok z prodlení v dohodnuté, resp. zákonné výši.</td>
		<td style="width:90mm">
			<table cellspacing="0" cellpadding="1" border="0" style="width:100%">
				<tr>
					<td style="width:30%" align="right"><strong>sazba DPH</strong></td>
					<td style="width:35%" align="right"><strong>základ</strong></td>
					<td style="width:35%" align="right"><strong>DPH</strong></td>
				</tr>
';
foreach ($tax_classes as $tax_class) {
	$price_sum_info = '';
	$vat_info = '';
	if ($tax_class[0]['price_sum']) {
		$price_sum_info = format_price($tax_class[0]['price_sum']) . ' ' . $invoice['Currency']['shortcut'];
	}
	if ($tax_class[0]['vat']) {
		$vat_info = format_price($tax_class[0]['vat']) . ' ' . $invoice['Currency']['shortcut'];
	}
	$foot_tbl .= '
				<tr>
					<td align="right">' . $tax_class['TaxClass']['name'] . '</td>
					<td align="right">' . $price_sum_info . '</td>
					<td align="right">' . $vat_info . '</td>
				</tr>
';
}
		
$foot_tbl .= '
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" valign="bottom"><br/><br/><br/><br/><br/>podpis/razítko</td>
	</tr>
</table>
';

$tcpdf->writeHTML($foot_tbl, true, false, false, false, '');

echo $tcpdf->Output('meavita_invoice_' . $invoice['CSInvoice']['code'] . '.pdf', 'D');

?>