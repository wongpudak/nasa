<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } ?>
<?php
if (is_admin()) :
$datakomisi = get_option('komisi');
?>
<div class="wrap">
<h1 class="wp-heading-inline">Pengaturan Komisi</h1>
<a class="page-title-action" data-bs-toggle="collapse" href="#advanced" role="button" aria-expanded="false">Bantuan ⮟</a>
<hr class="wp-header-end">
<div class="collapse" id="advanced">	
<ul>
<li><strong>Komisi Upgrade</strong> bisa menggunakan persen (%) atau angka fix.</li>
<li><strong>Komisi Produk Lain</strong> bisa menggunakan persen (%) atau angka fix.</li>
<li><strong>Komisi Woocommerce</strong> hanya bisa persen.</li>
<li>Gunakan tanda % untuk komisi persen.</li>
<li>Level adalah tingkat kedalaman generasi (biasa dipakai untuk Multi Level Marketing)</li>
<li><a href="https://cafebisnis.com/pwa.php?id=9" target="_blank" class="btn btn-success">
		<span class="dashicons dashicons-controls-play"></span> Video Panduan Pengaturan Komisi</a></li>
</ul>
</div>
<?php
if (isset($_POST['premium']) && is_array($_POST['premium'])) {
	$i = 0;
	foreach ($_POST['premium'] as $premium) {
		if ($_POST['premium'][$i] != '' ||  $_POST['lainpremium'] != '' || $_POST['woopremium'] != '') {
			$komisi[$i]['premium'] = $premium;
			$komisi[$i]['free'] = $_POST['free'][$i];
			$komisi[$i]['lainpremium'] = $_POST['lainpremium'][$i];
			$komisi[$i]['lainfree'] = $_POST['lainfree'][$i];
			$komisi[$i]['woopremium'] = trim(str_replace('%', '', $_POST['woopremium'][$i]));
			$komisi[$i]['woofree'] = trim(str_replace('%', '', $_POST['woofree'][$i]));
			$i++;
		}
	}
	if (isset($komisi)) { $datakomisi['pps'] = $komisi; }
	$datakomisi['pplfree'] = $_POST['pplfree'];
	$datakomisi['pplpremium'] = $_POST['pplpremium'];
	update_option("komisi",$datakomisi);

	echo '<div class="notice notice-success is-dismissible"><p>Komisi Telah Diperbarui</p></div>';
}
?>


<form action="" method="post">   	
	<table class="table" style="width: 100%; max-width: 800px">
		<thead>
			<tr><th rowspan="2" width="16%">Level</th>
				<th colspan="2">Komisi Upgrade</th>
				<th colspan="2">Komisi Produk Lain</th>
				<th colspan="2">Komisi Woocommerce</th>
			<tr><th width="14%">Premium</th>
				<th width="14%">Free</th>
				<th width="14%">Premium</th>
				<th width="14%">Free</th>
				<th width="14%">Premium</th>
				<th width="14%">Free</th>
			</tr>
		</thead>
		<tbody>
	<?php 
	if ($datakomisi['pps']) {
		$i = 0;
		foreach($datakomisi['pps'] as $komisi):
			if (!empty($komisi['premium']) 
				|| !empty($komisi['free']) 
				|| !empty($komisi['lainpremium']) 
				|| !empty($komisi['lainfree']) 
				|| !empty($komisi['woopremium']) 
				|| !empty($komisi['woofree'])) {
		?>
		<tr><td><?php echo $i+1;?></td>				
			<td><input type="text" name="premium[]" class="form-control" value="<?php echo $komisi['premium'];?>" size="5"/></td>
			<td><input type="text" name="free[]" class="form-control" value="<?php echo $komisi['free'];?>" size="5"/></td>
			<td><input type="text" name="lainpremium[]" class="form-control" value="<?php echo $komisi['lainpremium'];?>" size="5"/></td>
			<td><input type="text" name="lainfree[]" class="form-control" value="<?php echo $komisi['lainfree'];?>" size="5"/></td>
			<td><input type="text" name="woopremium[]" class="form-control" value="<?php echo $komisi['woopremium'];?>" size="5"/></td>
			<td><input type="text" name="woofree[]" class="form-control" value="<?php echo $komisi['woofree'];?>" size="5"/></td>
		</tr>
		<?php
			$i++;
			} 
		
		endforeach;
	} else {
		echo '<tr><td>1</td>				
			<td><input type="text" name="premium[]" class="form-control" size="5"/></td>
			<td><input type="text" name="free[]" class="form-control" size="5"/></td>
			<td><input type="text" name="lainpremium[]" class="form-control" size="5"/></td>
			<td><input type="text" name="lainfree[]" class="form-control" size="5"/></td>
			<td><input type="text" name="woopremium[]" class="form-control" size="5"/></td>
			<td><input type="text" name="woofree[]" class="form-control" size="5"/></td>
		</tr>';
	}
	?>
	<tr id="dynamicInput"></tr>
		</tbody>
	</table>
	<p><input type="button" value="Tambah Komisi" class="button button-primary" id="AddLevel"></p>


<h4>Komisi Pay Per Lead (PPL)</h4>
<p>Komisi PPL adalah komisi yang diberikan untuk rekrutment free member. Saat ini baru bisa diberikan ke upline level 1 saja.</p>
	<p><span style="float:left; width:250px;">Komisi untuk Premium Member:</span> <input type="text" size="15" name="pplpremium" value="<?php echo $datakomisi['pplpremium']; ?>"/><br/>
	<span style="float:left; width:250px;">Komisi untuk Free Member:</span> <input type="text" size="15" name="pplfree" value="<?php echo $datakomisi['pplfree']; ?>"/><br/>
	<input type="submit" class="button button-primary" value="Update Data"/>
	</p>
</form>
</div>
<?php endif;?>