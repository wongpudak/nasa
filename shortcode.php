<?php
add_shortcode('cb_kontak', 'cb_kontak');			# membuat halaman kontak
add_shortcode('cb_registrasi', 'cb_registrasi');	# membuat halaman registrasi
add_shortcode('cb_memberarea', 'cb_memberarea');	# membuat halaman memberarea
add_shortcode('cb_order','cb_order');				# membuat halaman order
add_shortcode('displayproduk','displayproduk');		# menampilkan produk yg dijual terpisah
add_shortcode('cb_jmlmember','cb_jmlmember');		# Menampilkan statistik jumlah member berdasarkan status (free, premium, novalid, total)
add_shortcode('member', 'member');					# menampilkan data member
add_shortcode('sponsor', 'sponsor');				# menampilkan data sponsor
add_shortcode('mysponsor', 'mysponsor');			# menampilkan data sponsor
add_shortcode('pagemember', 'pagemember');			# menampilkan page2 memberarea
add_shortcode('urlsponsor', 'urlsponsor');			# menampilkan form isian URL Sponsor
add_shortcode('cb_loginreg', 'cb_loginreg');
add_shortcode('khususfree', 'khususfree');			# menampilkan konten khusus free member
add_shortcode('khususpremium', 'khususpremium');	# menampilkan konten khusus premium member
add_shortcode('freemember', 'freemember');			# menampilkan konten khusus free member dg keterangan
add_shortcode('premium', 'premium');				# menampilkan konten khusus premium member dg keterangan

add_shortcode('memberlist', 'memberlist');			# menampilkan memberlist 
													# [memberlist data="<li>{nama}</li>" marquee=0 membership=10 jumlah=10]
add_shortcode('produkpage', 'produkpage');			# menampilkan page khusus utk pembeli produk
													# [produkpage produk=1]isi konten produk[/produkpage]
add_shortcode('cb_leaderboard', 'cb_leaderboard');	# menampilkan leaderboard


#cb_produk untuk menampilkan produk-produk dg model grid
function cb_kontak( $atts, $content = null) {
	global $wpdb, $user_ID;
	global $subdomain, $nama, $username, $password, $urlreseller;
	global $telp, $kota, $provinsi, $bank, $rekening, $ac, $komisi;
	global $namaprospek, $usernameprospek, $bayar, $namamember;
	include("kontak.php");
	return $kontaktxt;
}

function cb_registrasi($orderproduk='') {
	global $wpdb, $user_ID;
	global $subdomain, $nama, $email, $username, $password, $urlreseller;
	global $telp, $kota, $provinsi, $bank, $rekening, $ac, $komisi;
	global $namaprospek, $usernameprospek, $bayar, $namamember;
	global $val, $blogurl, $options;
	include("registrasi.php");
	return $showtxt;
}

function cb_jmlmember($atts) {
	global $wpdb;
	$showtxt = '';
	$a = shortcode_atts( 
		array(
			'data' => 'total'
		), $atts);
	$jmlmember = $wpdb->get_results("SELECT `membership`, COUNT(*) AS `jml_member`
								FROM `wp_member` GROUP BY `membership`");
	$totalmember = array(0,0,0);
	if (count($jmlmember) > 0) {
		foreach ($jmlmember as $jmlmember) {
			$totalmember[$jmlmember->membership] = $jmlmember->jml_member;
		}
	}

	switch ($a['data']) {
		case 'free':
			return number_format($totalmember[1]);
			break;
		case 'premium':
			return number_format($totalmember[2]);
			break;
		case 'novalid':
			return number_format($totalmember[0]);
			break;		
		default:
			return number_format(array_sum($totalmember));
			break;
	}
}

function member($atts) {
	$showtxt = '';
	$a = shortcode_atts( 
		array(
			'data' => 'nama',
			'ganti' => '',
			'text' => 'Chat via WhatsApp',
			'pesan' => 'Mohon info Lengkap'
		), $atts);
	if (defined('CB_MEMBER')) {
		#$datamember = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`=".$user_ID);
		$datamember = unserialize(CB_MEMBER);

		$custommember = unserialize(stripslashes($datamember->homepage));
		if (isset($datamember->nama)) {
			if (substr($a['data'],0,6) == 'custom') {
				$showdata = str_replace('custom','',$a['data']);
				if (isset($custommember[$showdata])) {
					$showtxt = $custommember[$showdata];
				} 
			} elseif ($a['data'] == 'whatsapp') {
				if (isset($custommember['whatsapp'])) {
					$wa = $custommember['whatsapp'];					
					if ($wa != '') {						
						$wa = formatwa($wa);
						$showtxt = '<a href="https://wa.me/'.$wa.'?text='.urlencode($a['pesan']).'" target="blank">'.$a['text'].'</a>';
					}
				}
			} elseif ($a['data'] == 'foto') {
				if (isset($custommember['pic_profil']) && $custommember['pic_profil'] != '') {
					$showtxt = '<img src="'.$custommember['pic_profil'].'" alt="'.$datamember->nama.'" class="pic_profil" />';
				}
			} elseif ($a['data'] == 'urlaff') {
				$showtxt = urlaff($datamember->subdomain);			
			} elseif ($a['data'] == 'urlpendek') {
				$showtxt = get_urlpendek(urlaff($datamember->subdomain));
			} elseif ($a['data'] == 'status') {
				$status = array('Blm Validasi','Free', 'Premium');
				$showtxt = $status[$datamember->membership];
			} elseif ($a['data'] == 'jml_voucher' || $a['data'] == 'sisa_voucher') {
				$showtxt = number_format($datamember->{$a['data']});		
			} else {
				$showtxt = $datamember->{$a['data']}??'';
			}
		}
	} 

	if ($a['ganti'] != '' && $showtxt == '') {
		$showtxt = $a['ganti'];
	} 

	return $showtxt;
}

function mysponsor($atts) {	
	$showtxt = '';
	$a = shortcode_atts( 
		array(
			'data' => 'nama',
			'ganti' => '',
			'text' => 'Chat via WhatsApp',
			'pesan' => 'Mohon info Lengkap'
		), $atts);

	if (defined('CB_MEMBER')) {
		$datamember = unserialize(CB_MEMBER);
		if ($datamember->id_referral != 0) {
			$datasponsor = unserialize(CB_MYSPONSOR);			
			$custom = unserialize($datasponsor->homepage);

			if (isset($datasponsor->idwp)) {
				if (substr($a['data'],0,6) == 'custom') {
					$showdata = str_replace('custom','',$a['data']);
					if (isset($custom[$showdata])) {
						$showtxt = $custom[$showdata];
					} 
				} elseif ($a['data'] == 'whatsapp') {
					if (isset($custom['whatsapp'])) {
						if (isset($custom['whatsapp'])) {
							$wa = $custom['whatsapp'];					
							if ($wa != '') {						
								$wa = formatwa($wa);
								$showtxt = '<a href="https://wa.me/'.$wa.'?text='.urlencode($a['pesan']).'" target="blank">'.$a['text'].'</a>';
							}
						}
					}
				} elseif ($a['data'] == 'foto') {
					if (isset($custom['pic_profil']) && $custom['pic_profil'] != '') {
						$showtxt = '<img src="'.$custom['pic_profil'].'" alt="'.$datasponsor->nama.'" class="pic_profil" />';
					}
				} elseif ($a['data'] == 'urlaff') {
					$showtxt = urlaff($datasponsor->subdomain);
				} elseif ($a['data'] == 'jml_voucher' || $a['data'] == 'sisa_voucher') {
					$showtxt = number_format($datasponsor->$a['data']);				
				} elseif ($a['data'] == 'status') {
					$status = array('Blm Validasi','Free Member', 'Premium');
					$showtxt = $status[$datasponsor->membership];		
				} else {
					$showtxt = $datasponsor->{$a['data']};
				}
			} else {
				$showtxt = 'bukan array';
			}
		} 
	} 

	if ($a['ganti'] != '' && $showtxt == '') {
		$showtxt = $a['ganti'];
	} 
	return $showtxt;
}

function sponsor($atts) {
	$showtxt = '';
	$a = shortcode_atts( 
		array(
			'data' => 'nama',
			'ganti' => '',
			'text' => 'Chat via WhatsApp',
			'pesan' => 'Mohon info Lengkap'
		), $atts);
	
	if (defined('CB_SPONSOR')) {
		$datasponsor = unserialize(CB_SPONSOR);
		$custom = unserialize($datasponsor['homepage']);
			
		if (is_array($datasponsor)) {
			if (substr($a['data'],0,6) == 'custom') {
				$showdata = str_replace('custom','',$a['data']);
				if (isset($custom[$showdata])) {
					$showtxt = $custom[$showdata];
				} 
			} elseif ($a['data'] == 'whatsapp') {
				if (isset($custom['whatsapp'])) {
					if (isset($custom['whatsapp'])) {
						$wa = $custom['whatsapp'];					
						if ($wa != '') {						
							$wa = formatwa($wa);
							$showtxt = '<a href="https://wa.me/'.$wa.'?text='.urlencode($a['pesan']).'" target="blank">'.$a['text'].'</a>';
						}
					}
				}
			} elseif ($a['data'] == 'foto') {
				if (isset($custom['pic_profil']) && $custom['pic_profil'] != '') {
					$showtxt = '<img src="'.$custom['pic_profil'].'" alt="'.$datasponsor['nama'].'" class="pic_profil" />';
				}
			} elseif ($a['data'] == 'urlaff') {
				$showtxt = urlaff($datasponsor['subdomain']);
			} elseif ($a['data'] == 'status') {
				$status = array('Blm Validasi','Free Member', 'Premium');
				$showtxt = $status[$datasponsor['membership']];		
			} else {
				$showtxt = $datasponsor[$a['data']];
			}
		} 
	} 

	if ($a['ganti'] != '' && $showtxt == '') {
		$showtxt = $a['ganti'];
	} 
	return $showtxt;
}

function cb_memberarea() {	
	global $wpdb, $user_ID, $member;
	global $subdomain, $nama, $username, $password, $urlreseller;
	global $telp, $kota, $provinsi, $bank, $rekening, $ac, $komisi;
	global $namaprospek, $usernameprospek, $bayar, $namamember;
	global $val, $blogurl, $options;
	$showtxt = '';
	if (isset($user_ID) && is_numeric($user_ID) && $user_ID > 0) {
		$menuoption = get_option('menuoption');
		if (is_array($menuoption)) {
			//$showtxt .= '<p><a href="'.site_url().'/?page_id='.get_the_ID().'&hal=home">Home</a>';
			$showtxt .= '<p>';
			if (isset($menuoption['home_cek']) && $menuoption['home_cek'] == 1) { 
				$showtxt .= '<a href="'.site_url().'/?page_id='.get_the_ID().'&hal=home">';
				if ($menuoption['home_label'] != '') { $showtxt .= $menuoption['home_label']; } else { $showtxt .= 'Home';}
				$showtxt .= '</a>';
			}
			if (isset($menuoption['profil_cek']) && $menuoption['profil_cek'] == 1) { 
				$showtxt .= ' | <a href="'.site_url().'/?page_id='.get_the_ID().'&hal=profil">';
				if ($menuoption['profil_label'] != '') { $showtxt .= $menuoption['profil_label']; } else { $showtxt .= 'Profil';}
				$showtxt .= '</a>';
			}
			if (isset($menuoption['laporan_cek']) && $menuoption['laporan_cek'] == 1) { 
				$showtxt .= ' | <a href="'.site_url().'/?page_id='.get_the_ID().'&hal=laporan">';
				if ($menuoption['laporan_label'] != '') { $showtxt .= $menuoption['laporan_label']; } else { $showtxt .= 'Laporan';}
				$showtxt .= '</a>';
			}
			if (isset($menuoption['banner_cek']) && $menuoption['banner_cek'] == 1) { 
				$showtxt .= ' | <a href="'.site_url().'/?page_id='.get_the_ID().'&hal=promosi">';
				if ($menuoption['banner_label'] != '') { $showtxt .= $menuoption['banner_label']; } else { $showtxt .= 'Banner';}
				$showtxt .= '</a>';
			}
			if (isset($menuoption['klien_cek']) && $menuoption['klien_cek'] == 1) { 
				$showtxt .= ' | <a href="'.site_url().'/?page_id='.get_the_ID().'&hal=klien">';
				if ($menuoption['klien_label'] != '') { $showtxt .= $menuoption['klien_label']; } else { $showtxt .= 'Klien';}
				$showtxt .= '</a>';
			}
			if (isset($menuoption['jaringan_cek']) && $menuoption['jaringan_cek'] == 1) { 
				$showtxt .= ' | <a href="'.site_url().'/?page_id='.get_the_ID().'&hal=jaringan">';
				if ($menuoption['jaringan_label'] != '') { $showtxt .= $menuoption['jaringan_label']; } else { $showtxt .= 'Jaringan';}
				$showtxt .= '</a>';
			}
			if (isset($menuoption['download_cek']) && $menuoption['download_cek'] == 1) { 
				$showtxt .= ' | <a href="'.site_url().'/?page_id='.get_the_ID().'&hal=download">';
				if ($menuoption['download_label'] != '') { $showtxt .= $menuoption['download_label']; } else { $showtxt .= 'Download';}
				$showtxt .= '</a>';
			}
			if (isset($menuoption['upgrade_cek']) && $menuoption['upgrade_cek'] == 1) { 
				if ($wpdb->get_var("SELECT `membership` FROM `wp_member` WHERE idwp = ".$user_ID) == 1) {
					$showtxt .= ' | <a href="'.site_url().'/?page_id='.get_the_ID().'&hal=order&idproduk=premium">';
					if ($menuoption['upgrade_label'] != '') { $showtxt .= $menuoption['upgrade_label']; } else { $showtxt .= 'Upgrade';}
					$showtxt .= '</a>';
				}
			}
			if (isset($menuoption['logout_cek']) && $menuoption['logout_cek'] == 1) { 
				$showtxt .= ' | <a href="'.wp_logout_url(site_url()).'">';
				if ($menuoption['logout_label'] != '') { $showtxt .= $menuoption['logout_label']; } else { $showtxt .= 'Logout';}
				$showtxt .= '</a>';
			}		
			$showtxt .= '</p>';
		} else {
			$showtxt .= '<p><a href="'.site_url().'/?page_id='.get_the_ID().'&hal=home">Home</a> | 
			<a href="'.site_url().'/?page_id='.get_the_ID().'&hal=profil">Profil</a> | 
			<a href="'.site_url().'/?page_id='.get_the_ID().'&hal=laporan">Laporan</a> | 
			<a href="'.site_url().'/?page_id='.get_the_ID().'&hal=promosi">Banner</a> | 
			<a href="'.site_url().'/?page_id='.get_the_ID().'&hal=klien">Klien</a> | 
			<a href="'.site_url().'/?page_id='.get_the_ID().'&hal=jaringan">Jaringan</a> | 
			<a href="'.site_url().'/?page_id='.get_the_ID().'&hal=download">Download</a>';
			if ($wpdb->get_var("SELECT `membership` FROM `wp_member` WHERE idwp = ".$user_ID) == 1) {
			 $showtxt .= ' | <a href="'.site_url().'/?page_id='.get_the_ID().'&hal=order&idproduk=premium">Upgrade</a>'; 
			}
			$showtxt .= '
			| <a href="'.wp_logout_url(site_url()).'">Logout</a></p>';
		}

		$memberpage = '';
		if (isset($_GET['hal'])) { $memberpage = $_GET['hal']; }
		switch ($memberpage) {
		case 'profil' : include('memberprofil.php'); break;
		case 'laporan' : include('memberlaporan.php'); break;
		case 'promosi' : include('memberpromosi.php'); break;
		case 'klien' : include('memberklien.php'); break;
		case 'jaringan' : include('memberjaringan.php'); break;
		case 'download' : include('memberdownload.php'); break;	
		case 'order' : 
			include('memberorder.php'); 
			$showtxt = $konten;
			break;
		default : include("memberarea.php");
		}
	} else {
		$refer = $_SERVER['REQUEST_URI'];
		header("Location: ".wp_login_url($refer));
		exit;
	}

	return $showtxt;
}

function pagemember($atts) {
	global $user_ID, $wpdb, $member;
	if (!isset($user_ID) || $user_ID == 0) { 
		$refer = $_SERVER['REQUEST_URI'];
		header("Location: ".wp_login_url($refer));
		exit;
	}
	$showtxt = '';
	$a = shortcode_atts( 
		array(
			'data' => 'custom'
		), $atts);
	switch ($a['data']) {
		case 'profil' : include('memberprofil.php'); break;
		case 'laporan' : include('memberlaporan.php'); break;
		case 'promosi' : include('memberpromosi.php'); break;
		case 'klien' : include('memberklien.php'); break;
		case 'jaringan' : include('memberjaringan.php'); break;
		case 'download' : include('memberdownload.php'); break;	
		case 'order' : 
			include('memberorder.php'); 
			$showtxt = $konten; 
			break;
		case 'home' : include('memberarea.php'); break;
	}
	return $showtxt;
}

function urlsponsor() {
	$blogurl = cbdomain();
	if (isset($_POST['subdomain']) && $_POST['subdomain'] != '') {
		if (substr($_POST['subdomain'], 0,4) == 'http') {
			header("Location:".$_POST['subdomain']);
		} else {
			if (get_option('affsub') == 1) {
				$url = 'http://'.$_POST['subdomain'].'.'.$blogurl;
			} else {
				$url = site_url().'/?reg='.$_POST['subdomain'];
			}
			header("Location:".$url);
			exit;
		}
	}
	
	$result = '<form action="" method="post">';
	if (get_option('affsub') == 1) {
		$result .= 'http://<input type="text" name="subdomain" class="subdomain" style="width:30%; display:inline">.'.$blogurl;
	} else {
		$result .= site_url().'/?reg=<input type="text" name="subdomain" class="subdomain">';
	}
	$result .= ' <input type="submit" value="GO"/></form>';
	return $result;
}

function cb_order() {
	if ( is_singular()) {
		include('memberorder.php');	
	} else {
		$konten = 'ORDER KONTEN';
	}
	return $konten;
}

function cb_loginreg($orderproduk='') {
	global $wpdb;
	include('cblogin.php');
	return $showtxt;
}


function khususfree($atts, $content = "") {
	global $user_ID, $wpdb;
	if (isset($user_ID) && $user_ID > 0) {
		if ($wpdb->get_var("SELECT `membership` FROM `wp_member` WHERE `idwp`=".$user_ID) == 1) {
			return $content;
		} else {
			return '';
		}
	} else {		
		return '';
	}
}

function khususpremium($atts, $content = "") {
	global $user_ID, $wpdb;
	if (isset($user_ID) && $user_ID > 0) {
		if ($wpdb->get_var("SELECT `membership` FROM `wp_member` WHERE `idwp`=".$user_ID) == 2) {
			return $content;
		} else {
			return '';
		}
	} else {		
		return '';
	}
}

function freemember($atts, $content = "") {
	global $user_ID, $wpdb;
	$options = get_option('cb_pengaturan');
	if (isset($user_ID) && $user_ID > 0) {
		if ($wpdb->get_var("SELECT `membership` FROM `wp_member` WHERE `idwp`=".$user_ID) >= 1) {
			return $content;
		} else {
			return '<p>Kelanjutan artikel ini hanya bisa dibaca oleh Member, <a href="'.wp_login_url(get_permalink()).'&reauth=1">silahkan login disini dulu</a> atau <a href="'.site_url().'/?page_id='.$options['registrasi'].'">Registrasi di sini</a></p>';
		}
	} else {		
		return '<p>Kelanjutan artikel ini hanya bisa dibaca oleh Member, <a href="'.wp_login_url(get_permalink()).'&reauth=1">silahkan login disini dulu</a> atau <a href="'.site_url().'/?page_id='.$options['registrasi'].'">Registrasi di sini</a></p>';
	}
}

function premium($atts, $content = "") {
	global $user_ID, $wpdb;
	$options = get_option('cb_pengaturan');
	if (isset($user_ID) && $user_ID > 0) {
		if ($wpdb->get_var("SELECT `membership` FROM `wp_member` WHERE `idwp`=".$user_ID) == 2) {
			return $content;
		} else {
			return '<p>Kelanjutan artikel ini hanya bisa dibaca oleh <b>Premium Member</b>, <a href="'.site_url().'/?page_id='.$options['order'].'">Silahkan Upgrade dulu</a></p>';
		}
	} else {		
		return '<p>Kelanjutan artikel ini hanya bisa dibaca oleh Member, <a href="'.wp_login_url(get_permalink()).'&reauth=1">silahkan login disini dulu</a> atau <a href="'.site_url().'/?page_id='.$options['registrasi'].'">Registrasi di sini</a></p>';
	}
}



function displayproduk() {
	global $user_ID,$member,$wpdb;
	$showtxt = '';
	include('displayproduk.php');
	return $showtxt;
}

function memberlist($atts) {
	global $wpdb, $user_ID;
	$showtxt = '';
	$a = shortcode_atts( 
		array(
			'data' => '<li>{nama}</li>',
			'marquee' => 0,
			'membership' => 10,
			'jumlah' => 10,
		), $atts);

	if ($a['membership'] == 10) {	# Show all member
		$memberlist = $wpdb->get_results("SELECT * FROM `wp_member` ORDER BY `tgl_daftar` DESC LIMIT 0,".$a['jumlah']);
	} elseif ($a['membership'] == 1 || $a['membership'] == 2) { # show only free or premium
		$memberlist = $wpdb->get_results("SELECT * FROM `wp_member` WHERE `membership`= ".$a['membership']." ORDER BY `tgl_daftar` DESC LIMIT 0,".$a['jumlah']);
	} 

	foreach ($memberlist as $member) {
		$ganti = $a['data'];		
		foreach ($member as $key => $value) {	
			if ($key == 'homepage') {
				$custom = unserialize($value);
				foreach ($custom as $keyval => $customval) {
					$cari = '{'.$keyval.'}';
					$ganti = str_replace(strtolower($cari), strtolower($value), $ganti);
				}
			} else {
				$cari = '{'.$key.'}';
				$ganti = str_replace(strtolower($cari), strtolower($value??=''), $ganti);
			}
		}
		$ganti = str_replace('{pic_profil}', plugins_url('wp-affiliasi/img/nopic.jpg'), $ganti);
		$showtxt .= $ganti;
		
	}

	if ($a['marquee'] == 1) {
		$showtxt = '<div class="marquee ver" data-direction="up" data-duration="4000" data-pauseOnHover="true">
		<ul>'.$showtxt.'</ul></div>';
	} else {
		$showtxt = '<div class="memberlist"><ul>'.$showtxt.'</ul></div>';
	}

	return $showtxt;
	
}

function produkpage($atts, $content = "") {
	global $wpdb, $user_ID;
	$showtxt = '';
	$a = shortcode_atts( 
		array(
			'id' => 0
		), $atts);
	$check = $wpdb->get_var("SELECT `status` FROM `cb_produklain` WHERE `idproduk`=".$a['id']." AND `idwp`=".$user_ID);
	if ($check == 1) {
		return $content;
	} else {
		return '';
	}
}

function cb_leaderboard($atts) {
	global $user_ID, $wpdb;	
	/*
	Data :
	- komisi 	 : berdasar komisi terbanyak
	- sale 		 : berdasar jumlah penjualan terbanyak
	- premium 	 : berdasar jumlah premium member terbanyak
	- rekrut	 : berdasar jumlah rekrut terbanyak	
	*/
	$showtxt = '';
	$start = date("Y-m-d", strtotime('-1 month'));	
	$a = shortcode_atts( 
		array(
			'data' => 'komisi',
			'start' => $start,
			'end' => date("Y-m-d"),
			'format' => '<li>{nama} - {total}</li>',
			'jumlah' => 10
		), $atts);

	if ($a['start'] == 'all') {
		$range = $rangemember = '';
	} else {
		$range = "AND `cb_laporan`.`tanggal` > '".$a['start']." 00:00:00'
		AND `cb_laporan`.`tanggal` < '".$a['end']." 23:59:59'";
		if ($a['data'] == 'rekrut') {
			$rangemember = "AND `tgl_daftar` > '".$a['start']." 00:00:00'
			AND `tgl_daftar` < '".$a['end']." 23:59:59'";
		} else {
			$rangemember = "AND `tgl_upgrade` > '".$a['start']." 00:00:00'
			AND `tgl_upgrade` < '".$a['end']." 23:59:59'";
		}
	}

	switch ($a['data']) {
		case 'komisi':
			$query = "SELECT *, SUM(`komisi`) AS `total` FROM `cb_laporan`,`wp_member` 			 
			WHERE `cb_laporan`.`id_sponsor` = `wp_member`.`idwp`
			".$range."
            GROUP BY `cb_laporan`.`id_sponsor`
			ORDER BY `total` DESC
			LIMIT 0,".$a['jumlah'];
			break;
		case 'sale':
			$query = "SELECT *, COUNT(`cb_laporan`.`id`) AS `total` FROM `cb_laporan`,`wp_member` 			 
			WHERE `cb_laporan`.`id_sponsor` = `wp_member`.`idwp`
			AND `cb_laporan`.`id_order` > 0
			".$range."
            GROUP BY `cb_laporan`.`id_sponsor`
			ORDER BY `total` DESC
			LIMIT 0,".$a['jumlah'];
			break;
		case 'premium':			
			$sponsor = $wpdb->get_results("SELECT *, COUNT(`id_user`) AS `total` FROM `wp_member` 
			WHERE `membership`=2 ".$rangemember."
			GROUP BY `id_referral` ORDER BY `total` DESC
			LIMIT 0,".$a['jumlah']);
			//print_r($sponsor);
			if (count($sponsor) > 0) {
				$listsponsor = '';
				$i = 0;
				foreach ($sponsor as $sponsor) {
					if ($sponsor->id_referral > 0) {
						$listsponsor .= $sponsor->id_referral.',';
						$total[$i] = $sponsor->total;
						$i++;
					}
				}
				$listsponsor = substr($listsponsor,0,-1);
				$query = "SELECT * FROM `wp_member`
				WHERE `idwp` IN (".$listsponsor.") ORDER BY FIELD (`idwp`,".$listsponsor.")";
			}
			break;
		case 'rekrut':
			$sponsor = $wpdb->get_results("SELECT *, COUNT(`id_user`) AS `total` FROM `wp_member` 
			WHERE `membership`> 0 ".$rangemember."
			GROUP BY `id_referral` ORDER BY `total` DESC
			LIMIT 0,".$a['jumlah']);
			if (count($sponsor) > 0) {
				//print_r($sponsor);
				$listsponsor = '';
				$i = 0;
				foreach ($sponsor as $sponsor) {
					if ($sponsor->id_referral > 0) {
						$listsponsor .= $sponsor->id_referral.',';
						$total[$i] = $sponsor->total;
						$i++;
					}
				}
				$listsponsor = substr($listsponsor,0,-1);
				$query = "SELECT * FROM `wp_member`
				WHERE `idwp` IN (".$listsponsor.") ORDER BY FIELD (`idwp`,".$listsponsor.")";
			}
			break;	
	}

	if (isset($query)) {	
		$data = $wpdb->get_results($query);
		$i = 0;
		foreach ($data as $item) {
			$txtdata = $a['format'];
			foreach ($item as $key => $value) {
				if ($key == 'total') {
					$txtdata = str_replace('{'.$key.'}',number_format($value),$txtdata);					
				} else {
					$txtdata = str_replace('{'.$key.'}',$value,$txtdata);
				}

				if (isset($total[$i])) {
					$txtdata = str_replace('{total}',number_format($total[$i]),$txtdata);
				}
			}
			$showtxt .= $txtdata;
			$i++;
		}
	} else {
		$showtxt = 'belum ada daftar';
	}
	
	return $showtxt;
}
?>