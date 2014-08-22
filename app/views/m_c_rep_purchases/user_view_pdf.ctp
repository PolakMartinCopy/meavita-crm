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
$linestyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(0, 0, 0));
$tcpdf->Line(10, 20, 200, 20, $linestyle);

$tcpdf->SetFont($textfont, 'B', 14);
$tcpdf->Cell(190, 0, 'Prodej', 0, 0, 'R', false);

// mezera
$tcpdf->Cell(190, 7, "", 0, 1, 'L', false);
$tcpdf->Ln();
$tcpdf->Cell(190, 7, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont, 'B',11);
$tcpdf->Cell(190, 0, 'Seznam produktů', 0, 1, 'L', false);
$tcpdf->Cell(190, 3, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont,'', 8);

$tbl = '
<table cellspacing="0" cellpadding="1" border="0" style="text-align:center">
    <tr>
        <th><strong>Popis zboží</strong></th>
        <th><strong>Množství</strong></th>
        <th><strong>Cena za MJ bez DPH</strong></th>
		<th><strong>Cena za MJ vč. DPH</strong></th>
    </tr>
';

foreach ($m_c_rep_purchase['MCRepTransactionItem'] as $transaction_item) {
	$tbl .= '
	<tr>
		<td nowrap="nowrap">' . $transaction_item['product_name'] . '</td>
		<td>' . $transaction_item['quantity'] . '</td>
		<td>' . number_format($transaction_item['price'], 2, ',', ' ') . ' Kč</td>
		<td>' . number_format($transaction_item['price_vat'], 2, ',', ' ')  . ' Kč</td>
	</tr>
';
}
$tbl .= '
</table>
';
				
$tcpdf->writeHTML($tbl, true, false, false, false, '');

$tcpdf->Cell(190, 5, "", 0, 1, 'L', false);

$tcpdf->SetFont($textfont, '', 8);
$tcpdf->Cell(190, 0, 'Základ: ' . number_format($m_c_rep_purchase['MCRepPurchase']['amount'], 2, ',', ' '). ' Kč', 0, 1, 'R', false);
$tcpdf->Cell(190, 0, 'Daň: ' . number_format($m_c_rep_purchase['MCRepPurchase']['amount_vat'] - $m_c_rep_purchase['MCRepPurchase']['amount'], 2, ',', ' '). ' Kč', 0, 1, 'R', false);

$tcpdf->SetFont($textfont, 'B', 8);

$tcpdf->Cell(190, 0, 'Celkem k úhradě: ' . number_format($m_c_rep_purchase['MCRepPurchase']['amount_vat'], 2, ',', ' ') . ' Kč', 0, 1, 'R', false);

echo $tcpdf->Output('m_c_rep_purchase_' . $m_c_rep_purchase['MCRepPurchase']['id'] . '.pdf', 'D');

?>