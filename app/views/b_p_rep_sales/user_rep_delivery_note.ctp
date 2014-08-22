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
$tcpdf->Cell(190, 0, 'Dodací list', 0, 0, 'L', false);

// mezera
$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'B',11);
$tcpdf->Cell(100, 0, 'Dodavatel', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, 'Odběratel', 0, 1, 'L', false);

// mezera
$tcpdf->Cell(190, 3, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell(100, 0, 'MedicalCorp CZ s.r.o.', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $b_p_rep_sale['Rep']['first_name'] . ' ' . $b_p_rep_sale['Rep']['last_name'], 0, 1, 'L', false);

$rep_street_info = $b_p_rep_sale['RepAttribute']['street'] . ' ' . $b_p_rep_sale['RepAttribute']['street_number'];
$rep_city_info = $b_p_rep_sale['RepAttribute']['zip'] . ' ' . $b_p_rep_sale['RepAttribute']['city'];
$rep_ico_info = 'IČ: ' . $b_p_rep_sale['RepAttribute']['ico'];
$rep_dic_info = 'DIČ: ' . $b_p_rep_sale['RepAttribute']['dic'];

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

$tcpdf->SetFont($textfont,'BU', 11);
$tcpdf->Cell(190, 0, 'Datum vystavení: ' . czech_date($b_p_rep_sale['BPRepSale']['date_of_issue']), 0, 1, 'R', false);

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
    </tr>
';

foreach ($b_p_rep_sale['BPRepTransactionItem'] as $transaction_item) {
	$tbl .= '
	<tr>
		<td nowrap="nowrap">' . $transaction_item['product_name'] . '</td>
		<td>' . -$transaction_item['quantity'] . '</td>
	</tr>
';
}
$tbl .= '
</table>
';
				
$tcpdf->writeHTML($tbl, true, false, false, false, '');

$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$user_info = $b_p_rep_sale['User']['first_name'] . ' ' . $b_p_rep_sale['User']['last_name'];
$rep_info = $b_p_rep_sale['Rep']['first_name'] . ' ' . $b_p_rep_sale['Rep']['last_name'];

$tcpdf->Cell(100, 0, '.........................', 0, 0, 'C', false);
$tcpdf->Cell(90, 0, '.........................', 0, 1, 'C', false);
$tcpdf->Cell(100, 0, 'Vystavil: ' . $user_info, 0, 0, 'C', false);
$tcpdf->Cell(90, 0, 'Přijal: ' . $rep_info, 0, 1, 'C', false);

echo $tcpdf->Output('medical_corp_dodaci_list_repovi_' . $b_p_rep_sale['BPRepSale']['id'] . '.pdf', 'D');

?>