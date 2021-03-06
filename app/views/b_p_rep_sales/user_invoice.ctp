<?php
App::import('Vendor','xtcpdf');

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

$tcpdf->SetFont($textfont, 'B', 14);
$tcpdf->Cell(190, 0, 'Faktura č. ' . $b_p_rep_sale['BPRepSale']['code'], 0, 0, 'L', false);

// mezera
$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'B',11);
$tcpdf->Cell(100, 0, 'Dodavatel', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, 'Odběratel', 0, 1, 'L', false);

// mezera
$tcpdf->Cell(190, 3, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell(100, 0, 'MedicalCorp CZ s.r.o.', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $b_p_rep_sale['BusinessPartner']['name'], 0, 1, 'L', false);

$rep_street_info = $b_p_rep_sale['Address']['street'] . ' ' . $b_p_rep_sale['Address']['number'];
$rep_city_info = $b_p_rep_sale['Address']['zip'] . ' ' . $b_p_rep_sale['Address']['city'];
$rep_ico_info = 'IČ: ' . $b_p_rep_sale['BusinessPartner']['ico'];
$rep_dic_info = 'DIČ: ' . $b_p_rep_sale['BusinessPartner']['dic'];

$tcpdf->Cell(100, 0, 'Běhounská 677/15', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $rep_street_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, 'Brno-střed, Brno-město', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $rep_city_info, 0, 1, 'L', false);

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tcpdf->Cell(100, 0, 'IČ: 02646421', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $rep_ico_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, 'DIČ: CZ02646421', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $rep_dic_info, 0, 1, 'L', false);

$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$date_of_issue = explode(' ', $b_p_rep_sale['BPRepSale']['date_of_issue']);
$date_of_issue = $date_of_issue[0];
$date_of_issue_info = 'Datum vystavení: ' . db2cal_date($date_of_issue);

$tcpdf->Cell(100, 0, '', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $date_of_issue_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, 'Číslo účtu: 2400317559/2010', 0, 0, 'L', false);
$tcpdf->SetFont($textfont, 'B', 8);
$tcpdf->Cell(90, 0, 'Datum splatnosti: ' . db2cal_date($b_p_rep_sale['BPRepSale']['due_date']), 0, 1, 'L', false);
$tcpdf->SetFont($textfont,'', 8);
	
$taxable_event_info = 'Datum zdanitelného plnění: ' . db2cal_date($date_of_issue);
$tcpdf->Cell(100, 0, 'Variabilní symbol: ' . $b_p_rep_sale['BPRepSale']['code'], 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $taxable_event_info, 0, 1, 'L', false);	

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

// mezera
$tcpdf->Cell(190, 7, "", 0, 1, 'L', false);
$tcpdf->Ln();
$tcpdf->Cell(190, 7, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont, 'B',11);
$tcpdf->Cell(190, 0, 'Seznam produktů', 0, 1, 'L', false);
$tcpdf->Cell(190, 3, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);

$tbl = '
<table cellspacing="0" cellpadding="1" border="0" style="text-align:center" width="100%">
    <tr>
        <th><strong>Popis zboží</strong></th>
        <th><strong>Množství</strong></th>
		<th><strong>Cena za MJ<br/>bez DPH</strong></th>
		<th><strong>Cena za MJ<br/>včetně DPH</strong></th>
    </tr>
';

foreach ($b_p_rep_sale['BPRepTransactionItem'] as $transaction_item) {
	$tbl .= '
	<tr>
		<td nowrap="nowrap">' . $transaction_item['product_name'] . '</td>
		<td>' . $transaction_item['quantity'] . '</td>
		<td>' . number_format($transaction_item['price'], 2, ',', ' ') . ' Kč</td>
		<td>' . number_format($transaction_item['price_vat'], 2, ',', ' ') . ' Kč</td>
	</tr>
';
}
$tbl .= '
</table>
';
				
$tcpdf->writeHTML($tbl, true, false, false, false, '');

$tcpdf->SetFont($textfont, '', 8);
$tcpdf->Cell(190, 0, 'Základ: ' . number_format($b_p_rep_sale['BPRepSale']['amount'], 2, ',', ' '). ' Kč', 0, 1, 'R', false);
$tcpdf->Cell(190, 0, 'Daň: ' . number_format($b_p_rep_sale['BPRepSale']['amount_vat'] - $b_p_rep_sale['BPRepSale']['amount'], 2, ',', ' '). ' Kč', 0, 1, 'R', false);

$round = round($b_p_rep_sale['BPRepSale']['amount_vat']);
$round_diff = $round - $b_p_rep_sale['BPRepSale']['amount_vat'];
$round_info = 'Zaokrouhlení: ' . number_format($round_diff, 2, ',', ' ') . ' Kč';
$tcpdf->Cell(190, 0, $round_info, 0, 1, 'R', false);

$tcpdf->SetFont($textfont, 'B', 8);

$tcpdf->Cell(190, 0, 'Celkem k úhradě: ' . number_format($round, 2, ',', ' ') . ' Kč', 0, 1, 'R', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$user_info = $b_p_rep_sale['User']['first_name'] . ' ' . $b_p_rep_sale['User']['last_name'];
$rep_info = $b_p_rep_sale['BusinessPartner']['name'];

$tcpdf->Cell(100, 0, '.........................', 0, 0, 'C', false);
$tcpdf->Cell(90, 0, '.........................', 0, 1, 'C', false);
$tcpdf->Cell(100, 0, 'Vystavil: ' . $user_info, 0, 0, 'C', false);
$tcpdf->Cell(90, 0, 'Přijal: ' . $rep_info, 0, 1, 'C', false);

echo $tcpdf->Output('medical_corp_faktura_obchodnimu_partnerovi_' . $b_p_rep_sale['BPRepSale']['id'] . '.pdf', 'D');

?>