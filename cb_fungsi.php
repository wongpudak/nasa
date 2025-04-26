<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
ob_start();
include('fileku.php');
add_action('init', 'include_affiliasi_on_frontend');

function include_affiliasi_on_frontend() {
    // Cek apakah request adalah untuk frontend
    if (!is_admin() && !wp_doing_ajax() && !wp_doing_cron() && !defined('XMLRPC_REQUEST')) {
        // Include file affiliasi.php
        include('affiliasi.php'); // Sesuaikan path file
    }
}

include('include/menuadmin.php');
include('include/headfoot.php');
include('shortcode.php');

function wp_affiliasi() {
}

function wpaff_load() {
	global $wpdb, $user_ID;
	$versi = get_option('wp_affiliasi_version');
	if (is_numeric($versi) && $versi < 340) {
		include_once('cb_update340.php');
	} 

	if (is_numeric($versi) && $versi < 347) {
		# Update Database
		$wpdb->query("ALTER TABLE `cb_produk` ADD `status` VARCHAR(1) NOT NULL DEFAULT '1' AFTER `harga`");
		update_option('wp_affiliasi_version',347);
	}

	if (is_numeric($versi) && $versi < 359) {
		$wpdb->query("UPDATE `cb_laporan` SET `kredit` = `debet` * -1, `debet`=0 WHERE `keterangan` = 'refund' AND `debet` > 0");
		update_option('wp_affiliasi_version',359);
	}

	# Load data-data member
	if (get_current_user_id() > 0) {
		$datamember = $wpdb->get_row("SELECT `wp_member`.*, 
			COALESCE(SUM(`kredit`), 0) AS `omset`, 
			COALESCE(COUNT(`cb_laporan`.`id`), 0) AS `totalorder`,
			COALESCE(SUM(`debet`), 0) AS `komisicair`,
			COALESCE(SUM(`komisi`), 0) AS `totalkomisi`
			FROM `wp_member`
			LEFT JOIN `cb_laporan` ON `wp_member`.`idwp`=`cb_laporan`.`id_sponsor` 
			WHERE `idwp`=".$user_ID." AND (`cb_laporan`.`keterangan` IN ('cbaff','ppl','woo','wd','refund') OR `cb_laporan`.`keterangan` IS NULL)
			GROUP BY `wp_member`.`idwp`");
		if (isset($datamember->id_user)) {
			$datadownline = $wpdb->get_results("SELECT `membership`, COUNT(*) AS `jmldownline` FROM `wp_member` 
				WHERE `id_referral`=".$user_ID." GROUP BY `membership`");
			if (count($datadownline) > 0) {
				foreach($datadownline as $datadownline) {
					$cbdownline[$datadownline->membership] = $datadownline->jmldownline;
				}
			}
			$datamember->jmlinvalid = $cbdownline[0] ??= 0;
			$datamember->jmlfree = $cbdownline[1] ??= 0;
			$datamember->jmlpremium = $cbdownline[2] ??= 0;
			$datamember->jmldownline = $datamember->jmlinvalid + $datamember->jmlfree + $datamember->jmlpremium;
			$datamember->omset = number_format($datamember->omset);
			$datamember->totalorder = number_format($datamember->totalorder);
			$datamember->komisitertahan = number_format($datamember->totalkomisi - $datamember->komisicair);
			$datamember->totalkomisi = number_format($datamember->totalkomisi);
			$datamember->komisicair = number_format($datamember->komisicair);
			$datamember->jml_voucher = $datamember->totalkomisi;
			$datamember->sisa_voucher = $datamember->komisitertahan;

			$sesmember = serialize($datamember);
			if (!defined('CB_MEMBER')) { 
				define('CB_MEMBER', $sesmember);
			}

			if (isset($datamember->id_referral) && $datamember->id_referral > 0) {
				$datamysponsor = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`=".$datamember->id_referral);
				if (isset($datamysponsor->id_user)) {
				$datamysponsor->jmlinvalid = '';
				$datamysponsor->jmlfree = '';
				$datamysponsor->jmlpremium = '';
				$datamysponsor->jmldownline = '';
				$datamysponsor = serialize($datamysponsor);
				if (!defined('CB_MYSPONSOR')) { 
					define('CB_MYSPONSOR', $datamysponsor);
				}
				}
			}
		}
	}
}
add_action( 'plugins_loaded', 'wpaff_load' );

	
function cb_install() {
	global $wpdb, $user_ID;
	include ('cb_install.php');
}

function get_urlpendek($url) {
	$options = get_option('cb_pengaturan');
	if (isset($options['shorturl'])) {
		$url = str_replace('[URL]',$url,$options['shorturl']);
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,$url);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$url = curl_exec($ch);  
		curl_close($ch);
		$url = trim($url);
		return $url;
	}
}

function urlpendek($url) {
	echo '<a href="'.get_urlpendek($url).'">'.get_urlpendek($url).'</a>';
}

function getData($url, $agent){
	$curl = curl_init();
	if (!$curl) {
	    // Gagal membuat koneksi cURL
	    return false;
	}
	
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $agent);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($curl, CURLOPT_ENCODING, "");
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_COOKIEFILE, getcwd() . '/mdr.cok');
	curl_setopt($curl, CURLOPT_COOKIEJAR, getcwd() . '/mdr.cok');

	$data = curl_exec($curl);
	if ($data === false) {
	    // Gagal mengambil data
	    $error = curl_error($curl);
	    // Tambahkan penanganan kesalahan sesuai kebutuhan Anda
	    curl_close($curl);
	    return false;
	} else {
		return $data;
	}

	curl_close($curl);
	
}

function postData($url, $agent, $post, $ref = ''){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,$url);
	curl_setopt($curl, CURLOPT_USERAGENT, $agent);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
	curl_setopt($curl, CURLOPT_REFERER, $ref);
	curl_setopt($curl, CURLOPT_ENCODING, "");
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER ,1);
	curl_setopt($curl, CURLOPT_COOKIEFILE, getcwd() . '/mdr.cok');
	curl_setopt($curl, CURLOPT_COOKIEJAR, getcwd() . '/mdr.cok');
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

	$data = curl_exec($curl);
	curl_close ($curl);
	return $data;
}

function GetBetween($content,$start,$end){
    $r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}

function getBCA() {
	global $options;
	$username = $options['bca']['uname'];
	$password = $options['bca']['paswd'];
	
	$url = getData('https://ibank.klikbca.com/',$_SERVER['HTTP_USER_AGENT']);
	$curnum = GetBetween($url,'name="value(CurNum)" value="','"');
	$url= "https://ibank.klikbca.com/authentication.do";
	$post = 'value(actions)=login&value(user_id)='.$username.'&value(pswd)='.$password.'&value(user_ip)='.$_SERVER['REMOTE_ADDR'].'&value(Submit)=LOGIN&value(CurNum)='.$curnum;
	$data = postData($url, $_SERVER['HTTP_USER_AGENT'], $post);

	$ref= 'https://ibank.klikbca.com/accountstmt.do?value(actions)=acct_stmt';
	$url= 'https://ibank.klikbca.com/accountstmt.do?value(actions)=acctstmtview';
	$now = strtotime('Today');
	
	list($day1, $month1, $year1) = explode(' ', date('d n Y', $now));
	$minus_t = $now - (24 * 3600 * 3);
	list($day2, $month2, $year2) = explode(' ', date('d n Y', $minus_t));

	$post = 'value(r1)=1&value(D1)=0&value(startDt)='.$day2.'&value(startMt)='.$month2.'&value(startYr)='.$year2.'&value(endDt)='.$day1.'&value(endMt)='.$month1.'&value(endYr)='.$year1.'&value(submit1)=Lihat Mutasi Rekening';
	$data = postData($url, $_SERVER['HTTP_USER_AGENT'], $post, $ref);
	if (stristr($data,'TRANSAKSI ANDA GAGAL')) {
	$return[0][ket] = 'TRANSAKSI ANDA GAGAL';
	} else {
		$start = '<td colspan="2">';
		$end = '</table>';
		$data = GetBetween($data,$start,$end).'</table>';
		$items = explode('</tr>',$data);
		$i=0;
		foreach ($items as $item) {
			$exitem = explode('</td>',$item);
			if (isset($exitem[0])) { $result[$i]['tgl'] = trim(GetBetween($exitem[0],'<font face="verdana" size="1" color="#0000bb">','</font>')); }
			if (isset($exitem[1])) { $result[$i]['ket'] = trim(GetBetween($exitem[1],'<font face="verdana" size="1" color="#0000bb">','</font>')); }
			if (isset($exitem[2])) { $result[$i]['cab'] = trim(GetBetween($exitem[2],'<font face="verdana" size="1" color="#0000bb">','</font>')); }
			if (isset($exitem[3])) { $result[$i]['mutasi'] = trim(GetBetween($exitem[3],'<font face="verdana" size="1" color="#0000bb">','</font>')); }
			if (isset($exitem[4])) { $result[$i]['trx'] = trim(GetBetween($exitem[4],'<font face="verdana" size="1" color="#0000bb">','</font>')); }
			if (isset($exitem[5])) { $result[$i]['saldo'] = trim(GetBetween($exitem[5],'<font face="verdana" size="1" color="#0000bb">','</font>')); }
			$i++;
		}
	}
	getData('https://ibank.klikbca.com/authentication.do?value(actions)=logout', $_SERVER['HTTP_USER_AGENT']);

	return $result;
}

function special($p) {
		$p = htmlentities($p, ENT_COMPAT,'UTF-8');
		$p = str_replace('--','-&minus;',$p);
		return $p;
	}

function txtonly($p) {
	$p = preg_replace("/[^a-zA-Z0-9]+/", "", $p);
	return $p;
}	
function realIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $ip=$_SERVER['HTTP_CLIENT_IP']; 
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else { $ip=$_SERVER['REMOTE_ADDR']; }
    return $ip;
}

add_filter('authenticate', 'check_login', 10, 3);
function check_login($user, $username, $password) {
	global $wpdb;
	if (isset($username) && $username != '') {
		#Cek dulu ini pakai email atau username
		if (is_email($username)) {
			$field = "`email`";
			if (!email_exists($username)) {
				$new = 1;
			}
		} else {
			$field = "`username`";
			if (!username_exists($username)) {
				$new = 1;
			}
		}

		if (isset($new) && $new == 1) {
			$datamember = $wpdb->get_row("SELECT * FROM `wp_member` WHERE ".$field." = '".$username."' AND `password`='".$password."'",ARRAY_A);
			if (isset($datamember['id_user']) && $datamember['id_user'] > 0) {
				// Buat akun di WordPress
				$idwp = wp_create_user($datamember['username'], $datamember['password'], $datamember['email']);
				$passdb = md5($datamember['password']);
				$wpdb->query("UPDATE `wp_member` SET `membership` = 1, `password`='".$passdb."', `idwp`='".$idwp."', `tgl_daftar` = '".wp_date('Y-m-d H:i:s')."' WHERE `username` = '".$datamember['username']."'");
				
				$komisi = get_option('komisi');
				$options = get_option('cb_pengaturan');
				$freePPL = $komisi['pplfree'];
				$premiumPPL = $komisi['pplpremium'];
				
				if ($freePPL > 0 || $premiumPPL > 0) {
					$ip = realIP();
					$checkip = $wpdb->get_var("SELECT COUNT(*) FROM `wp_member` WHERE `ip`='".$ip."'");
					if ($checkip >= 2) {
						$freePPL = 0;
						$premiumPPL = 0;
						}			
					
					// Ambil info status keanggotaan sponsor
					$status = $wpdb->get_var("SELECT `membership` FROM `wp_member` WHERE `idwp` = ".$datamember['id_referral']);		
					if ($status == 1) { 
						$voucher = $freePPL; 
					} elseif ($status >=2) { 
						$voucher = $premiumPPL; 
					} else { 
						$voucher = 0;
					}
					
					// Tambah Komisi Sponsor
					if (isset($voucher) && $voucher > 0) {
						$wpdb->query("UPDATE `wp_member` SET `sisa_voucher` = `sisa_voucher`+".$voucher.", `jml_voucher`=`jml_voucher`+".$voucher."  WHERE `idwp` = ".$datamember['id_referral']);
						$wpdb->query("INSERT INTO `cb_laporan` 
							(`tanggal`,`transaksi`,`debet`,`kredit`,`komisi`,`keterangan`,`id_user`,`id_sponsor`,`id_order`) 
							VALUES ('".wp_date('Y-m-d H:i:s')."', 'Komisi PPL oleh ".$datamember['nama']."',0,0,".$voucher.",'ppl',".$idwp.",".$datamember['id_referral'].",0)");
					}
				}
			}	
		} else {
			#cek apakah sudah punya data wp-affiliasi
			$datamember = $wpdb->get_row("SELECT * FROM `wp_member` WHERE ".$field." = '".$username."'",ARRAY_A);
			if (!isset($datamember['id_user'])) {
				$current_user = wp_get_current_user();
				$wpdb->query("INSERT INTO `wp_member` (`idwp`,`id_referral`,`nama`,`tgl_daftar`,`username`,`password`,`email`,`subdomain`,`membership`) 
					VALUES (".$current_user->ID.", 1, '".$current_user->display_name."', '".wp_date('Y-m-d H:i:s')."', '".$current_user->user_login."', '', '".$current_user->user_email."','".$current_user->user_login."', 1)");
			}
		}

		return $user;
	}
}

function cb_logout() {
	setcookie("datamember",'',strtotime('+30 days'),'/');
}

add_action('wp_logout', 'cb_logout');

function cb_stats_widget($args) {
	extract($args);
	$data = get_option('wp_affiliasi_widget');
	echo $before_widget;
	echo $before_title.$data['judulstats'].$after_title;
	echo '<ul>';
	if ($data['stats'][0] == 'free') { echo '<li>Free Member: '.get_jml_member($data['stats'][0]).'</li>'; }
	if ($data['stats'][1] == 'premium') { echo '<li>Premium Member: '.get_jml_member($data['stats'][1]).'</li>'; }
	if ($data['stats'][2] == 'total') { echo '<li>Total Member: '.get_jml_member($data['stats'][2]).'</li>'; }
	echo '</ul>';
	echo $after_widget;
}

function cb_stats_control() {
	$data = get_option('wp_affiliasi_widget');
	?>
	<p><label>Title:</label><br/>
	<input name="judulstats" type="text" value="<?php if (isset($data['judulstats'])) { echo $data['judulstats']; } ?>" size="30"/></p>
	<p><label>Tampilkan Statistik:</label><br/>
	<input name="stats[0]" type="checkbox" value="free" <?php if (isset($data['stats'][0]) && $data['stats'][0] == 'free') { echo 'checked="yes"'; }?>/> Free Member<br/>
	<input name="stats[1]" type="checkbox" value="premium" <?php if (isset($data['stats'][1]) && $data['stats'][1] == 'premium') { echo 'checked="yes"'; }?>/> Premium Member<br/>
	<input name="stats[2]" type="checkbox" value="total" <?php if (isset($data['stats'][2]) && $data['stats'][2] == 'total') { echo 'checked="yes"'; }?>/> Total Jumlah Member <br/>
	</p>
	<?php
	if (isset($_POST['judulstats'])){
	    $data['judulstats'] = attribute_escape($_POST['judulstats']);
	    $data['stats'] = ($_POST['stats']);
	    update_option('wp_affiliasi_widget', $data);
	}
}

function cb_list_widget($args) {
	extract($args);
	$data = get_option('wp_affiliasi_widget');
	if (isset($data['marqueecheck']) && $data['marqueecheck'] == 1) {
		$marque = ' class="marquee ver" data-direction="up" data-duration="4000" data-pauseOnHover="true"';
	} else {
		$marque = '';
	}
	if (isset($data['premiumcheck']) && $data['premiumcheck'] == 1) {
		echo $before_widget;
		echo $before_title.$data['judulpremium'].$after_title;
		echo '<div'.$marque.'>';
		echo '<ul>';
		if (isset($data['jmlpremium']) && $data['jmlpremium'] > 0) { $number = $data['jmlpremium']; } else { $number = 5; }
		cb_list(2,'<li>nama</li>',$number);
		echo '</ul>';
		echo '</div>';
		echo $after_widget;
	}
	if (isset($data['freecheck']) && $data['freecheck'] == 1) {
		echo $before_widget;
		echo $before_title.$data['judulfree'].$after_title;
		echo '<div'.$marque.'>';
		echo '<ul>';
		if (isset($data['jmlfree']) && $data['jmlfree'] > 0) { $number = $data['jmlfree']; } else { $number = 5; }
		cb_list(1,'<li>nama</li>',$data['jmlfree']);
		echo '</ul>';
		echo '</div>';
		echo $after_widget;
	}
	if (isset($data['allcheck']) && $data['allcheck'] == 1) {
		echo $before_widget;
		echo $before_title.$data['judulall'].$after_title;
		echo '<div'.$marque.'>';
		echo '<ul>';
		if (isset($data['jmlall']) && $data['jmlall'] > 0) { $number = $data['jmlall']; } else { $number = 5; }
		cb_list('all','<li>nama</li>',$data['jmlall']);
		echo '</ul>';
		echo '</div>';
		echo $after_widget;
	}
	
}

function cb_list_control() {
	$data = get_option('wp_affiliasi_widget');
	echo '
	<p><input type="checkbox" value="1" name="marqueecheck"';
	if (isset($data['marqueecheck']) && $data['marqueecheck'] == 1) { echo 'checked="yes"'; }
	echo '/> Marquee</p>
	<p>List Member Premium<br/>
	<input type="checkbox" value="1" name="premiumcheck"';
	if (isset($data['premiumcheck']) && $data['premiumcheck'] == 1) { echo 'checked="yes"'; }
	echo '/>
	<input type="text" name="judulpremium" placeholder="Judul List" value="'.$data['judulpremium'].'" size="22"/>
	<input type="number" name="jmlpremium" value="'.$data['jmlpremium'].'" style="width:60px"/></p>';
	echo '<p>List Member Free<br/>
	<input type="checkbox" value="1" name="freecheck"';
	if (isset($data['freecheck']) && $data['freecheck'] == 1) { echo 'checked="yes"'; }
	echo '/>
	<input type="text" name="judulfree" placeholder="Judul List" value="'.$data['judulfree'].'" size="22"/>
	<input type="number" name="jmlfree" value="'.$data['jmlfree'].'" style="width:60px"/></p>';
	echo '<p>List Semua Member<br/>
	<input type="checkbox" value="1" name="allcheck"';
	if (isset($data['allcheck']) && $data['allcheck'] == 1) { echo 'checked="yes"'; }
	echo '/>
	<input type="text" name="judulall" placeholder="Judul List" value="'.$data['judulall'].'" size="22"/>
	<input type="number" name="jmlall" value="'.$data['jmlall'].'" style="width:60px"/></p>';
	
	if (isset($_POST['judulpremium'])){
	    $data['premiumcheck'] = attribute_escape($_POST['premiumcheck']);
		$data['freecheck'] = attribute_escape($_POST['freecheck']);
		$data['allcheck'] = attribute_escape($_POST['allcheck']);
		$data['judulpremium'] = attribute_escape($_POST['judulpremium']);
		$data['judulfree'] = attribute_escape($_POST['judulfree']);
		$data['judulall'] = attribute_escape($_POST['judulall']);
		$data['jmlpremium'] = attribute_escape($_POST['jmlpremium']);
		$data['jmlfree'] = attribute_escape($_POST['jmlfree']);
		$data['jmlall'] = attribute_escape($_POST['jmlall']);
		$data['marqueecheck'] = attribute_escape($_POST['marqueecheck']);
	    update_option('wp_affiliasi_widget', $data);
	}
	
}

function cbcek() {
	$options = get_option('cb_pengaturan');
	$post = array(
		'url'=> site_url(),
		'c'=> $options['lisensi']
	);
	$url = 'https://'.'lisensi.'.'cafe'.'bisnis.com/newcek.php';
	$cbcek = postData($url,$_SERVER['HTTP_USER_AGENT'],$post);
	//$cbcek = getData($url,$_SERVER['HTTP_USER_AGENT']);
	if ($cbcek == 'error') {
		unset($options['lisensi']);
		update_option('cb_pengaturan', $options);
	} 
}

function cb_aff_init() {
	$ops_stats = array('classname' => 'wp_aff_stats', 'description' => "Menampilkan statistik jumlah member", 'number' => 5 );
	wp_register_sidebar_widget('wp_aff','Statistik Member', 'cb_stats_widget',$ops_stats); 
	wp_register_widget_control('wp_aff','Statistik Member', 'cb_stats_control');
	wp_register_sidebar_widget('cb_list','List Member Terbaru', 'cb_list_widget');
	wp_register_widget_control('cb_list','List Member Terbaru', 'cb_list_control');
}
add_action("plugins_loaded", "cb_aff_init");

function get_cb_datasponsor($field) {
	global $id_sponsor, $wpdb;
	if (isset($_COOKIE['sponsor']) && !$_GET['reg']) {
		$id_sponsor = $_COOKIE['sponsor'];
	} 
	
	$cb_tampil = $wpdb->get_var("SELECT `$field` FROM `wp_member` WHERE `idwp`='$id_sponsor'");
	return $cb_tampil;
}

function cb_datasponsor($tampillist) {
	$datasponsor = unserialize(CB_SPONSOR);
	$datalain = unserialize($datasponsor['homepage']);
	$tampillist = str_replace('idmlm',$datasponsor['id_tianshi'],$tampillist);
	$tampillist = str_replace('nama',$datasponsor['nama'],$tampillist);
	$tampillist = str_replace('alamat',$datasponsor['alamat'],$tampillist);
	$tampillist = str_replace('kota',$datasponsor['kota'],$tampillist);
	$tampillist = str_replace('provinsi',$datasponsor['provinsi'],$tampillist);
	$tampillist = str_replace('kodepos',$datasponsor['kodepos'],$tampillist);
	$tampillist = str_replace('telp',$datasponsor['telp'],$tampillist);
	$tampillist = str_replace('ac',$datasponsor['ac'],$tampillist);
	$tampillist = str_replace('bank',$datasponsor['bank'],$tampillist);
	$tampillist = str_replace('rekening',$datasponsor['rekening'],$tampillist);
	$tampillist = str_replace('email',$datasponsor['email'],$tampillist);
	$urlaffiliasi = urlaff($datasponsor['subdomain']);
	$tampillist = str_replace('urlaffiliasi',urlaff($urlaffiliasi),$tampillist);
	if ($datasponsor['homepage'] == '') {
		$avatar = '<img src="http://www.gravatar.com/avatar/'.md5(strtolower($datasponsor['email'])).'/?r=R&s=200" alt="'.$datasponsor['nama'].'" style="width:175px; height:175px; float:left; margin-right:5px;">';
	} else {
		$avatar = '<img src="'.$datalain['pic_profil'].'" alt="'.$datasponsor['nama'].'" style="width:175px; height:175px; float:left; margin-right:5px;">';
	}

	$tampillist = str_replace('avatar',$avatar,$tampillist);
	echo $tampillist;	
}

function cb_list($status=2,$format='<li>nama (kota)</li>',$number=5) {
	global $wpdb;
	if ($number == '') { $number = 5; }
	if ($status == 'all') {
		$listprem = $wpdb->get_results("SELECT * FROM `wp_member` ORDER BY `tgl_daftar` DESC LIMIT 0,".$number);
	} else {
		$listprem = $wpdb->get_results("SELECT * FROM `wp_member` WHERE `membership` = ".$status." ORDER BY `tgl_daftar` DESC LIMIT 0,".$number);
	}
	foreach ($listprem as $listprem) {
		$tampillist = str_replace('nama',$listprem->nama,$format);
		$tampillist = str_replace('alamat',$listprem->alamat,$tampillist);
		$tampillist = str_replace('kota',$listprem->kota,$tampillist);
		$tampillist = str_replace('provinsi',$listprem->provinsi,$tampillist);
		$tampillist = str_replace('kodepos',$listprem->kodepos,$tampillist);
		$tampillist = str_replace('telp',$listprem->telp,$tampillist);
		$tampillist = str_replace('ac',$listprem->ac,$tampillist);
		$tampillist = str_replace('bank',$listprem->bank,$tampillist);
		$tampillist = str_replace('rekening',$listprem->rekening,$tampillist);
		$tampillist = str_replace('email',$listprem->email,$tampillist);
		$urlaffiliasi = urlaff($listprem->subdomain);
		$tampillist = str_replace('urlaffiliasi',$urlaffiliasi,$tampillist);		
		echo $tampillist;
	}
}

function get_jml_member($status) {
	global $wpdb;
	if ($status == 'free') {
		$membership = 1;
	} elseif ($status == 'premium') {
		$membership = 2;
	} else {
		$membership = 1;
	}
	if ($status == 'total') {
	$cb_jml_member = $wpdb->get_var("SELECT COUNT(*) FROM `wp_member` ORDER BY `tgl_daftar` DESC");
	return $cb_jml_member;
	} else {
	$cb_jml_member = $wpdb->get_var("SELECT COUNT(*) FROM `wp_member` WHERE `membership`=$membership ORDER BY `tgl_daftar` DESC");
	return $cb_jml_member;
	}
}

function cb_jml_member($status) {
	echo get_jml_member($status);
}

function aktivasi($id2up,$thebank="") {
	global $wpdb, $user_ID, $user_identity, $sponsorkita;
	global $subdomain, $nama, $username, $password, $urlreseller;
	global $telp, $kota, $provinsi, $bank, $rekening, $ac, $komisi;
	global $namaprospek, $usernameprospek, $status, $blogurl;	
	include('aktivasi.php');
}

class classsponsor extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'classsponsor',
			'description' => 'Menampilkan Data Sponsor',
		);
		parent::__construct( 'classsponsor', 'Data Sponsor', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$datasponsor = unserialize(CB_SPONSOR);
		extract($args, EXTR_SKIP);
		/*
		if (isset($_COOKIE['sponsor']) && !isset($_GET['reg'])) {
			$datasponsor = unserialize(stripslashes($_COOKIE['sponsor']));
		} else {			
			$datasponsor = unserialize(stripslashes($sponsor));
		}
		*/

		
		if (is_array($datasponsor) && is_array($instance['widgetsponsor'])) {
		$custom = unserialize($datasponsor['homepage']);	
		if (isset($custom[0])) {
			$ym = $custom[0];
		}
		echo $before_widget;
		echo $before_title.$instance['title'].$after_title;
		
		foreach ($instance['widgetsponsor'] as $widget) {
			if (substr($widget,0,6) == 'custom') {
				$cus = str_replace('custom[','',$widget);
				$cus = str_replace(']','',$cus);
				if (isset($custom['whatsapp']) && $cus == 'customwhatsapp') {
					echo '<a href="https://wa.me/'.formatwa($custom['whatsapp']).'">Chat via WhatsApp</a><br/>';
				} elseif (isset($custom[$cus])) {
					echo $custom[$cus].'<br/>';
				}

			} elseif ($widget == 'ym') {
				if (isset($ym)) {
				echo '<a href="ymsgr:sendIM?'.$ym.'"><img border="0" src="http://opi.yahoo.com/online?u='.$ym.'&m=g&t=2&l=us" width="125" height="25" alt="Chat dengan Sponsor" /></a><br/>';
				}
			} elseif ($widget == 'avatar') {
				if ($custom['pic_profil'] == '') {
					echo '<img src="http://www.gravatar.com/avatar/'.md5(strtolower($datasponsor['email'])).'/?r=R&s=100" alt="'.$datasponsor['nama'].'" style="width:175px; height:175px; margin-bottom:10px"><br/>';
				} else {
					echo '<img src="'.$custom['pic_profil'].'" alt="'.$datasponsor['nama'].'" style="width:175px; height:175px; margin-bottom:10px"><br/>';
				}
			} elseif ($widget == 'subdomain') {
				if (get_option('affsub') == 1) {
					echo '<a href="http://'.$datasponsor['subdomain'].'.'.get_option('domain').'">http://'.$datasponsor['subdomain'].'.'.get_option('domain').'</a><br/>';
				} else {
					echo '<a href="'.site_url().'/?reg='.$datasponsor['subdomain'].'">'.site_url().'/?reg='.$datasponsor['subdomain'].'</a><br/>';
				}
			} elseif ($widget == 'password') {
				//diem aja
			} else {
				echo '<span class="sponsor'.$widget.'">'.$datasponsor[$widget].'</span><br/>';
			}
		}
		echo $after_widget;
		} else {
			echo 'DATA SPONSOR BLM ADA';
		}
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		//$data = get_option('wp_affiliasi_widget');
		$default = 	array( 'title' => __('Data Sponsor') );
		$instance = wp_parse_args( (array) $instance, $default );
		$w = 1;
		$c = 1;
		echo '<p><label>Title:</label><br/>
		<input name="'.$this->get_field_name('title').'" type="text" value="'. esc_attr( $instance['title'] ).'" size="30"/></p>
		<p><label>Data yang ditampilkan</label><br/>
		<input name="'.$this->get_field_name('widgetsponsor').'[0]" type="checkbox" value="avatar"'; 
			if (isset($instance['widgetsponsor'][0])) { echo 'checked="checked"'; }
			echo '/> Gravatar<br/>';
		$aturform = get_option('aturform');
		$form = unserialize($aturform);
		if (is_array($form)) {
			foreach ($form as $form) {
			
			if ($form['profil'] == 1) {
				if (empty($form['label'])) {
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
						case 'subdomain' : $label = 'URL Affiliasi'; break;
						case 'ym' : $label = 'Yahoo Messenger'; break;						
					}
				} else {
					$label = $form['label'];
				}
				if ($form['field'] != 'keterangan' && $form['field'] != 'password') {
					if ($form['field'] == 'custom') {
						echo '<input name="'.$this->get_field_name('widgetsponsor').'['.$w.']" type="checkbox" value="custom['.$c.']"'; 
						if (isset($instance['widgetsponsor'][$w])) { echo 'checked="checked"'; }
						echo '/> '.$label.'<br/>';
						$c++;
					} else {
						echo '<input name="'.$this->get_field_name('widgetsponsor').'['.$w.']" type="checkbox" value="'.$form['field'].'"'; 
						if (isset($instance['widgetsponsor'][$w])) { echo 'checked="checked"'; }
						echo '/> '.$label.'<br/>';
					}
					$w++;
				}
			}
			}
		}
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['widgetsponsor'] = $new_instance['widgetsponsor'];
		return $instance;
	}
}

/* register widget when loading the WP core */
add_action('widgets_init', 'widget_sponsor');

function widget_sponsor(){
	register_widget('classsponsor');
}

add_action('woocommerce_new_order', 'cbaff_addsponsor', 10, 1);

function cbaff_addsponsor($order_id) {
    global $wpdb;
    $user_ID = get_current_user_id(); // Ambil ID pengguna yang sedang login

    // Ambil data sponsor berdasarkan user ID
    if ($user_ID > 0) {
        $datasponsor = $wpdb->get_var($wpdb->prepare("SELECT `id_referral` FROM `wp_member` WHERE `idwp` = %d", $user_ID));
        if ($wpdb->last_error) {
            error_log('Database error: ' . $wpdb->last_error);
        }
    } else {
        // Jika pengguna tidak login, gunakan data dari konstanta CB_SPONSOR
        if (defined('CB_SPONSOR')) {
            $sponsor = unserialize(CB_SPONSOR);
            if (is_array($sponsor) && isset($sponsor['idwp'])) {
                $datasponsor = $sponsor['idwp'];
            } else {
                error_log('Data CB_SPONSOR tidak valid atau tidak ditemukan.');
            }
        } else {
            error_log('Konstanta CB_SPONSOR tidak terdefinisi.');
        }
    }

    // Jika data sponsor valid, tambahkan ke meta order menggunakan CRUD
    if (isset($datasponsor) && is_numeric($datasponsor)) {
        $order = wc_get_order($order_id); // Dapatkan objek order
        if ($order) {
            $order->update_meta_data('Sponsor ID', $datasponsor); // Tambahkan meta data
            $order->save(); // Simpan perubahan
        } else {
            error_log('Gagal mendapatkan objek order untuk order #' . $order_id);
        }
    } else {
        error_log('Data sponsor tidak valid atau tidak ditemukan untuk order #' . $order_id);
    }
}

function custom_action_on_order_completion( $order_id ) {
    // Dapatkan objek order
    global $wpdb;
    $user_ID = get_current_user_id();
    include('wooproses.php');
}
add_action( 'woocommerce_order_status_completed', 'custom_action_on_order_completion' );

add_action('user_register','cbaff_adduser');

function cbaff_adduser($user_id) {
	global $wpdb, $sponsor;
	$userdata = get_userdata( $user_id );
	$username = $userdata->user_login;
	$cekmember = $wpdb->get_var("SELECT `id_user` FROM `wp_member` WHERE `username` = '".$username."'");

	if ($cekmember == NULL && !isset($_POST['nama'])) {
		$id_referral = $_COOKIE['idsponsor'];
		$lainlain['uplines'] = cbaff_uplines($id_referral);
		if (isset($lainlain)) {
			$homepage = serialize($lainlain);
		}
		$nama = $alamat = $kota = $provinsi = $telp = '';
		if (isset($_POST['billing_first_name'])) { $nama = sanitize_text_field($_POST['billing_first_name']); }
		if (isset($_POST['billing_last_name'])) { $nama .= ' '.sanitize_text_field($_POST['billing_last_name']); }
		if (isset($_POST['billing_address_1'])) { $alamat = sanitize_text_field($_POST['billing_address_1']); }
		if (isset($_POST['billing_address_2'])) { $alamat .= ' '.sanitize_text_field($_POST['billing_address_2']); }
		if (isset($_POST['billing_city'])) { $kota = sanitize_text_field($_POST['billing_city']); }
		if (isset($_POST['billing_state'])) { $provinsi = sanitize_text_field($_POST['billing_state']); }
		if (isset($_POST['billing_phone'])) { $telp = sanitize_text_field($_POST['billing_phone']); }
		$ip = $_SERVER['REMOTE_ADDR'];
		$email = $userdata->user_email;
		if ($nama == '') {
			if (isset($userdata->first_name) && $userdata->first_name != '') {
				$nama = $userdata->first_name.' '.$userdata->last_name;
			} else {
				$nama = $username;
			}
		}

		$wpdb->query("INSERT INTO `wp_member` 
			   (`idwp`,`id_referral`,`nama`,`alamat`,`kota`,`provinsi`,`telp`,`tgl_daftar`,`username`,`email`,`subdomain`,`homepage`,`membership`,`ip`) 
				VALUES (".$user_id.",".$id_referral.",'".$nama."','".$alamat."','".$kota."','".$provinsi."','".$telp."','".wp_date('Y-m-d H:i:s')."','".$username."','".$email."','".txtonly($username)."','".$homepage."',1,'".$ip."')");
	}
}

function cbaff_uplines($id) {
	global $wpdb;	
	$idsponsor = $wpdb->get_var("SELECT `id_referral` FROM `wp_member` WHERE `idwp`=".$id);
	$uplines = $id.','.$idsponsor;
	while ($idsponsor != 0) {
		$getidsponsor = $wpdb->get_var("SELECT `id_referral` FROM `wp_member` WHERE `idwp`=".$idsponsor);
		if ($getidsponsor == 0 || $getidsponsor == $idsponsor ) {
			break;
		} else {
			$uplines = $uplines.','.$getidsponsor;
			$idsponsor = $getidsponsor;
		}
	}
	return $uplines;
}

function urlaff($subdomain) {
	$blogurl = cbdomain();
	if (get_option('affsub') == 1) {
		$result = 'http://'.$subdomain.'.'.$blogurl;
	} else {
		$result = site_url().'/?reg='.$subdomain;
	}

	return $result;
}

function cbdomain() {
	$blogurl = str_replace('https://', '', get_bloginfo('wpurl'));
	$blogurl = str_replace('http://', '', $blogurl);
	if (substr($blogurl,0,4) == 'www.') {
		$blogurl = substr($blogurl, 4);
	}
	return $blogurl;
}

function cb_register_url($link){
    /*
        Change wp registration url
    */
    $options = get_option('cb_pengaturan');
    return str_replace(site_url('wp-login.php?action=register', 'login'),site_url('?page_id='.$options['registrasi'], 'login'),$link);
}
add_filter('register','cb_register_url');

function cb_register_url_fix($url, $path, $orig_scheme){
    /*
        Site URL hack to overwrite register url     
        http://en.bainternet.info/2012/wordpress-easy-login-url-with-no-htaccess
    */
    $options = get_option('cb_pengaturan');
    if ($orig_scheme !== 'login')
        return $url;

    if ($path == 'wp-login.php?action=register')
        return site_url('?page_id='.$options['registrasi'], 'login');

    return $url;
}
add_filter('site_url', 'cb_register_url_fix', 10, 3);

function sendsms($iduser,$pesan,$status) {
	global $wpdb;
	$smsoption = get_option('smsoption');
	$member = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `id_user`=".$iduser);
	$sponsor = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`=".$member->id_referral);
	foreach ($member as $key => $value) {
		if ($key != 'homepage') {
			$pesan = str_replace('[member_'.$key.']', $value, $pesan);
			if (isset($sponsor->$key)) {
				$pesan = str_replace('[sponsor_'.$key.']', $sponsor->$key, $pesan);
			}
		} else {
			$lainlain = unserialize($value);
			foreach ($lainlain as $lainkey => $lainvalue) {
				$pesan = str_replace('[member_'.$lainkey.']', $lainvalue, $pesan);
				if (isset($lainsponsor[$lainkey])) {
					$pesan = str_replace('[sponsor_'.$lainkey.']', $lainsponsor[$lainkey], $pesan);
				}
			}
		}
	}

	// Kirim SMS
	if (isset($status) && $status == 'sponsor') {
		$telp = $sponsor->telp;
	} else {
		$telp = $member->telp;
	}
	$url = $smsoption['sms_urlapi'];
	for ($i=1; $i <= 5; $i++) { 
		$value = str_replace('NOHP',$telp, $smsoption['value'.$i]);
		$value = str_replace('[PESAN]',$pesan,$value);
		$post[$smsoption['field'.$i]] = $value;
	}
	postData($url, $_SERVER['HTTP_USER_AGENT'], $post);
}

function formatdata($id_user,$pesan) {
	global $wpdb;
	if (is_numeric($id_user)) {
		$member = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `id_user`=".$id_user);
		$sponsor = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`=".$member->id_referral);
		$lainsponsor = unserialize($sponsor->homepage);
		foreach ($member as $key => $value) {
			if ($key != 'homepage') {
				if (empty($value)) {
					$value = '';
				}
				$pesan = str_replace('[member_'.$key.']', $value, $pesan);
				if (isset($sponsor->$key)) {
					$pesan = str_replace('[sponsor_'.$key.']', $sponsor->$key, $pesan);
				}	
			} else {
				$lainlain = unserialize($value);
				foreach ($lainlain as $lainkey => $lainvalue) {
					$pesan = str_replace('[member_'.$lainkey.']', $lainvalue, $pesan);
					if (isset($lainsponsor[$lainkey])) {
						$pesan = str_replace('[sponsor_'.$lainkey.']', $lainsponsor[$lainkey], $pesan);
					}
				}
				if (isset($lainlain['whatsapp'])) {
					$pesan = str_replace('[member_whatsapp]', formatwa($lainlain['whatsapp']), $pesan);
				}
				if (isset($lainsponsor['whatsapp'])) {
					$pesan = str_replace('[sponsor_whatsapp]', formatwa($lainsponsor['whatsapp']), $pesan);
				}
			}
		}
		$affmember = urlaff($member->subdomain);
		$affsponsor = urlaff($sponsor->subdomain);
		$pesan = str_replace('[member_urlaff]', $affmember, $pesan);
		$pesan = str_replace('[sponsor_urlaff]', $affsponsor, $pesan);

		return $pesan;
	} else {
		return false;
	}
}

function cb_notif($id_user,$action,$datalain=array()) {
	global $user_ID, $wpdb;
	/*
	Daftar action notif:
	- registrasi
	- upgrade
	- beli
	- prosesbeli
	- komisi
	- woo
	*/
	
	// Ambil Data konten Notif
	$kontenemail = get_option('konfemail');
	$kontensms = get_option('smsoption');
	$member = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `id_user`=".$id_user);
	if (isset($member->id_referral) && $member->id_referral > 0) {
		$sponsor = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`=".$member->id_referral);
		$lainsponsor = unserialize($sponsor->homepage);		
	}

	$pesan = array(
		'kontenemail' => $kontenemail,
		'kontensms' => $kontensms
	);

	$pesan = apply_filters('cb_notif_pesan',$pesan);	// Filter untuk memasukkan pesan lain

	// Jadikan dalam 1 string json
	$pesan = json_encode($pesan);
	// Ubah format
	
	# UPDATE PENENTUAN FIELD CUSTOM HARUS BERDASAR FORM
	$aturform = get_option('aturform');
	$dataform = unserialize($aturform);
	$cc = 0;
	foreach ($dataform as $dataform) {
		if ($dataform['field'] == 'custom') {
			$cc++;
		}
	}

	foreach ($member as $key => $value) {
		if ($key != 'homepage') {
			$pesan = str_replace('[member_'.$key.']', ($value??=''), $pesan);
			if (isset($sponsor->$key)) {
				$pesan = str_replace('[sponsor_'.$key.']', $sponsor->$key, $pesan);
			}	
		} else {
			$lainlain = unserialize($value);
			
			if (isset($lainlain['whatsapp'])) {
				$pesan = str_replace('[member_whatsapp]', formatwa($lainlain['whatsapp']), $pesan);
			}
			if (isset($lainsponsor['whatsapp'])) {
				$pesan = str_replace('[sponsor_whatsapp]', formatwa($lainsponsor['whatsapp']), $pesan);
			}
			if ($cc > 0) {
				for ($c=1; $c <= $cc ; $c++) { 
					if (isset($lainsponsor[$c])) { $pesan = str_replace('[sponsor_'.$c.']', $lainsponsor[$c], $pesan); }
					if (isset($lainlain[$c])) { $pesan = str_replace('[member_'.$c.']', $lainlain[$c], $pesan); }
				}
			}
		}
	}

	$affmember = urlaff($member->subdomain);
	$pesan = str_replace('[member_urlaff]', $affmember, $pesan);
	$pesan = str_replace('[member_urlpendek]',get_urlpendek($affmember),$pesan);
	
	if (isset($sponsor->subdomain)) {
		$affsponsor = urlaff($sponsor->subdomain);
		$pesan = str_replace('[sponsor_urlaff]', $affsponsor, $pesan);
		$pesan = str_replace('[sponsor_urlpendek]',get_urlpendek($affsponsor),$pesan);
	}

	// Proses Data Lain
	if (isset($datalain) && is_array($datalain) && count($datalain) > 0) {
		foreach ($datalain as $key => $value) {
			$pesan = str_replace('['.$key.']',$value,$pesan);
		}
	}
	// Kirimkan
	$pesan = json_decode($pesan,true);
	// Persiapan kirim email
	$i = 0;
	if (isset($pesan['kontenemail']['judul_'.$action.'_member']) && $pesan['kontenemail']['judul_'.$action.'_member'] != '') {
		$send[$i] = array(
			'to' => $member->email,
			'judul' => $pesan['kontenemail']['judul_'.$action.'_member'],
			'isi' => $pesan['kontenemail']['isi_'.$action.'_member']
		);
		$i++;
	}
	

	if (isset($pesan['kontenemail']['judul_'.$action.'_sponsor']) && $pesan['kontenemail']['judul_'.$action.'_sponsor'] != '') {
		$send[$i] = array(
			'to' => $sponsor->email,
			'judul' => $pesan['kontenemail']['judul_'.$action.'_sponsor'],
			'isi' => $pesan['kontenemail']['isi_'.$action.'_sponsor']
		);
		$i++;
	}

	if (isset($pesan['kontenemail']['judul_'.$action.'_admin']) && $pesan['kontenemail']['judul_'.$action.'_admin'] != '') {
		$send[$i] = array(
			'to' => $pesan['kontenemail']['alamat_email'],
			'judul' => $pesan['kontenemail']['judul_'.$action.'_admin'],
			'isi' => $pesan['kontenemail']['isi_'.$action.'_admin']
		);
	}

	// Persiapan kirim SMS

	$i = 0;

	if (isset($pesan['kontensms']['sms_'.$action.'_member']) && $pesan['kontensms']['sms_'.$action.'_member'] != '') {
		$sendsms[$i] = array(
			'to' => $member->telp,
			'pesan' => $pesan['kontensms']['sms_'.$action.'_member']
		);
		$i++;
	}

	if (isset($pesan['kontensms']['sms_'.$action.'_sponsor']) && $pesan['kontensms']['sms_'.$action.'_sponsor'] != '') {
		$sendsms[$i] = array(
			'to' => $sponsor->telp,
			'pesan' => $pesan['kontensms']['sms_'.$action.'_sponsor']
		);
		$i++;
	}

	if (isset($send) && count($send) > 0) {
		$header = 	'From: '.$pesan['kontenemail']['nama_email'].' <'.$pesan['kontenemail']['alamat_email'].'>'."\r\n".
					'Content-Type: text/html; charset=UTF-8';
		foreach ($send as $send) {
			if (function_exists('wp_mail')) {
				$mailkonten = stripslashes($send['isi']);
				$mailkonten = html_entity_decode($mailkonten);
				$mailkonten = str_replace("\r\n", '<br/>', $mailkonten);
				wp_mail($send['to'], $send['judul'], $mailkonten, $header);
			}
		}
	}

	if (isset($sendsms) && count($sendsms) > 0) {
		foreach ($sendsms as $sendsms) {
			/*
			$smsapi = str_replace('[PESAN]', rawurlencode($sendsms['pesan']), $pesan['kontensms']['smsapi']);
			$smsapi = str_replace('[NOHP]', $sendsms['to'], $smsapi);
			$result = getData($smsapi,$_SERVER['HTTP_USER_AGENT']);
			*/
			
			$url = $pesan['kontensms']['sms_urlapi'];
			for ($i=1; $i <= 5; $i++) { 
				$value = str_replace('[NOHP]',$sendsms['to'], $pesan['kontensms']['sms_value'.$i]);
				$value = str_replace('[PESAN]',$sendsms['pesan'],$value);
				$post[$pesan['kontensms']['sms_field'.$i]] = $value;
			}
			postData($url, $_SERVER['HTTP_USER_AGENT'], $post);
		}
	}
	#$header = 'From: '.$pesan['kontenemail']['nama_email'].' <'.$pesan['kontenemail']['alamat_email'].'>';
	
	// Persilahkan action tambahan di sini
	do_action('cb_notif',$id_user,$action,$pesan);
}


$options = get_option('cb_pengaturan');
if (isset($options['loginpage']) && is_numeric($options['loginpage'])) {
	function cb_login_page( $login_url, $redirect, $force_reauth ) {
		$options = get_option('cb_pengaturan');		
	    $login_page = site_url().'/?page_id='.$options['loginpage'];
	    if (isset($redirect) && $redirect != '') {
	    	$login_page .= '&redirect_to='.urlencode($redirect);
	    }
	    return $login_page;
	}
	add_filter( 'login_url', 'cb_login_page', 10, 3 );
}


function cb_filtercontent($content) {
	global $user_ID, $wpdb;
	if (defined('CB_SPONSOR')) {
		$datasponsor = unserialize(CB_SPONSOR);
		$customsponsor = unserialize($datasponsor['homepage']);
	}

	if (defined('CB_MEMBER')) {
		$member = unserialize(CB_MEMBER);
		$custommember = unserialize($member->homepage);
	}

	if (defined('CB_MYSPONSOR')) {
		$mysponsor = unserialize(CB_MYSPONSOR);
		$custommysponsor = unserialize($mysponsor->homepage);
	}
	
	/*
	$aturform = get_option('aturform');
	$dataform = unserialize($aturform);
	$cc = 1;
	foreach ($dataform as $form) {						
		if ($form['field'] == 'customwhatsapp') {
			if (isset($customsponsor['whatsapp'])) {
				$content = str_replace('[sponsor_whatsapp]', formatwa($customsponsor['whatsapp']), $content);
				$content = str_replace('8888888888', formatwa($customsponsor['whatsapp']), $content);
			}
			if (isset($custommember['whatsapp'])) {
				$content = str_replace('[member_whatsapp]', formatwa($custommember['whatsapp']), $content);
				$content = str_replace('9999999999', formatwa($custommember['whatsapp']), $content);
			}
			if (isset($custommysponsor['whatsapp'])) {
				$content = str_replace('[mysponsor_whatsapp]', formatwa($custommysponsor['whatsapp']), $content);
				$content = str_replace('7777777777', formatwa($custommysponsor['whatsapp']), $content);
			}
		} elseif ($form['field'] == 'custom') {
			if (isset($customsponsor[$cc])) {
				$content = str_replace('[sponsor_'.$cc.']', $customsponsor[$cc], $content);
			}
			if (isset($custommember[$cc])) {
				$content = str_replace('[member_'.$cc.']', $custommember[$cc], $content);
			}
			if (isset($custommysponsor[$cc])) {
				$content = str_replace('[mysponsor_'.$cc.']', $custommysponsor[$cc], $content);
			}
			$cc++;
		} else {			
			if (isset($datasponsor[$form['field']])) {
				$content = str_replace('[sponsor_'.$form['field'].']', $datasponsor[$form['field']], $content);
				$content = str_replace('%5Bsponsor_'.$form['field'].'%5D', $datasponsor[$form['field']], $content);
			}
			if (isset($member->{$form['field']})) {
				$content = str_replace('[member_'.$form['field'].']', $member->{$form['field']}, $content);
				$content = str_replace('%5Bmember_'.$form['field'].'%5D', $member->{$form['field']}, $content);
			}
			if (isset($mysponsor->{$form['field']})) {
				$content = str_replace('[mysponsor_'.$form['field'].']', $mysponsor->{$form['field']}, $content);
				$content = str_replace('%5Bmysponsor_'.$form['field'].'%5D', $mysponsor->{$form['field']}, $content);
			}
		}		
	}

	if (isset($customsponsor['pic_profil'])) {
		$content = str_replace(plugins_url('wp-affiliasi/img/fotosponsor.jpg'), $customsponsor['pic_profil'], $content);
		$content = str_replace('[sponsor_foto]', $customsponsor['pic_profil'], $content);
	}

	if (isset($custommember['pic_profil'])) {
		$content = str_replace(plugins_url('wp-affiliasi/img/fotomember.jpg'), $custommember['pic_profil'], $content);
		$content = str_replace('[member_foto]', $custommember['pic_profil'], $content);
	}
	if (isset($custommysponsor['pic_profil'])) {
		$content = str_replace(plugins_url('wp-affiliasi/img/fotomysponsor.jpg'), $custommysponsor['pic_profil'], $content);
		$content = str_replace('[member_foto]', $custommysponsor['pic_profil'], $content);
	}
	
	/*
	if (is_array($customsponsor)) {
		foreach ($customsponsor as $key => $value) {
			if (isset($custommember[$key])) { $valuemember = $custommember[$key]; } else { $valuemember = ''; }
			if (isset($custommysponsor[$key])) { $valuemysponsor = $custommysponsor[$key]; } else { $valuemysponsor = ''; }
			if ($key == 'whatsapp') {
				$content = str_replace('[sponsor_whatsapp]',formatwa($value),$content);
				$content = str_replace('8888888888',formatwa($value),$content);
				$content = str_replace('[member_whatsapp]',formatwa($valuemember),$content);
				$content = str_replace('9999999999',formatwa($valuemember),$content);
				$content = str_replace('[mysponsor_whatsapp]',formatwa($valuemysponsor),$content);
				$content = str_replace('7777777777',formatwa($valuemysponsor),$content);
			} elseif ($key == 'pic_profil') {
				$content = str_replace(plugins_url('wp-affiliasi/img/fotosponsor.jpg'),$value,$content);
				$content = str_replace('[sponsor_foto]',$value,$content);
				$content = str_replace(plugins_url('wp-affiliasi/img/fotomember.jpg'),$valuemember,$content);
				$content = str_replace('[member_foto]',$valuemember,$content);
				$content = str_replace(plugins_url('wp-affiliasi/img/fotomysponsor.jpg'),$valuemysponsor,$content);
				$content = str_replace('[mysponsor_foto]',$valuemysponsor,$content);
			} else {				
				$content = str_replace('[sponsor_'.$key.']',$value,$content);
				$content = str_replace('[member_'.$key.']',$valuemember,$content);
				$content = str_replace('[mysponsor_'.$key.']',$valuemysponsor,$content);
			}
		}
	}
	

	if (isset($member)) {
		$content = str_replace('[member_urlaff]',urlaff($member->subdomain),$content);
		$content = str_replace('[member_urlpendek]',get_urlpendek(urlaff($member->subdomain)),$content);
	}

	$content = preg_replace('/\[sponsor_(.*)\]/','',$content);
	$content = preg_replace('/\[member_(.*)\]/','',$content);
	$content = preg_replace('/\[mysponsor_(.*)\]/','',$content);
	*/
	$kontenpremium = GetBetween($content,'{premium}','{/premium}');
	$kontenfreemember = GetBetween($content,'{freemember}','{/freemember}');
	$kontenvisitor = GetBetween($content,'{visitor}','{/visitor}');
	$kontenmember = GetBetween($content,'{member}','{/member}');

	
	if (isset($member->membership)) {
		$content = str_replace('{member}'.$kontenmember.'{/member}',$kontenmember,$content);
		$content = str_replace('{visitor}'.$kontenvisitor.'{/visitor}','',$content);
		if ($member->membership == 2) {
			$content = str_replace('{premium}'.$kontenpremium.'{/premium}',$kontenpremium,$content);
		} elseif ($member->membership == 1) {
			$content = str_replace('{freemember}'.$kontenfreemember.'{/freemember}',$kontenfreemember,$content);
		}
	} else {
		$content = str_replace('{visitor}'.$kontenvisitor.'{/visitor}',$kontenvisitor,$content);
		$content = str_replace('{member}'.$kontenmember.'{/member}','',$content);
	}

	$content = str_replace('{premium}'.$kontenpremium.'{/premium}','',$content);
	$content = str_replace('{freemember}'.$kontenfreemember.'{/freemember}','',$content);
	return $content;
}

add_filter( 'the_content', 'cb_filtercontent');

function formatwa($nomor) {	
	$nomor = preg_replace('/[^0-9]/', '', $nomor);
	$nomor = preg_replace('/^620/','62', $nomor);
	$nomor = preg_replace('/^0/','62', $nomor);
	return $nomor;
}

function meta_id_produk() {
    add_meta_box(
      'id_produk', // ID meta box
      'Deklarasi Artikel Khusus Produk', // Judul meta box
      'produk_meta_box', // Fungsi callback yang akan menampilkan konten meta box
      'post', // Jenis post yang akan menggunakan meta box ini
      'side', // Letak meta box (normal, advanced, atau side)
      'high' // Prioritas meta box (high, core, default, atau low)
    );
}
add_action('add_meta_boxes', 'meta_id_produk');

function produk_meta_box($post) {
    global $wpdb;
    $meta_key = 'id_produk';
    $values = get_post_meta($post->ID, $meta_key, true);
    $values = is_array($values) ? $values : array();
    $keterangan_produk = get_post_meta($post->ID, 'keterangan_produk', true);

    // Nonce untuk keamanan
    wp_nonce_field('custom_meta_box_nonce_action', 'custom_meta_box_nonce');

    // Daftar opsi dropdown
    $options = $wpdb->get_results("SELECT * FROM `cb_produk` WHERE `membership`=3");

    echo '<div class="inside">';
    echo '<label for="keterangan_produk">Informasi jika pengunjung belum login atau belum membeli produk</label>';
    echo '<textarea name="keterangan_produk" id="keterangan_produk" rows="4" style="width: 100%;" placeholder="<p>Maaf, untuk bisa mengakses artikel ini, silahkan membeli salah satu produk ini:</p>'."\n".'[listproduk]">' . esc_textarea($keterangan_produk) . '</textarea>';
    echo '</div>';

    // Tampilkan dropdown
    echo '<div class="inside">';
    echo '<select name="' . $meta_key . '[]" id="' . $meta_key . '" class="components-select-control" multiple>';
    foreach ($options as $produk) {
        echo '<option value="' . $produk->id . '" ' . (in_array($produk->id, $values) ? 'selected="selected"' : '') . '>' . $produk->nama . '</option>';
    }
    echo '</select>';
    echo '</div>';
}


function save_meta_produk($post_id) {
  // Verifikasi nonce untuk keamanan
  if (!isset($_POST['custom_meta_box_nonce']) || !wp_verify_nonce($_POST['custom_meta_box_nonce'], 'custom_meta_box_nonce_action')) {
      return;
  }

  // Cek izin pengguna
  if (!current_user_can('edit_post', $post_id)) {
      return;
  }

  // Cek dan simpan data
  $meta_key = 'id_produk';
  if (isset($_POST[$meta_key])) {
      $values = array_map('sanitize_text_field', $_POST[$meta_key]);
      update_post_meta($post_id, $meta_key, $values);
  } else {
      delete_post_meta($post_id, $meta_key);
  }

  if (isset($_POST['keterangan_produk'])) {
      $keterangan_produk = sanitize_textarea_field($_POST['keterangan_produk']);
      update_post_meta($post_id, 'keterangan_produk', $keterangan_produk);
  }
}

add_action('save_post', 'save_meta_produk');

function check_user_product_access($content) {
  if (is_single()) { // Hanya untuk halaman single post
    global $wpdb;

    // Mendapatkan ID produk dari meta post
    $post_id = get_the_ID();
    $id_produk_meta = get_post_meta($post_id, 'id_produk', true);
    $options = get_option('cb_pengaturan');

    // Mendapatkan keterangan produk dari meta post
    $keterangan_produk = get_post_meta($post_id, 'keterangan_produk', true);

    // Jika meta produk tidak kosong
    if (!empty($id_produk_meta)) {
        // Mengonversi nilai meta produk menjadi array jika belum dalam bentuk array
        $id_produk_meta = is_array($id_produk_meta) ? $id_produk_meta : array($id_produk_meta);

        // Periksa apakah user sedang login
        if (is_user_logged_in()) {
            // Mendapatkan informasi user yang sedang login
            $current_user = wp_get_current_user();
            $user_ID = $current_user->ID;

            // Memeriksa apakah user memiliki produk dengan status 1
            $placeholders = implode(',', array_fill(0, count($id_produk_meta), '%d'));
            $query = $wpdb->prepare("
                SELECT COUNT(*) 
                FROM cb_produklain 
                WHERE idwp = %d 
                AND idproduk IN ($placeholders) 
                AND status = 1
            ", array_merge(array($user_ID), $id_produk_meta));

            $count = $wpdb->get_var($query);

            if ($count > 0) {
                // Menampilkan artikel jika syarat terpenuhi
                return $content;
            }
        }

        // Mengambil nama produk dari tabel cb_produk
        $produk_ids = implode(',', array_map('intval', $id_produk_meta));
        $produk_nama = $wpdb->get_results("SELECT id, nama FROM cb_produk WHERE id IN ($produk_ids)");

        $produk_list = '<ul>';
        foreach ($produk_nama as $produk) {
            $produk_list .= '<li><a href="'. site_url('?page_id=' . $options['order'] . '&orderproduk=' . $produk->id) .'">' . esc_html($produk->nama) . '</a></li>';
        }
        $produk_list .= '</ul>';

        // Ganti pesan default dengan keterangan produk dari meta post jika tersedia
        $keterangan_tambahan = !empty($keterangan_produk) ? wp_kses_post($keterangan_produk) : '<p>Maaf, untuk bisa mengakses artikel ini, silahkan membeli salah satu produk ini:</p>[listproduk]';
        $produk_list = str_replace('[listproduk]', $produk_list, $keterangan_tambahan);

        return $produk_list;
    }
  }

  // Jika tidak ada opsi id_produk, tampilkan artikelnya seperti biasa
  return $content;
}
add_filter('the_content', 'check_user_product_access');

function my_custom_query_vars( $query_vars ) {
    $query_vars[] = 'download_id';
    return $query_vars;
}
add_filter( 'query_vars', 'my_custom_query_vars' );

function handle_download_request() {
  global $wpdb, $user_ID;
  if ( get_query_var( 'download_id' ) ) {
      $download_id = get_query_var( 'download_id' );

      // Include file download.php
      include plugin_dir_path( __FILE__ ) . 'downloader.php';

      // Logika untuk proses download berdasarkan $download_id
      // Misalnya memeriksa ID file dan mengunduh file yang sesuai

      exit; // Hentikan eksekusi setelah menangani request
  }
}
add_action( 'template_redirect', 'handle_download_request' );

include_once('dashwidget.php');

function show_admin_menu_for_customers() {
    if (current_user_can('customer')) {
        remove_menu_page('edit.php'); // Sembunyikan menu Posts
        remove_menu_page('tools.php'); // Sembunyikan menu Tools
        remove_menu_page('options-general.php'); // Sembunyikan Settings
    }
}
add_action('admin_menu', 'show_admin_menu_for_customers');
add_filter( 'woocommerce_prevent_admin_access', '__return_false' );
add_filter( 'woocommerce_disable_admin_bar', '__return_false' );

/*
function force_admin_dashboard_layout($user_id) {
    // Ambil tata letak admin (user_id 1)
    $admin_layout = get_user_meta(1, 'meta-box-order_dashboard', true);
    if (!empty($admin_layout)) {
        return $admin_layout;
    }
    return false;
}

// Paksa semua user menggunakan tata letak admin
add_filter('get_user_option_meta-box-order_dashboard', function($value, $option, $user) {
    if (!current_user_can('administrator')) {
        return force_admin_dashboard_layout($user->ID);
    }
    return $value;
}, 10, 3);
*/

// Fungsi untuk mengambil tata letak dashboard admin
function get_admin_dashboard_layout() {
    return get_user_meta(1, 'meta-box-order_dashboard', true);
}

// Fungsi untuk mengambil widget yang disembunyikan oleh admin
function get_admin_hidden_widgets() {
    return get_user_meta(1, 'metaboxhidden_dashboard', true);
}

// Paksa semua user menggunakan tata letak widget admin
add_filter('get_user_option_meta-box-order_dashboard', function($value, $option, $user) {
    if (!current_user_can('administrator')) {
        return get_admin_dashboard_layout();
    }
    return $value;
}, 10, 3);

// Paksa semua user menyembunyikan widget yang disembunyikan oleh admin
add_filter('get_user_option_meta-box-hidden_dashboard', function($value, $option, $user) {
    if (!current_user_can('administrator')) {
        return get_admin_hidden_widgets();
    }
    return $value;
}, 10, 3);

// Pastikan widget yang disembunyikan benar-benar dihapus dari tampilan
add_action('wp_dashboard_setup', function() {
    if (!current_user_can('administrator')) {
        $hidden_widgets = get_admin_hidden_widgets();
        if (!empty($hidden_widgets)) {
            global $wp_meta_boxes;
            foreach ($hidden_widgets as $widget_id) {
                // Hapus widget dari tampilan
                if (isset($wp_meta_boxes['dashboard']['normal']['core'][$widget_id])) {
                    unset($wp_meta_boxes['dashboard']['normal']['core'][$widget_id]);
                }
                if (isset($wp_meta_boxes['dashboard']['side']['core'][$widget_id])) {
                    unset($wp_meta_boxes['dashboard']['side']['core'][$widget_id]);
                }
                if (isset($wp_meta_boxes['dashboard']['column3']['core'][$widget_id])) {
                    unset($wp_meta_boxes['dashboard']['column3']['core'][$widget_id]);
                }
                if (isset($wp_meta_boxes['dashboard']['column4']['core'][$widget_id])) {
                    unset($wp_meta_boxes['dashboard']['column4']['core'][$widget_id]);
                }
            }
        }
    }
}, 999);

function disable_dashboard_widget_dragging() {
    if (!current_user_can('administrator')) {
        ?>
         <style>
        /* Sembunyikan tombol panah pindah widget */
        #dashboard-widgets .postbox .handle-order-higher,
        #dashboard-widgets .postbox .handle-order-lower {
            display: none !important;
        }
        </style>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            let widgetHeaders = document.querySelectorAll("#dashboard-widgets .postbox h2");
            
            widgetHeaders.forEach(header => {
                header.addEventListener("mousedown", function(event) {
                    event.stopPropagation(); // Mencegah drag-and-drop
                });
            });
        });
        </script>
        <?php
    }
}
add_action('admin_head', 'disable_dashboard_widget_dragging');



function hide_screen_options_for_non_admin() {
    if (!current_user_can('administrator')) {
        echo '<style>#screen-meta-links { display: none !important; }</style>';
    }
}
add_action('admin_head', 'hide_screen_options_for_non_admin');

function remove_dashboard_widgets_for_non_admin() {
    if (!current_user_can('administrator')) {
        remove_meta_box('dashboard_activity', 'dashboard', 'normal'); // Hapus widget Activity
        remove_meta_box('dashboard_primary', 'dashboard', 'side'); // Hapus widget WordPress Events and News
    }
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets_for_non_admin');

function remove_profile_menu() {
    if (!current_user_can('administrator')) {
        remove_menu_page('profile.php'); // Hapus menu Profile
    }
}
add_action('admin_menu', 'remove_profile_menu');

function redirect_profile_to_custom() {
    if (!current_user_can('administrator') && strpos($_SERVER['REQUEST_URI'], 'profile.php') !== false) {
        wp_redirect(admin_url('admin.php?page=cbaf_profil'));
        exit;
    }
}
add_action('admin_init', 'redirect_profile_to_custom');

function visitor_data($id_sponsor) {
	$folder_path = WP_CONTENT_DIR . '/logs';

	// Pastikan folder ada
	if (!file_exists($folder_path)) {
	    mkdir($folder_path, 0755, true);
	}

	$file_name = $folder_path . '/visitor_data.txt';
	
  if (!file_exists($file_name)) {
    $myfile = fopen($file_name, "w");
    $visitor_data = array();
  } else {
    $file = fopen($file_name, 'r');
    $visitor_data = array();
    while (!feof($file)) {
        $line = fgets($file);
        if (!empty($line)) {
            $line_data = explode(',', $line);
            if ($line_data[0] == $id_sponsor) {
                $time = $line_data[1];
                $count = (int)$line_data[2];
                if (!isset($visitor_data[$time])) {
                    $visitor_data[$time] = $count;
                } else {
                    $visitor_data[$time] += $count;
                }
            }
        }
    }
    fclose($file);
  }
  return $visitor_data;
}

?>