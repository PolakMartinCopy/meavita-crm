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

// logo meavita
$tcpdf->Image('img/meavita-small.png', 10, 15, 60, 15, 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);

// mezera
$tcpdf->Cell(190, 22, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'B',11);
$tcpdf->Cell(100, 0, 'Dodavatel', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, 'Odběratel', 0, 1, 'L', false);

// mezera
$tcpdf->Cell(190, 3, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);
$tcpdf->Cell(100, 0, 'MeaVita s.r.o.', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $b_p_c_s_rep_sale['CSRep']['first_name'] . ' ' . $b_p_c_s_rep_sale['CSRep']['last_name'], 0, 1, 'L', false);

$rep_street_info = $b_p_c_s_rep_sale['CSRepAttribute']['street'] . ' ' . $b_p_c_s_rep_sale['CSRepAttribute']['street_number'];
$rep_city_info = $b_p_c_s_rep_sale['CSRepAttribute']['zip'] . ' ' . $b_p_c_s_rep_sale['CSRepAttribute']['city'];
$rep_ico_info = 'IČ: ' . $b_p_c_s_rep_sale['CSRepAttribute']['ico'];
$rep_dic_info = 'DIČ: ' . $b_p_c_s_rep_sale['CSRepAttribute']['dic'];

$tcpdf->Cell(100, 0, 'Cejl 37/62', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $rep_street_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, 'Brno, 602 00', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $rep_city_info, 0, 1, 'L', false);

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tcpdf->Cell(100, 0, 'IČ: 29248400', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $rep_ico_info, 0, 1, 'L', false);

$tcpdf->Cell(100, 0, 'DIČ: CZ29248400', 0, 0, 'L', false);
$tcpdf->Cell(90, 0, $rep_dic_info, 0, 1, 'L', false);

$tcpdf->Cell(190, 15, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'BU', 11);
$tcpdf->Cell(190, 0, 'Datum vystavení: ' . czech_date($b_p_c_s_rep_sale['BPCSRepSale']['date_of_issue']), 0, 1, 'R', false);

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

foreach ($b_p_c_s_rep_sale['BPCSRepTransactionItem'] as $transaction_item) {
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

$user_info = $b_p_c_s_rep_sale['User']['first_name'] . ' ' . $b_p_c_s_rep_sale['User']['last_name'];
$rep_info = $b_p_c_s_rep_sale['CSRep']['first_name'] . ' ' . $b_p_c_s_rep_sale['CSRep']['last_name'];

$tcpdf->Cell(100, 0, '.........................', 0, 0, 'C', false);
$tcpdf->Cell(90, 0, '.........................', 0, 1, 'C', false);
$tcpdf->Cell(100, 0, 'Vystavil: ' . $user_info, 0, 0, 'C', false);
$tcpdf->Cell(90, 0, 'Přijal: ' . $rep_info, 0, 1, 'C', false);

echo $tcpdf->Output('meavita_dodaci_list_repovi_' . $b_p_c_s_rep_sale['BPCSRepSale']['id'] . '.pdf', 'D');

?>