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
$worksheet->write($row, 0, 'Faktura číslo:', $header_left);
$worksheet->write($row, 3, $invoice['CSInvoice']['code'], $header_center);

$row += 2;
$worksheet->write($row, 0, 'Dodavatel', $content_bold);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'Název:', $content_common);
$worksheet->write($row, 1, 'MeaVita s.r.o.', $content_common);
$worksheet->write($row, 3, 'Datum vystavení:', $content_common);
$date_of_issue = explode(' ', $invoice['CSInvoice']['date_of_issue']);
$date_of_issue = $date_of_issue[0];
$date_of_issue_info = db2cal_date($date_of_issue);
$worksheet->write($row, 5, $date_of_issue_info, $content_common);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'Adresa:', $content_common);
$worksheet->write($row, 1, 'Fillova 260/1', $content_common);
$worksheet->write($row, 3, 'Datum splatnosti:', $content_bold);
$worksheet->write($row, 5, db2cal_date($invoice['CSInvoice']['due_date']), $content_bold);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'Místo, PSČ:', $content_common);
$worksheet->write($row, 1, 'Brno, 602 00', $content_common);
$worksheet->write($row, 3, 'Datum zdanitelného plnění:', $content_common);
$worksheet->write($row, 5, $date_of_issue_info, $content_common);

$row += 2;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'IČO:', $content_common);
$worksheet->write($row, 1, '29248400', $content_common);
$worksheet->write($row, 3, 'Odběratel:', $content_bold);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'DIČ:', $content_common);
$worksheet->write($row, 1, 'CZ29248400', $content_common);
$worksheet->write($row, 3, 'Název:', $content_bold);
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
$worksheet->write($row, 0, 'Telefon:', $content_common);
$worksheet->write($row, 1, '+420 722 779 110', $content_bold);
$worksheet->write($row, 3, 'Adresa:', $content_common);
$worksheet->write($row, 5, $street_info, $content_common);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'E-mail:', $content_common);
$worksheet->write($row, 1, 'objednavky@meavita.cz', $content_bold);
$worksheet->write($row, 3, 'PSČ, Místo:', $content_common);
$worksheet->write($row, 5, $city_info, $content_common);

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
$row++;
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 3, 'Kontaktní osoba:', $content_common);
$worksheet->write($row, 5, $contact_person_info, $content_bold);

$row++;
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 3, 'IČO:', $content_common);
$worksheet->write($row, 5, $invoice['BusinessPartner']['ico'], $content_common);

$row++;
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 3, 'DIČ:', $content_common);
$worksheet->write($row, 5, $invoice['BusinessPartner']['dic'], $content_common);

$row += 2;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->write($row, 0, 'Forma úhrady:', $content_bold);
$worksheet->write($row, 1, 'převodem', $content_bold);
$worksheet->write($row, 3, 'Poznámka:', $content_common);

$row++;
// pole pro obsah poznamky
$worksheet->mergeCells($row, 3, $row+2, 5);

$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->write($row, 0, 'Bankovní spojení:', $content_common);
$worksheet->write($row, 1, 'Fio banka, a.s.', $content_common);
$worksheet->write($row, 3, $invoice['CSInvoice']['note'], $content_common);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->write($row, 0, 'Číslo účtu:', $content_common);
$worksheet->write($row, 1, '2200096026 / 2010', $content_common);

$row++;
$worksheet->mergeCells($row, 1, $row, 2);
$worksheet->write($row, 0, 'Variabilní symbol:', $content_bold);
$worksheet->write($row, 1, $invoice['CSInvoice']['code'], $content_bold);

$row += 2;
$worksheet->mergeCells($row, 0, $row, 1);
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 0, 'Popis zboží', $content_bold);
$worksheet->write($row, 2, 'Množství', $content_bold_right);
$worksheet->write($row, 3, 'Cena za MJ bez DPH', $content_bold_right);
$worksheet->write($row, 5, 'Cena za MJ vč. DPH', $content_bold_right);

$row++;
foreach ($invoice['CSTransactionItem'] as $transaction_item) {
	$worksheet->mergeCells($row, 0, $row, 1);
	$worksheet->mergeCells($row, 3, $row, 4);
	$worksheet->write($row, 0, $transaction_item['product_name'], $content_common);
	$worksheet->write($row, 2, $transaction_item['quantity'], $content_right);
	$worksheet->write($row, 3, format_price($transaction_item['price']) . ' ' . $invoice['Currency']['shortcut'], $content_right);
	$worksheet->write($row, 5, format_price($transaction_item['price_vat']) . ' ' . $invoice['Currency']['shortcut'], $content_right);
	
	$row++;
}

$row++;
$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 3, 'Celkem k úhradě:', $content_bold);
$worksheet->write($row, 5, format_price($invoice['CSInvoice']['amount_vat']) . ' ' . $invoice['Currency']['shortcut'], $content_bold_right);

$row += 2;
$tax_class_rows = count($tax_classes);
// vypocitam vysku pro levy element s textem o uhrazeni ceny blablalba
$left_rows = $tax_class_rows + 5;
$worksheet->mergeCells($row, 0, $row + $left_rows, 1);
$worksheet->write($row, 0, 'Zboží zůstává až do úplného uhrazení kupní ceny majetkem společnosti MeaVita s.r.o.  Dovolujeme si Vás upozornit, že v případě nedodržení data splatnosti uvedeného na faktuře, Vám budeme účtovat úrok z prodlení v dohodnuté, resp. zákonné výši.', $content_common);

$worksheet->mergeCells($row, 3, $row, 4);
$worksheet->write($row, 2, 'sazba DPH', $content_bold_right);
$worksheet->write($row, 3, 'základ', $content_bold_right);
$worksheet->write($row, 5, 'DPH', $content_bold_right);

$row++;
foreach ($tax_classes as $tax_class) {
	$worksheet->mergeCells($row, 3, $row, 4);
	$worksheet->write($row, 2, $tax_class['TaxClass']['name'], $content_bold_right);
	$price_sum_info = '';
	$vat_info = '';
	if ($tax_class[0]['price_sum']) {
		$price_sum_info = format_price($tax_class[0]['price_sum']) . ' ' . $invoice['Currency']['shortcut'];
	}
	if ($tax_class[0]['vat']) {
		$vat_info = format_price($tax_class[0]['vat']) . ' ' . $invoice['Currency']['shortcut'];
	}
	$worksheet->write($row, 3, $price_sum_info, $content_bold_right);
	$worksheet->write($row, 5, $vat_info, $content_bold_right);
	
	$row++;
}


$worksheet->mergeCells($row, 2, $row + 3, 5);

$row += 4;
$worksheet->mergeCells($row, 2, $row, 5);
$worksheet->write($row, 2, 'podpis / razítko', $content_center);

// sending HTTP headers
$workbook->send('meavita_invoice_' . $invoice['CSInvoice']['code'] . '.xls');

// Let's send the file
$workbook->close();
?>