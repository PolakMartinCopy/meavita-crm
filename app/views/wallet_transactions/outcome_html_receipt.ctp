<table width="100%" border="1px" cellpadding="3">
	<tr>
		<td colspan="2" style="text-align:right">Číslo dokladu: <strong><?php echo $wallet_transaction['WalletTransaction']['code']?></strong></td>
	</tr>
	<tr>
		<td width="50%">
			<table>
				<tr><td>Organizace</td></tr>
				<tr><td><strong>MedicalCorp CZ s.r.o., Běhounská 677/15, Brno-střed, Brno-město</strong></td></tr>
				<tr><td><strong>IČ: 02646421</strong></td></tr>
			</table>
		</td>
		<td width="50%">
			<table width="100%">
				<tr>
					<td colspan="2">VÝDAJOVÝ</td>
				</tr>
				<tr>
					<td colspan="2">pokladní doklad č. <?php echo $wallet_transaction['WalletTransaction']['id']?></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td width="50%">ze dne: <?php echo czech_date($wallet_transaction['WalletTransaction']['created'])?></td>
					<td>Přílohy:</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="100%">
				<tr>
					<td width="20%">Vyplaceno (komu):<br/>(jméno, adresa)</td>
					<td colspan="5"><?php
						$info = array();
						$name = $wallet_transaction['WalletTransaction']['rep_first_name'];
						if (!empty($name)) {
							$name .= ' ';
						}
						$name .= $wallet_transaction['WalletTransaction']['rep_last_name'];
						$info[] = $name;
						$street = $wallet_transaction['WalletTransaction']['rep_street'];
						if (!empty($street)) {
							$street .= ' ';
						}
						$street .= $wallet_transaction['WalletTransaction']['rep_street_number'];
						$info[] = $street;
						$info[] = $wallet_transaction['WalletTransaction']['rep_city'];
						$info[] = $wallet_transaction['WalletTransaction']['rep_zip'];
						
						echo implode(', ', $info);
					?></td>
				</tr>
				<tr>
					<td>Průkaz totožnosti:</td>
					<td width="40%"></td>
					<td width="5%">Kč</td>
					<td width="15%"><?php echo round($wallet_transaction['WalletTransaction']['amount'])?></td>
					<td width="5%">h</td>
					<td width="15%">-</td>
				</tr>
				<tr>
					<td>Slovy:</td>
					<td colspan="5"><?php echo convert_number($wallet_transaction['WalletTransaction']['amount'])?>korunčeských</td>
				</tr>
				<tr>
					<td>Účel výplaty</td>
					<td colspan="5">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>Schválil(i)</td>
		<td>
			<table cellpadding="3" width="100%">
				<tr>
					<td width="50%">Podpis příjemce</td>
					<td>Podpis pokladníka</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;<br/>&nbsp;<br/>&nbsp;</td>
		<td>
			<table>
				<tr>
					<td></td>
					<td></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>Účetní doklad ze dne</td>
		<td>č.</td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="100%" border="1" cellpadding="3">
				<tr>
					<td width="35%">Text</td>
					<td width="35%">Účtovací předpis (Má dáti - účet)</td>
					<td width="20%">Kč</td>
					<td width="10%">h</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>Přezkoušel(i)</td>
		<td rowspan="2">
			<table width="100%">
				<tr>
					<td rowspan="2" width="50%">&nbsp;</td>
					<td>Zaúčtoval</td>
				</tr>
				<tr>
					<td>dne</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>dne</td>
	</tr>		
</table>