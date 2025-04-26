<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
$order = $wpdb->get_results("SELECT *,`cb_produklain`.`id` AS `idorder` FROM `cb_produklain` LEFT JOIN `cb_produk` ON `cb_produk`.`id` = `cb_produklain`.`idproduk` WHERE `cb_produklain`.`idwp` =".$user_ID);

if (count($order) > 0) {
	$i = 0;
	$showtxt = '<table class="widefat">
	<thead>
	<tr>
		<th width="5%">ID</th>
		<th width="15%">Tgl. Order</th>
		<th width="30%">Produk</th>
		<th width="35%">Harga.</th>	
		<th width="15%">Action</th>
	</tr>
	</thead>
	<tbody>';
	foreach ($order as $order) {
		$showtxt .= '
		<tr';
		if ($i == 0) { $showtxt .= ' class="alternate"'; $i=1;} else {$i=0;}
		$showtxt .= '><td>'.$order->idorder.'</td>
		<td>'.$order->tgl_order.'</td>
		<td>'.$order->nama.'</td>
		<td>'.$order->hargaproduk.'</td>
		</tr>';
	}

	$showtxt .= '</tbody></table>';
} else {
	$showtxt = 'Belum ada produk';
}
?>
