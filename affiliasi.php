<?php 
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
global $wpdb,$user_ID;
if ($wpdb->get_var("show tables like 'wp_member'")) {
	$namadefault = $options = '';
	$options = get_option('cb_pengaturan');

	// Hilangkan www dari alamat
	$url = $_SERVER['HTTP_HOST'];
	if (substr($url,0,4) == 'www.') {
		$url = substr($url, 4);
	}

	$path = $_SERVER['REQUEST_URI'];
	if ($path == '/wp-login.php?action=register') {
		header("Location:".site_url('?page_id='.$options['registrasi']));
	}

	$blogurl = str_replace('https://', '', get_bloginfo('wpurl'));
	$blogurl = str_replace('http://', '', $blogurl);
	if (substr($blogurl,0,4) == 'www.') {
		$blogurl = substr($blogurl, 4);
	}

	if (isset($_GET['reg']) && $_GET['reg'] != '') { # Handle URL REG
		// cari member dg reg itu
		$datasponsor = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `subdomain`='".sanitize_text_field($_GET['reg'])."'",ARRAY_A);
		if (isset($datasponsor['idwp']) && is_numeric($datasponsor['idwp'])) {
			if (isset($options['khususpremium']) && $options['khususpremium'] == 1) {
				if (isset($datasponsor['membership']) && $datasponsor['membership'] >=2) {
					$idsponsor = $datasponsor['idwp'];
				}
			} else {
				$idsponsor = $datasponsor['idwp'];
			}
		} elseif (isset($options['salahlink']) && is_numeric($options['salahlink']) && $options['salahlink'] != 0) {
			header("Location:".site_url()."/?page_id=".$options['salahlink']."&sponsor=no");
		}	
	} elseif ($url != $blogurl) { # Handle URL Subdomain
		$wpaffsub = str_replace('.'.$blogurl, '', $url);
		$datasponsor = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `subdomain`='".$wpaffsub."'",ARRAY_A);
		if (isset($datasponsor['idwp']) && is_numeric($datasponsor['idwp'])) {
			if (isset($options['khususpremium']) && $options['khususpremium'] == 1) {
				if (isset($datasponsor['membership']) && $datasponsor['membership'] >=2) {
					$idsponsor = $datasponsor['idwp'];					
				}
			} else {
				$idsponsor = $datasponsor['idwp'];
			}			
		} elseif (isset($options['salahlink']) && is_numeric($options['salahlink']) && $options['salahlink'] != 0) {
			header("Location:".site_url()."/?page_id=".$options['salahlink']."&sponsor=no");
		}	
	} elseif (isset($_COOKIE["idsponsor"]) && is_numeric($_COOKIE["idsponsor"])) { # Handle Cookie jika 2 URL di atas gak ada
		$datasponsor = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`=".$_COOKIE["idsponsor"],ARRAY_A);
		if (isset($datasponsor['idwp']) && is_numeric($datasponsor['idwp'])) {
			$idsponsor = $datasponsor['idwp'];
		}
	} elseif (isset($_GET['sponsor']) && $_GET['sponsor'] == 'no') {
		// Diam saja, nanti akan dicari sponsor random di bawah

	} elseif (isset($_GET['action']) && $_GET['action'] == 'activate') { # Lupa ini buat apaan
		$datasponsor = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`='".$user_ID."'",ARRAY_A);
		if (isset($datasponsor['idwp']) && is_numeric($datasponsor['idwp'])) {
			$idsponsor = $datasponsor['idwp'];
		}
	} elseif (isset($options['carisponsor']) && is_numeric($options['carisponsor']) && $options['carisponsor'] != 0) {
		header("Location:".site_url()."/?page_id=".$options['carisponsor']."&sponsor=no");
	}

	if (isset($idsponsor) && is_numeric($idsponsor)) {
		// Oke sip
	} else {
		if (isset($options['default']) && $options['default'] != '') {
			$datasponsor = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp` IN (".$options['default'].") ORDER BY RAND() LIMIT 0,1",ARRAY_A);
		} else {
			$datasponsor = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `membership`=2 ORDER BY RAND() LIMIT 0,1",ARRAY_A);	
		}
		
		if (isset($datasponsor['idwp']) && is_numeric($datasponsor['idwp'])) {
			$idsponsor = $datasponsor['idwp'];
		}
	}

	setcookie("idsponsor", "", strtotime('-30 days'),'/');
	setcookie("idsponsor",$datasponsor['idwp'],strtotime('+30 days'),'/');
	$datasponsor['jmlinvalid'] = '';
	$datasponsor['jmlfree'] = '';
	$datasponsor['jmlpremium'] = '';
	$datasponsor['jmldownline'] = '';
	$sesdata = serialize($datasponsor);
	if (!defined('CB_SPONSOR')) { 
		define('CB_SPONSOR', $sesdata);
	}
		
	if (isset($_GET['sponsor']) && $_GET['sponsor'] == 'no') {
		setcookie("idsponsor", "", strtotime('-30 days'),'/');
	}


	if (!isset($_COOKIE['visitor_count']) || (isset($_COOKIE['visitor_count']) && $_COOKIE['visitor_count'] != $datasponsor['idwp'])) {
	  setcookie("visitor_count", "", time() - 3600, '/'); // Hapus cookie lama
    setcookie("visitor_count", $datasponsor['idwp'], time() + 86400, '/'); // Simpan cookie selama 1 hari

		$folder_path = WP_CONTENT_DIR . '/logs';

		// Pastikan folder ada
		if (!file_exists($folder_path)) {
		    mkdir($folder_path, 0755, true);
		}

		$file_name = $folder_path . '/visitor_data.txt';
		
		$file = fopen($file_name, 'a+');
		$data = array();
		while (!feof($file)) {
		    $line = fgets($file);
		    if (!empty($line)) {
		        $line_data = explode(',', $line);	        
		        if (count($line_data) == 3) {
			        $id_sponsor = $line_data[0];
			        $time = $line_data[1];
			        $count = (int)$line_data[2];
			        if (!isset($data[$id_sponsor])) {
			            $data[$id_sponsor] = array();
			        }
			        $data[$id_sponsor][$time] = $count;
		        }
		    }
		}

		$current_time = date('Ymd');
		$id_sponsor = $datasponsor['idwp'];
		if (isset($data[$id_sponsor][$current_time])) {
		    $data[$id_sponsor][$current_time]++;
		} else {
		    $data[$id_sponsor][$current_time] = 1;
		}
		
		ftruncate($file, 0);
		rewind($file);
		foreach ($data as $id_sponsor => $visitor_data) {
		    foreach ($visitor_data as $time => $count) {
		        fwrite($file, $id_sponsor.','.$time.','.$count.PHP_EOL);
		    }
		}

		fclose($file);
	} 

}