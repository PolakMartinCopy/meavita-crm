<table width="100%" border="1px" cellpadding="3">
	<tr>
		<td colspan="2" style="text-align:right">Číslo dokladu: <strong><?php echo $c_s_wallet_transaction['CSWalletTransaction']['code']?></strong></td>
	</tr>
	<tr>
		<td width="50%">
			<table>
				<tr><td>Organizace</td></tr>
				<tr><td><strong>Meavita, s.r.o., Cejl 37/62, 60200 Brno</strong></td></tr>
				<tr><td><strong>IČ: 29248400</strong></td></tr>
			</table>
		</td>
		<td width="50%">
			<table width="100%">
				<tr>
					<td colspan="2">PŘÍJMOVÝ</td>
				</tr>
				<tr>
					<td colspan="2">pokladní doklad č. <?php echo $c_s_wallet_transaction['CSWalletTransaction']['id']?></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td width="50%">ze dne: <?php echo czech_date($c_s_wallet_transaction['CSWalletTransaction']['created'])?></td>
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
					<td width="20%">Přijato (od):<br/>(jméno, adresa)</td>
					<td colspan="5"><?php
						$info = array();
						$name = $c_s_wallet_transaction['CSWalletTransaction']['rep_first_name'];
						if (!empty($name)) {
							$name .= ' ';
						}
						$name .= $c_s_wallet_transaction['CSWalletTransaction']['rep_last_name'];
						$info[] = $name;
						$street = $c_s_wallet_transaction['CSWalletTransaction']['rep_street'];
						if (!empty($street)) {
							$street .= ' ';
						}
						$street .= $c_s_wallet_transaction['CSWalletTransaction']['rep_street_number'];
						$info[] = $street;
						$info[] = $c_s_wallet_transaction['CSWalletTransaction']['rep_city'];
						$info[] = $c_s_wallet_transaction['CSWalletTransaction']['rep_zip'];
						
						echo implode(', ', $info);
					?></td>
				</tr>
				<tr>
					<td>Průkaz totožnosti:</td>
					<td width="40%"></td>
					<td width="5%">Kč</td>
					<td width="15%"><?php
						$amount = abs(round($c_s_wallet_transaction['CSWalletTransaction']['amount']));
						echo $amount;
					?></td>
					<td width="5%">h</td>
					<td width="15%">-</td>
				</tr>
				<tr>
					<td>Slovy:</td>
					<td colspan="5"><?php echo convert_number($amount)?>korunčeských</td>
				</tr>
				<tr>
					<td>Účel platby</td>
					<td colspan="5">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>Schválil(i)</td>
		<td>Podpis pokladníka</td>
	</tr>
	<tr>
		<td>&nbsp;<br/>&nbsp;<br/>&nbsp;</td>
		<td>&nbsp;</td>
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
					<td width="35%">Účtovací předpis (Dal - účet)</td>
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