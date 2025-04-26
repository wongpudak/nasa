<?php
function datasponsor() {
	global $wpdb, $user_ID;
	$idsponsor = $wpdb->get_var("SELECT `id_referral` FROM `wp_member` WHERE `idwp`=".$user_ID);
	if ($idsponsor > 0) {
		$datasponsor = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`=".$idsponsor);
		$custom = unserialize($datasponsor->homepage);
		$aturform = get_option('aturform');
		$form = unserialize($aturform);
		if (is_array($form)) {
			$c = 0;
				echo '<table class="widefat">';
				foreach ($form as $form) {
					if (isset($form['jaringan']) && $form['jaringan'] == 1) {
						if (!$form['label']) {
							switch ($form['field']) {
								case 'nama' : $label = 'Nama Lengkap'; $required = 'required'; break;
								case 'id_tianshi' : $label = 'ID MLM'; break;
								case 'email' : $label = 'Email'; break;
								case 'ktp' : $label = 'No. KTP'; break;
								case 'tgl_lahir' : $label = 'Tanggal Lahir'; break;
								case 'alamat' : $label = 'Alamat'; break;
								case 'kota' : $label = 'Kota'; break;
								case 'provinsi' : $label = 'Provinsi'; break;
								case 'kodepos' : $label = 'Kodepos'; break;
								case 'telp' : $label = 'No. Telp / HP'; break;
								case 'ktp_istri' : $label = 'No. KTP Pasangan'; break;
								case 'nama_istri' : $label = 'Nama Pasangan'; break;
								case 'tgl_lahir_istri' : $label = 'Tgl Lahir Pasangan'; break;
								case 'ac' : $label = 'Atas Nama'; break;
								case 'bank' : $label = 'Nama Bank'; break;
								case 'rekening' : $label = 'No. Rekening'; break;
								case 'kelamin' : $label = 'Jenis Kelamin'; break;
								case 'username' : $label = 'Username'; break;
								case 'password' : $label = 'Password'; break;
								case 'subdomain' : $label = 'URL Affiliasi'; break;
								case 'customwhatsapp' : $label = 'WhatsApp'; break;
								case 'ym' : $label = 'Yahoo Messenger'; break;
							}
						} else {
							$label = $form['label'];
						}

						if ($form['field'] == 'keterangan') {
						echo '
						<tr>
						  <td colspan="2"><strong>'.$label.'</strong></td>
						</tr>';	
						} elseif ($form['field'] == 'username') {
						echo '
						<tr>
						  <td>'.$label.'</td>
						  <td>'.$datasponsor->{$form['field']}.'</td>
						</tr>';
						} elseif ($form['field'] == 'subdomain') {
						echo '
						<tr>
						  <td>'.$label.'</td>
						  <td>'.urlaff($datasponsor->{$form['field']}).'</td>
						</tr>';
						} elseif ($form['field'] == 'ym') {
						echo '
						<tr>
						  <td>'.$label.'</td>
						  <td>'.$custom[0]??=''.'</td>
						</tr>';
						} elseif ($form['field'] == 'customwhatsapp') {
						echo '
						<tr>
						  <td>'.$label.'</td>
						  <td><a href="https://wa.me/'.formatwa($custom['whatsapp']??='').'">'.formatwa($custom['whatsapp']??='').'</a></td>
						</tr>';
						} elseif ($form['field'] == 'password') {
							echo '
							<tr>
							  <td>'.$label.'</td>
							  <td><em>disembunyikan</em></td>
							</tr>';
						} elseif ($form['field'] == 'kelamin') {
							$pria = $wanita = '';
							if ($datasponsor->kelamin == 1) { $kelamin = 'Pria'; } else { $kelamin = 'Wanita'; }
							echo '
							<tr>
							  <td>'.$label.'</td>
							  <td>'.$kelamin.'</td>
							</tr>';
						} elseif ($form['field'] == 'custom') {
						echo '
						<tr>
						  <td>'.$label.'</td>
						  <td>';
						  if (isset($custom[$c+1])) {
						  echo $custom[$c+1];
						  }
						  echo '</td>
						</tr>';
						$c++;
						} else {
						echo '
						<tr>
						  <td>'.$label.'</td>
						  <td>'.$datasponsor->{$form['field']}.'</td>
						</tr>';
						}
					}
				}
				echo '</table>';
		}
	} else {
		echo 'Anda tidak memiliki Sponsor atau sponsor anda terhapus';
	}
}

function linkaffiliasi() {
  $data = unserialize(CB_MEMBER);
  $data = get_object_vars($data);
  $urlaff = urlaff($data['subdomain']);
  $linklist = get_option('widget_linkaffiliasi');
  echo '
  <ul><li><b><a href="'.$urlaff.'">'.$urlaff.'</a></b></li>';
	if (substr(get_urlpendek($urlaff), 0, 4) == 'http') {
		echo '<li><a href="'.get_urlpendek($urlaff).'">'.get_urlpendek($urlaff).'</a></li>';
	}

	if (!empty($linklist)) {
		$list = explode("\n", $linklist);
		if (count($list) > 0) {
			foreach ($list as $list) {
				echo '<li><a href="'.trim($list).'?reg='.$data['subdomain'].'">'.trim($list).'?reg='.$data['subdomain'].'</a></li>';
			}
		}
	}

	echo '</ul>';

	if (current_user_can('administrator')) {
  	echo '<p><a href="admin.php?page=cbaf_editwidget&widget=linkaffiliasi">Edit</a></p>';
  }
}

function leaderboard() {  
  $setwidget = array(
  	'kriteria' => 'komisi',
  	'jmllist' => 10,
  	'start' => wp_date("Y-m-d", strtotime('-1 month')),
  	'end' => wp_date("Y-m-d"),
  	'before' => '<ol>',
  	'after' => '</ol>'
  );

  $getoption = get_option('widget_leaderboard');
  if ($getoption !== false) { $setwidget = $getoption; }

  $kriteria = $setwidget['kriteria']??='komisi';
  $jmllist = $setwidget['jmllist']??=10;
  if (isset($setwidget['start']) && !empty($setwidget['start'])) { $start = $setwidget['start']; } else { $start = date("Y-m-d", strtotime('-1 month')); }
  if (isset($setwidget['end']) && !empty($setwidget['end'])) { $end = $setwidget['end']; } else { $end = date("Y-m-d"); }
  $format = $setwidget['format']??='<li>{nama} - {total}</li>';
  echo $setwidget['before']??='<ol>';
  echo do_shortcode('[cb_leaderboard data="'.$kriteria.'" jumlah="'.$jmllist.'" start="'.$start.'" end="'.$end.'" format="'.$format.'"]');
  echo $setwidget['after']??'</ol>';
  if (current_user_can('administrator')) {
  	echo '<p><a href="admin.php?page=cbaf_editwidget&widget=leaderboard">Edit</a></p>';
  }
}

function aksesproduk() {
	global $wpdb, $user_ID;
	$options = get_option('cb_pengaturan');
	$pageorder = $options['order'];
	$id_user= $wpdb->get_var("SELECT `id_user` FROM `wp_member` WHERE `idwp`=".$user_ID);
  $data = $wpdb->get_results("SELECT *,
  	`cb_produklain`.`id` AS `idorder`,
  	`cb_produk`.`nama` AS `namaproduk`
  	FROM `cb_produklain` 
  	LEFT JOIN `cb_produk` ON `cb_produk`.`id` = `cb_produklain`.`idproduk` 
  	WHERE `cb_produklain`.`status`=1 AND `id_user`=".$id_user);
  $i = 0;
  if (count($data) > 0) {
  	echo '
  	<table class="widefat">
  	<thead><tr><th>Produk</th><th>&nbsp;</th></tr></thead>
  	<tbody>';
  	foreach ($data as $data) {
  		echo '
  		<tr';
  		if ($i == 0) { echo ' class="alternate"'; $i=1;} else {$i=0;}
  		echo '>
  		<td>'.$data->namaproduk.'</td>
  		<td class="alignright"><a href="'.esc_url( add_query_arg( 'download_id', $data->idproduk, home_url( '/' ) ) ).'" class="button button-primary">Akses</a></td>
  		</tr>';
  	}
  	echo '</tbody></table>';
  } else {
  	echo 'Belum ada produk';
  }
}

function tagihan() {
	global $wpdb, $user_ID;
	$options = get_option('cb_pengaturan');
	$pageorder = $options['order'];
	$id_user= $wpdb->get_var("SELECT `id_user` FROM `wp_member` WHERE `idwp`=".$user_ID);
  $data = $wpdb->get_results("SELECT *,
  	`cb_produklain`.`id` AS `idorder`,
  	`cb_produk`.`nama` AS `namaproduk`,
  	`cb_produklain`.`status` AS `statusorder`
  	FROM `cb_produklain` 
  	LEFT JOIN `cb_produk` ON `cb_produk`.`id` = `cb_produklain`.`idproduk` 
  	WHERE `cb_produklain`.`status`=0 AND `id_user`=".$id_user);
  $i = 0;
  if (count($data) > 0) {
  	echo '
  	<table class="widefat">
  	<thead><tr><th>ID</th><th>Produk</th><th><span class="alignright">Total</span></th></tr></thead>
  	<tbody>';
  	foreach ($data as $data) {
  		echo '
  		<tr';
  		if ($i == 0) { echo ' class="alternate"'; $i=1;} else {$i=0;}
  		echo '>
  		<td><a href="'.site_url().'?page_id='.$pageorder.'&idorder='.$data->idorder.'"
  		target="_blank">'.$data->idorder.'</a></td>
  		<td>'.$data->namaproduk.'</td>
  		<td class="alignright">'.number_format($data->hargaproduk).'</td>
  		</tr>';
  	}
  	echo '</tbody></table>';
  } else {
  	echo 'Belum ada order';
  }
}

function prospek() {
  global $wpdb, $user_ID;
  $data = $wpdb->get_results("SELECT * FROM `wp_member` WHERE `membership`<2 AND `id_referral`=".$user_ID." ORDER BY `tgl_daftar` DESC LIMIT 0,10");
  if (count($data) > 0) {
  	echo '
  	<table class="widefat">
  	<thead>
  		<tr><th>Nama</th>
  		<th>WA / Telp</th>
  		</tr>
  	</thead>
  	<tbody>';
  	$i = 0;
  	foreach ($data as $data) {
  		if (!empty($data->homepage)) {
  			$datalain = unserialize($data->homepage);
  		}

  		echo '
  		<tr';
  		if ($i == 0) { echo ' class="alternate"'; $i=1;} else {$i=0;}
  		echo '><td>'.$data->nama.'</td>
  		<td>';
  		if (isset($datalain['whatsapp'])) {
  			echo '<a href="https://wa.me/'.formatwa($datalain['whatsapp']).'" target="_blank">'.formatwa($datalain['whatsapp']).'</a>';
  		} else {
  			echo $data->telp;
  		}
  		echo '</td></tr>';
  	}
  	echo '
  	</tbody>
  	</table>';
  }
}

function statistik() {
  $data = unserialize(CB_MEMBER);
  $data = get_object_vars($data);
  echo '
  <table class="widefat">  
  <tr><td>Total Order</td><td class="alignright">'.$data['totalorder'].'</td></tr>
  <tr class="alternate"><td>Omset</td><td class="alignright">'.$data['omset'].'</td></tr>
	<tr><td>Komisi Dicairkan</td><td class="alignright">'.$data['komisicair'].'</td></tr>
	<tr class="alternate"><td>Komisi Tertahan</td><td class="alignright">'.$data['komisitertahan'].'</td></tr>
	<tr><td>Total Komisi</td><td class="alignright">'.$data['totalkomisi'].'</td></tr>
	<tr class="alternate"><td>Invalid Member</td><td class="alignright">'.$data['jmlinvalid'].'</td></tr>
	<tr><td>Free Member</td><td class="alignright">'.$data['jmlfree'].'</td></tr>
	<tr class="alternate"><td>Premium Member</td><td class="alignright">'.$data['jmlpremium'].'</td></tr>
	<tr><td>Downline</td><td class="alignright">'.$data['jmldownline'].'</td></tr>
	</table>';	
}

function pengunjung() {
  global $wpdb, $user_ID;
  $visitor = visitor_data($user_ID);
  $chart = ''; 
  if (count($visitor) > 0) {     
    foreach ($visitor as $key => $value) {    
      if (strtotime($key) > strtotime('-30 days')) {
        $chart .= "['".date('Y-m-d',strtotime($key))."',".$value."],";
      }
    }
    
  }

  $show = '  
  <div id="chart_div" style="margin:30px 0 0 0; width: 100%; height: 300px"></div>
    
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load(\'current\', {\'packages\':[\'corechart\']});
    google.charts.setOnLoadCallback(drawChart); 
    function drawChart() {
      var dataTable = new google.visualization.DataTable();
      dataTable.addColumn(\'string\', \'Tanggal\');
      dataTable.addColumn(\'number\', \'Jml Pengunjung\');
      // A column for custom tooltip content
      
      dataTable.addRows([
        '.substr($chart, 0,-1).'
        // Treat first row as data as well.
      ]);     
    var options = {
        series: {
          1: {type: \'line\'}
        },      
        vAxis: { title: \'Jml Pengunjung\' },
        hAxis: { title: \'Tanggal\' },
        legend: {position: \'top\'},
        chartArea:{left:70,top:10,width:\'100%\',height:\'70%\'}
    };
    var chart = new google.visualization.LineChart(document.getElementById(\'chart_div\'));
      chart.draw(dataTable, options);
    }
  </script>
  ';

  echo $show;
}

function add_custom_dashboard_widgets() {
    $modul = array('Data Sponsor','Link Affiliasi','Leaderboard','Akses Produk','Tagihan','Prospek', 'Statistik','Pengunjung');
    foreach ($modul as $modul) {
    	$mod = strtolower(txtonly($modul));
    	wp_add_dashboard_widget($mod, $modul, $mod);
    }
}

add_action('wp_dashboard_setup', 'add_custom_dashboard_widgets');

function hidden_admin_page() {
    add_menu_page(
        'Edit Widget',         // Page title
        '',                    // Menu title (kosong agar tidak terlihat)
        'manage_options',      // Kapabilitas
        'cbaf_editwidget',     // Slug halaman
        'editwidget_callback', // Callback function
        ' ',    // Gunakan ikon tidak terlihat
        9999                   // Posisi di bagian bawah (tidak mengganggu)
    );
}
add_action('admin_menu', 'hidden_admin_page');


function editwidget_callback() {
  global $wpdb, $user_ID;
  if (isset($_GET['widget'])) {  	
  	switch ($_GET['widget']) {
  		case 'leaderboard':
  			if (isset($_POST['kriteria']) && is_numeric($_POST['jmllist'])) {  				
  				update_option('widget_leaderboard', $_POST);
  				echo '<div class="notice notice-success is-dismissible">
					<p>Pengaturan telah disimpan</p>
					</div>';
  			}

  			$setwidget = array(
			  	'kriteria' => 'komisi',
			  	'jmllist' => 10,
			  	'start' => wp_date("Y-m-d", strtotime('-1 month')),
			  	'end' => wp_date("Y-m-d"),
			  	'before' => '<ol>',
			  	'after' => '</ol>'
			  );

  			$getoption = get_option('widget_leaderboard');
  			if ($getoption !== false) { $setwidget = $getoption; }

  			if (isset($setwidget['kriteria']) && !empty($setwidget['kriteria'])) {
  				$select[$setwidget['kriteria']] = ' selected';
  			}
  			$title = 'Edit Leaderboard';
  			$showtxt = '
  			<form action="" method="post">
  			  <div class="mb-3 row">
				    <label for="kriteria" class="col-sm-2 col-form-label">Kriteria</label>
				    <div class="col-sm-5">
				      <select name="kriteria" class="form-select">
				      	<option value="komisi"'.($select['komisi']??='').'>Komisi Terbanyak</option>
				      	<option value="sale"'.($select['sale']??='').'>Penjualan Terbanyak</option>
				      	<option value="premium"'.($select['premium']??='').'>Member Premium Terbanyak</option>
				      	<option value="rekrut"'.($select['rekrut']??='').'>Rekrut Terbanyak</option>				      	
				      </select>
				    </div>
				  </div>
				  <div class="mb-3 row">
				    <label for="jmllist" class="col-sm-2 col-form-label">Jumlah List</label>
				    <div class="col-sm-2">
				      <input type="number" class="form-control" name="jmllist" value="'.($setwidget['jmllist']??=10).'" id="jmllist">
				    </div>
				  </div>
				  <div class="mb-3 row">
				    <label for="start" class="col-sm-2 col-form-label">Range Start</label>
				    <div class="col-sm-2">
				      <input type="date" class="form-control" name="start" value="'.($setwidget['start']??='').'" id="start">
				    </div>
				  </div>
				  <div class="mb-3 row">
				    <label for="end" class="col-sm-2 col-form-label">Range End</label>
				    <div class="col-sm-2">
				      <input type="date" class="form-control" name="end" value="'.($setwidget['end']??='').'" id="end">
				    </div>
				  </div>
				  <div class="mb-3 row">
				    <label for="before" class="col-sm-2 col-form-label">Sebelum List</label>
				    <div class="col-sm-2">
				      <input type="text" class="form-control" name="before" value="'.($setwidget['before']??='<ol>').'" id="before">
				    </div>
				  </div>
				  <div class="mb-3 row">
				    <label for="format" class="col-sm-2 col-form-label">List Format</label>
				    <div class="col-sm-2">
				      <input type="text" class="form-control" name="format" value="'.($setwidget['format']??='<li>{nama} - {total}</li>').'" id="format">
				    </div>
				  </div>
				  <div class="mb-3 row">
				    <label for="after" class="col-sm-2 col-form-label">Setelah List</label>
				    <div class="col-sm-2">
				      <input type="text" class="form-control" name="after" value="'.($setwidget['after']??='</ol>').'" id="after">
				    </div>
				  </div>
				  <input type="submit" value="Simpan" class="button button-primary"/>
  			</form>';
  			break;
  		case 'linkaffiliasi':
  			if (isset($_POST['linkaffiliasi']) && !empty($_POST['linkaffiliasi'])) {  				
  				update_option('widget_linkaffiliasi', $_POST['linkaffiliasi']);
  				echo '<div class="notice notice-success is-dismissible">
					<p>Pengaturan telah disimpan</p>
					</div>';
  			}

  			$linklist = get_option('widget_linkaffiliasi');
  			$title = 'Edit Link Affiliasi';
  			$showtxt = '
  			<form action="" method="post">
  				<div class="mb-3 row">
				    <label for="linkaffiliasi" class="col-sm-2 col-form-label">URL Page/Post</label>
				    <div class="col-sm-10">
				      <textarea class="form-control" name="linkaffiliasi" style="height:200px;" id="linkaffiliasi">'.($linklist??='').'</textarea>
				      <br/><small>Masukkan URL yang ingin dibuatkan link affiliasi. Per baris 1 URL.</small>
				    </div>
				  </div>
				  <input type="submit" value="Simpan" class="button button-primary"/>
  			</form>';
  		break;
  		default:
  			// code...
  			break;
  	}
  	echo '<div class="wrap">
		<h1 class="wp-heading-inline">'.$title.'</h1>
		<hr class="wp-header-end">'.$showtxt.'</div>';
  }
}

?>