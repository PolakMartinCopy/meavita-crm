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
$tcpdf->Cell($lw, 0, 'Invoice', 0, 0, 'L', false);
$tcpdf->Cell($rw, 0, 'Inv. ' . $invoice['CSInvoice']['code'], 0, 1, 'C', false);

// mezera
$tcpdf->Cell($w, 5, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);

// nachystam si datum vystaveni
$date_of_issue = explode(' ', $invoice['CSInvoice']['date_of_issue']);
$date_of_issue = $date_of_issue[0];
$date_of_issue_info = db2cal_date($date_of_issue);
// info o adrese
$street_info = '';
// info o meste
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

$header_table = '
	<table cellspacing="0" cellpadding="1" border="0">
		<tr>
			<td colspan="2"><strong>Supplier (from):</strong></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td style="width:' . $llw . 'mm">Company name:</td>
			<td style="width:' . $lrw . 'mm">MeaVita s.r.o.</td>
			<td style="width:' . $rlw . 'mm">Date:</td>
			<td style="width:' . $rrw . 'mm">' . $date_of_issue_info . '</td>
		</tr>
		<tr>
			<td>Address:</td>
			<td>Fillova 260/1</td>
			<td>Order no.:</td>
			<td>' . $invoice['CSInvoice']['order_number'] . '</td>
		</tr>
		<tr>
			<td>City, postal code:</td>
			<td>638 00 Brno-Lesna</td>
			<td>Country of origin:</td>
			<td>EU</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td>ID (IČ):</td>
			<td>29248400</td>
			<td colspan="2"><strong>Customer (to):</strong></td>
		</tr>
		<tr>
			<td>VAT reg. no. (DIČ):</td>
			<td>CZ29248400</td>
			<td>Company name:</td>
			<td>' . $invoice['BusinessPartner']['name'] . '</td>
		</tr>
		<tr>
			<td>Phone:</td>
			<td>420 602 773 453</td>
			<td>Address:</td>
			<td>' . $street_info . '</td>
		</tr>
		<tr>
			<td>E-mail:</td>
			<td>meavita@meavita.cz</td>
			<td>City, postal code:</td>
			<td>' . $city_info . '</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td>VAT reg. no. (DIČ):</td>
			<td>' . $invoice['BusinessPartner']['dic'] . '</td>
		</tr>
	</table>
';
$tcpdf->writeHTML($header_table, true, false, false, false, '');

/*
$tcpdf->Cell($lw, 0, 'Supplier (from):', 0, 0, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell($llw, 0, 'Company name:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, 'MeaVita s.r.o.', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'Date:', 0, 0, 'L', false);
$date_of_issue = explode(' ', $invoice['CSInvoice']['date_of_issue']);
$date_of_issue = $date_of_issue[0];
$date_of_issue_info = db2cal_date($date_of_issue);
$tcpdf->Cell($rrw, 0, $date_of_issue_info, 0, 1, 'L', false);

$tcpdf->Cell($llw, 0, 'Address:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, 'Fillova 260/1', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'Order no.:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $invoice['CSInvoice']['order_number'], 0, 1, 'L', false);

$tcpdf->Cell($llw, 0, 'City, postal code:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, '638 00 Brno-Lesna', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'Country of origin:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, 'EU', 0, 1, 'L', false);

$tcpdf->Cell($w, 8, '', 0, 1, 'L', false);

$tcpdf->Cell($llw, 0, 'ID (IČ):', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, '29248400', 0, 0, 'L', false);
$tcpdf->SetFont($textfont,'B', 8);
$tcpdf->Cell($rw, 0, 'Customer (to):', 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell($llw, 0, 'VAT reg. no. (DIČ):', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, 'CZ29248400', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'Company name:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $invoice['BusinessPartner']['name'], 0, 1, 'L', false);

$tcpdf->Cell($llw, 0, 'Phone:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, '420 602 773 453', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'Address:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $street_info, 0, 1, 'L', false);

$tcpdf->Cell($llw, 0, 'E-mail:', 0, 0, 'L', false);
$tcpdf->Cell($lrw, 0, 'meavita@meavita.cz', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'City, postal code:', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $city_info, 0, 1, 'L', false);

$tcpdf->Cell($lw, 0, '', 0, 0, 'L', false);
$tcpdf->Cell($rlw, 0, 'VAT reg. no. (DIČ):', 0, 0, 'L', false);
$tcpdf->Cell($rrw, 0, $invoice['BusinessPartner']['dic'], 0, 1, 'L', false);
*/
$tcpdf->Cell($w, 5, "", 0, 1, 'L', false);

$payment_tbl = '
	<table cellspacing="0" cellpadding="1" border="0">
		<tr>
			<td style="width:' . $llw . 'mm">Payment:</td>
			<td style="width:' . $lrw . 'mm">100% in advance</td>
			<td style="width:' . $rlw . 'mm">Note:</td>
			<td style="width:' . $rrw . 'mm" rowspan="4">' . $invoice['CSInvoice']['note'] . '</td>
		</tr>
		<tr>
			<td>Bank name:</td>
			<td>Fio banka, a.s.</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>IBAN Account no.:</td>
			<td>CZ0620100000002000098174</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>SWIFT Code:</td>
			<td>FIOBCZPPXXX</td>
			<td>&nbsp;</td>
		</tr>
	</table>
';

$tcpdf->writeHTML($payment_tbl, true, false, false, false, '');

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tbl = '
<table cellspacing="0" cellpadding="1" border="0">
    <tr>
        <th style="width:100mm"><strong>Description</strong></th>
        <th style="width:20mm" align="center"><strong>Quantity</strong></th>
        <th style="width:35mm" align="right"><strong>Unit price without VAT</strong></th>
		<th style="width:35mm" align="right"><strong>Subtotal price without VAT</strong></th>
    </tr>
';

foreach ($invoice['CSTransactionItem'] as $transaction_item) {
	$tbl .= '
	<tr>
		<td nowrap="nowrap">' . $transaction_item['product_en_name'] . '</td>
		<td align="center">' . $transaction_item['quantity'] . '</td>
		<td align="right">' . format_price($transaction_item['price']) . '&nbsp;' . $invoice['Currency']['shortcut'] . '</td>
		<td align="right">' . format_price($transaction_item['price'] * $transaction_item['quantity']) . '&nbsp;' . $invoice['Currency']['shortcut'] . '</td>
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
$tcpdf->Cell($rlw, 0, "Total price without VAT", 0, 0, 'C', false);
$tcpdf->Cell($rrw, 0, format_price($invoice['CSInvoice']['amount']) . ' ' . $invoice['Currency']['shortcut'], 0, 1, 'R', false);

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$foot_tbl = '
<table cellspacing="0" cellpadding="1" border="0">
	<tr>
		<td style="width:100mm"></td>
		<td align="center" valign="bottom"><br/><br/><br/><br/><br/>Signature, stamp</td>
	</tr>
</table>
';

$tcpdf->writeHTML($foot_tbl, true, false, false, false, '');

echo $tcpdf->Output('meavita_invoice_' . $invoice['CSInvoice']['code'] . '.pdf', 'D');

?>