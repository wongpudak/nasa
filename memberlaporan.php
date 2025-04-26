<?php
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (!isset($user_ID) || $user_ID == 0) { 
	$refer = $_SERVER['REQUEST_URI'];
	header("Location: ".wp_login_url($refer));
}
if (isset($_GET['page']) && $_GET['page'] == 'laporan') {
	$showtxt .= '<h2>Laporan Keuangan</h2>';
}

if (isset($_GET['detil'])) {
	$exp = explode('-',$_GET['detil']);
	if (is_numeric($exp[0]) && is_numeric($exp[1])) {
		$tgl = $exp[0].'-'.$exp[1];
		$select = "SELECT * FROM `cb_laporan` WHERE `id_sponsor`=".$user_ID." AND MONTH(`tanggal`) = ".$exp[1]." AND YEAR(`tanggal`) = ".$exp[0]." ORDER BY `tanggal`";
		$data = $wpdb->get_results($select);
		$showtxt .= '
		<h4>Laporan '.$tgl.'</h4>
		<table class="table table-stripped">
		<thead class="thead-dark">
		<tr>
			<th>Tanggal</th>
			<th>Transaksi</th>
			<th style="text-align:right">Omset</th>
			<th style="text-align:right">Komisi</th>
		</tr>
		</thead>
		<tbody>';
		foreach ($data as $data) {
			$showtxt .= '
			<tr>
				<td>'.date('d-m H:i', strtotime($data->tanggal)).'</td>
				<td>'.$data->transaksi.'</td>';
			if ($data->komisi > 0) {
				$showtxt .= '
				<td style="text-align:right">'.number_format($data->kredit).'</td>
				<td style="text-align:right">'.number_format($data->komisi).'</td>
			</tr>';
		} elseif ($data->keterangan == 'refund') {
				$showtxt .= '
				<td style="text-align:right">'.number_format($data->kredit).'</td>
				<td style="text-align:right">'.number_format($data->komisi).'</td>
			</tr>';
			} elseif ($data->keterangan == 'wd') {
				$showtxt .= '
				<td style="text-align:right">0</td>
				<td style="text-align:right">-'.number_format($data->debet).'</td>
			</tr>';
			} else {				
				$showtxt .= '
				<td style="text-align:right">0</td>
				<td style="text-align:right">'.number_format($data->komisi).'</td></tr>';
			}
		}
		$showtxt .= '</tbody></table>';
	}
	//
} else {
	$data = $wpdb->get_results("SELECT SUM(`kredit`) AS `omset`, SUM(`komisi`) AS `totkomisi`, DATE_FORMAT( `tanggal`,  '%Y-%m' ) AS `bulan` FROM  `cb_laporan` WHERE `id_sponsor`=".$user_ID." GROUP BY `bulan` ORDER BY `tanggal` DESC");

	foreach ($data as $data) {
		$duit[$data->bulan]['omset'] = $data->omset;
		$duit[$data->bulan]['komisi'] = $data->totkomisi;
	}

	$now = date("Y-m-d");

	$showtxt .='
	<table class="table table-stripped">
		<thead class="thead-dark">
			<tr>
				<th>Bulan</th>
				<th style="text-align:right">Omset</th>
				<th style="text-align:right">Komisi</th>
			</tr>
		</thead>
		<tbody>';
			if (isset($duit) && is_array($duit)) {
				foreach ($duit as $key => $value) {
					$lap_url = add_query_arg('detil', $key, $_SERVER['REQUEST_URI']);
					$showtxt .= '<tr><td><a href="'.$lap_url.'">'.date('F Y',strtotime($key)).'</a></td>
					<td style="text-align:right">'.number_format($value['omset']).'</td></td>
					<td style="text-align:right">'.number_format($value['komisi']).'</td></td></tr>';
				}
			}
	$showtxt .='
		</tbody>
	</table>';
}
?>