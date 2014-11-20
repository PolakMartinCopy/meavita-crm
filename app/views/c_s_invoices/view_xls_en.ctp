<?php
// celkova sirka
$w = 80;
// sirka leveho sirokeho sloupce
$lw = 42;
// sirka praveho sirokeho sloupce
$rw = 38;
// sirka leveho podsloupce v levem sloupci
$llw = 15;
// sirka praveho podsloupce v levem sloupci
$lrw = 27;
// sirka leveho podsloupce v levem podsloupci
$lrlw = 17;
// sirka praveho podsloupce v levem podsloupci
$lrrw = 10;
// sirka leveho podsloupce v pravem sloupci
$rlw = 20;
// sirka leveho podsloupce v levem podsloupci
$rllw = 10;
// sirka leveho podsloupce v pravem podsloupci
$rlrw = 10;
// sirka praveho podsloupce v pravem sloupci
$rrw = 18;

require_once 'Spreadsheet/Excel/Writer.php';

// Creating a workbook
$workbook = new Spreadsheet_Excel_Writer();

$workbook->setVersion(8);

// Creating a worksheet
$worksheet =& $workbook->addWorksheet();

$worksheet->setInputEncoding('utf-8');
$worksheet->hideGridlines();

$worksheet->setColumn(0, 0, $llw);
$worksheet->setColumn(1, 1, $lrlw);
$worksheet->setColumn(2, 2, $lrrw);
$worksheet->setColumn(3, 3, $rllw);
$worksheet->setColumn(4, 4, $rlrw);
$worksheet->setColumn(5, 5, $rrw);

$header_format = array(
	'Size' => 14,
	'Bold' => true
);

$content_format = array(
	'Size' => 8,
	'Align' => 'left'
);

$content_common = $workbook->addFormat($content_format);
$content_common->setTextWrap();
$content_right = $workbook->addFormat(array_merge($content_format, array('Align' => 'right')));
$content_right->setTextWrap();
$content_center = $workbook->addFormat(array_merge($content_format, array('Align' => 'center')));
$content_center->setTextWrap();
$content_bold = $workbook->addFormat($content_format + array('Bold' => true));
$content_bold->setTextWrap();
$content_bold_right = $workbook->addFormat(array_merge($content_format, array('Bold' => true, 'Align' => 'right')));
$content_bold_right->setTextWrap();

$header_left = $workbook->addFormat($header_format + array('Align' => 'left'));
$header_center = $workbook->addFormat($header_format + array('Align' => 'center'));

$row = 0;
// The actual data
$worksheet->mergeCells($row, 0, $row, 2);
$worksheet->mergeCells($row, 3, $row, 5);
$worksheet->write($row, 0, 'Invoice:', $header_left);
$worksheet->write($row, 3, 'Inv. ' . $invoice['CSInvoice']['code'], $header_center);

$row += 2;
$worksheet->write($row, 0, 'Supplier (from):', $content_bold);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'Company name:', $content_common);
$worksheet->write($row, 1, 'MeaVita s.r.o.', $content_common);
$worksheet->write($row, 3, 'Date:', $content_common);
$date_of_issue = explode(' ', $invoice['CSInvoice']['date_of_issue']);
$date_of_issue = $date_of_issue[0];
$date_of_issue_info = db2cal_date($date_of_issue);
$worksheet->write($row, 5, $date_of_issue_info, $content_common);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'Address:', $content_common);
$worksheet->write($row, 1, 'Fillova 260/1', $content_common);
$worksheet->write($row, 3, 'Order no.:', $content_bold);
$worksheet->write($row, 5, $invoice['CSInvoice']['order_number'], $content_bold);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'City, postal code:', $content_common);
$worksheet->write($row, 1, '638 00 Brno-Lesna', $content_common);
$worksheet->write($row, 3, 'Country of origin:', $content_common);
$worksheet->write($row, 5, 'EU', $content_common);

$row += 2;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'ID (IČ):', $content_common);
$worksheet->write($row, 1, '29248400', $content_common);
$worksheet->write($row, 3, 'Customer (to)::', $content_bold);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'VAT reg. no. (DIČ):', $content_common);
$worksheet->write($row, 1, 'CZ29248400', $content_common);
$worksheet->write($row, 3, 'Company name:', $content_bold);
$worksheet->write($row, 5, $invoice['BusinessPartner']['name'], $content_bold);

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

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'Phone:', $content_common);
$worksheet->write($row, 1, '420 602 773 453', $content_bold);
$worksheet->write($row, 3, 'Address:', $content_common);
$worksheet->write($row, 5, $street_info, $content_common);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'E-mail:', $content_common);
$worksheet->write($row, 1, 'meavita@meavita.cz', $content_bold);
$worksheet->write($row, 3, 'City, postal code:', $content_common);
$worksheet->write($row, 5, $city_info, $content_common);

$row++;
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 3, 'VAT reg. no. (DIČ):', $content_common);
$worksheet->write($row, 5, $invoice['BusinessPartner']['dic'], $content_common);

$row += 2;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->write($row, 0, 'Payment:', $content_bold);
$worksheet->write($row, 1, '100% in advance', $content_bold);
$worksheet->write($row, 3, '  Note:', $content_common);

$row++;
// pole pro obsah poznamky
$worksheet->mergeCells($row, 3, $row+2, 5);

$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->write($row, 0, 'Bank name:', $content_common);
$worksheet->write($row, 1, 'Fio banka, a.s.', $content_common);
$worksheet->write($row, 3, $invoice['CSInvoice']['note'], $content_common);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->write($row, 0, 'IBAN Account no.:', $content_common);
$worksheet->write($row, 1, 'CZ0620100000002000098174', $content_common);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->write($row, 0, 'SWIFT Code:', $content_bold);
$worksheet->write($row, 1, 'FIOBCZPPXXX', $content_bold);

$row += 2;
$worksheet->mergeCells($row, 0, $row, 1);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'Description', $content_bold);
$worksheet->write($row, 2, 'Quantity', $content_bold_right);
$worksheet->write($row, 3, 'Unit price without VAT', $content_bold_right);
$worksheet->write($row, 5, 'Subtotal price without VAT', $content_bold_right);

$row++;
foreach ($invoice['CSTransactionItem'] as $transaction_item) {
	$worksheet->mergeCells($row, 0, $row, 1);
	$worksheet->mergeCells($row, 3, $row, 4);
	$worksheet->write($row, 0, $transaction_item['product_en_name'], $content_common);
	$worksheet->write($row, 2, $transaction_item['quantity'], $content_right);
	$worksheet->write($row, 3, format_price($transaction_item['price']) . ' ' . $invoice['Currency']['shortcut'], $content_right);
	$worksheet->write($row, 5, format_price($transaction_item['price'] * $transaction_item['quantity']) . ' ' . $invoice['Currency']['shortcut'], $content_right);
	
	$row++;
}

$row++;
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 3, 'Total price without VAT:', $content_bold);
$worksheet->write($row, 5, format_price($invoice['CSInvoice']['amount']) . ' ' . $invoice['Currency']['shortcut'], $content_bold_right);

$row += 2;

$worksheet->mergeCells($row, 0, $row + 5, 2);
$worksheet->mergeCells($row, 3, $row + 5, 5);

$row += 6;
$worksheet->mergeCells($row, 3, $row, 5);
$worksheet->write($row, 3, 'Signature, stamp', $content_center);

// sending HTTP headers
$workbook->send('meavita_invoice_' . $invoice['CSInvoice']['code'] . '.xls');

// Let's send the file
$workbook->close();
?>