<?php
function adminarea() {
	add_menu_page('Administrasi', 'Administrasi', '', 'wpaff_admin','cbaf_orderlist','dashicons-list-view');
	add_submenu_page('wpaff_admin', 'Order', 'Daftar Order', 'manage_options', 'cbaf_orderlist', 'cbaf_orderlist');
	add_submenu_page('wpaff_admin', 'Laporan Keuangan', 'Laporan', 'manage_options', 'cbaf_laporan', 'cbaf_laporan');
	add_submenu_page('wpaff_admin', 'Member List', 'Member List', 'manage_options', 'cbaf_memberlist', 'cbaf_memberlist');
	add_submenu_page('wpaff_admin', 'Tambah Member', 'Tambah Member', 'manage_options', 'cbaf_tambah', 'cbaf_tambah');	
	add_submenu_page('wpaff_admin', 'Bayar Affiliasi', 'Bayar Komisi', 'manage_options', 'cbaf_bayar', 'cbaf_bayar');
}

function konfigurasi() {
	add_menu_page('Pengaturan', 'Pengaturan', '', 'wpaff_konfigurasi','cbaf_pengaturan','dashicons-admin-tools');
	add_submenu_page('wpaff_konfigurasi', 'Pengaturan Umum','Pengaturan Umum', 'manage_options', 'pengaturan', 'cbaf_pengaturan');
	add_submenu_page('wpaff_konfigurasi', 'Pengaturan Metode Pembayaran','Metode Pembayaran', 'manage_options', 'cbaf_pembayaran', 'cbaf_pembayaran');
	add_submenu_page('wpaff_konfigurasi', 'Pengaturan Form Pendaftaran', 'Form Pendaftaran', 'manage_options', 'cbaf_daftar', 'cbaf_daftar');
	add_submenu_page('wpaff_konfigurasi', 'Pengaturan Produk', 'Produk', 'manage_options', 'cbaf_produk', 'cbaf_produk');
	add_submenu_page('wpaff_konfigurasi', 'Pengaturan Email', 'Email', 'manage_options', 'cbaf_konfemail', 'cbaf_konfemail');
	add_submenu_page('wpaff_konfigurasi', 'Pengaturan SMS', 'SMS Notifikasi', 'manage_options', 'cbaf_sms', 'cbaf_sms');
	add_submenu_page('wpaff_konfigurasi', 'Pengaturan Menu', 'Menu Member Area', 'manage_options', 'cbaf_menu', 'cbaf_menu');	
	add_submenu_page('wpaff_konfigurasi', 'Pengaturan Komisi', 'Komisi Affiliasi', 'manage_options', 'cbaf_komisi', 'cbaf_komisi');
	add_submenu_page('wpaff_konfigurasi', 'Pengaturan Banner', 'Banner Promosi', 'manage_options', 'cbaf_mediapromo', 'cbaf_mediapromo');
	add_submenu_page('wpaff_konfigurasi', 'Pengaturan Autoresponder','Autoresponder', 'manage_options', 'cbaf_autoresponder', 'cbaf_autoresponder');			
	add_submenu_page('wpaff_konfigurasi', 'Panduan', 'Panduan WP-Affiliasi', 'manage_options', 'cbaf_panduan', 'cbaf_panduan');	
	add_submenu_page('wpaff_konfigurasi', 'Uninstall', 'Uninstall', 'manage_options', 'cbaf_uninstall', 'cbaf_uninstall');	
}

function memberarea() {
    // Ambil data user saat ini
    $user = wp_get_current_user();

    // Jika user adalah administrator, gunakan menu dengan submenu
    if (current_user_can('administrator')) {
        add_menu_page('Memberarea', 'Memberarea', '', 'wpaff_member', 'cbaf_home', 'dashicons-id');
        add_submenu_page('wpaff_member', 'Home', 'Home Member', 'read', 'cbaf_home', 'cbaf_home');
        add_submenu_page('wpaff_member', 'Laporan Komisi', 'Laporan', 'read', 'cbaf_lapkomisi', 'cbaf_lapkomisi');
        add_submenu_page('wpaff_member', 'Klien List', 'Klien List', 'read', 'cbaf_klienlist', 'cbaf_klienlist');
        add_submenu_page('wpaff_member', 'Profil', 'Profil', 'read', 'cbaf_profil', 'cbaf_profil');
        add_submenu_page('wpaff_member', 'Pesanan Anda', 'Pesanan Anda', 'read', 'cbaf_pesanan', 'cbaf_pesanan');
        add_submenu_page('wpaff_member', 'Jaringan', 'Jaringan', 'read', 'cbaf_jaringan', 'cbaf_jaringan');
    } else {
        // Jika bukan admin, buat menu utama terpisah untuk setiap submenu
        add_menu_page('Laporan Komisi', 'Laporan Komisi', 'read', 'cbaf_lapkomisi', 'cbaf_lapkomisi', 'dashicons-chart-line');
        add_menu_page('Klien List', 'Klien List', 'read', 'cbaf_klienlist', 'cbaf_klienlist', 'dashicons-groups');
        add_menu_page('Profil', 'Profil', 'read', 'cbaf_profil', 'cbaf_profil', 'dashicons-admin-users');
        add_menu_page('Pesanan Anda', 'Pesanan Anda', 'read', 'cbaf_pesanan', 'cbaf_pesanan', 'dashicons-cart');
        add_menu_page('Jaringan', 'Jaringan', 'read', 'cbaf_jaringan', 'cbaf_jaringan', 'dashicons-networking');
    }
}

// ======================== MENU ADMINISTRASI =============================== //

function cbaf_laporan() {
	global $wpdb, $user_ID;
	include ("adminmenu/laporan.php");
}

function cbaf_mutasi() {
	global $user_ID, $options;
	include('adminmenu/mutasi.php');
}

function cbaf_memberlist() {
	global $wpdb, $user_ID, $user_identity, $sponsorkita;
	global $subdomain, $nama, $username, $password, $urlreseller;
	global $telp, $kota, $provinsi, $bank, $rekening, $ac, $komisi;
	global $namaprospek, $usernameprospek, $status, $blogurl;
	include ("adminmenu/membercontrol.php");
}

function cbaf_orderlist() {
	global $wpdb, $options, $user_ID, $user_identity;
	global $subdomain, $nama, $username, $password, $urlreseller;
	global $telp, $kota, $provinsi, $bank, $rekening, $ac, $komisi;
	global $namaprospek, $usernameprospek, $status, $blogurl;
	include ("adminmenu/orderlist.php");
}

function cbaf_tambah() {
	global $wpdb, $user_ID, $nama, $username, $password, $telp, $kota, $val,$id_referral;
	global $provinsi, $ac, $bank, $subdomain, $rekening, $blogurl, $options;
	include('adminmenu/tambahmember.php');
}

function cbaf_bayar() {
	global $wpdb, $user_ID;
	global $subdomain, $nama, $username, $password, $urlreseller;
	global $telp, $kota, $provinsi, $bank, $rekening, $ac, $komisi;
	global $namaprospek, $usernameprospek, $bayar, $namamember;
	include ("adminmenu/bayar.php");
}

// ======================== MENU KONFIGURASI =============================== //

function cbaf_pengaturan() {
	global $wpdb, $user_ID;
	include ("adminmenu/pengaturan.php");
}


function cbaf_pembayaran() {
	global $wpdb, $user_ID;
	include ("adminmenu/pembayaran.php");
}

function cbaf_autoresponder() {
	global $wpdb, $user_ID;
	include ("adminmenu/autoresponder.php");
}
function cbaf_daftar() {
	global $wpdb, $user_ID;	
	include ("adminmenu/daftar.php");
}

function cbaf_komisi() {
	global $wpdb, $user_ID;
	include ("adminmenu/komisi.php");
}

function cbaf_mediapromo() {
	global $wpdb, $user_ID;
	include ("adminmenu/mediapromo.php");
}

function cbaf_konfemail() {
	global $wpdb, $user_ID;
	include ("adminmenu/konfemail.php");
}

function cbaf_sms() {
	global $wpdb, $user_ID;
	include ("adminmenu/sms.php");
}

function cbaf_menu() {
	global $wpdb, $user_ID;
	include ("adminmenu/membermenu.php");
}

function cbaf_produk() {
	global $wpdb, $user_ID;
	include ("adminmenu/produk.php");
}

function cbaf_subdomain() {
	global $wpdb, $user_ID;
	include ("adminmenu/subdomain.php");
}

function cbaf_panduan() {
	$url = 'https://www.youtube.com/embed/RE5IMUajyAQ';
	echo '<div class="wrap">
	<div class="row">
	<div class="col-sm-8">
		<div class="embed-responsive embed-responsive-16by9">
		<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/hDq6ieAkcFE" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" name="video" allowfullscreen></iframe>		
		</div>
		<div class="text-center mt-3">
		<a href="https://www.youtube.com/mentoringcafebisnis?sub_confirmation=1" target="_blank" class="btn btn-danger">Subscribe</a>
		<a href="'.plugins_url('wp-affiliasi/readme.txt').'" target="_blank" class="btn btn-success">Shortcode List</a>
		
		</div>
	</div>
	<div class="col-sm-4" style="height:100%; max-height:500px; overflow:scroll">
		<h3>Daftar Isi Panduan</h3>
		<ol>';
		$panduan = getData('https://cafebisnis.com/pwa.php',$_SERVER['HTTP_USER_AGENT']);
		$panduan = json_decode($panduan,true);
		if (is_array($panduan)) {
			foreach ($panduan as $panduan) {
				if (!isset($panduan['status'])) {
					echo '<li><a href="'.$panduan['url'].'" target="video">'.$panduan['judul'].'</a></li>';
				}
			}
		} else {
			echo 'Gagal menghubungi cafebisnis';
		}
	echo '
		</ol>
	</div>
	</div>
	</div>';

	
}

function cbaf_lisensi() {
	global $wpdb, $user_ID;
	include ("adminmenu/lisensi.php");
}

function cbaf_uninstall() {
	global $wpdb, $user_ID;
	include ("adminmenu/cb_uninstall.php");
}

function cbaf_home() {
	global $wpdb, $user_ID;
	$showtxt = '';
	include(plugin_dir_path(__DIR__).'/memberarea.php');
	echo '<div class="wrap">
	<h1 class="wp-heading-inline">Memberarea</h1>
	<hr class="wp-header-end">'.$showtxt.'</div>';
}

function cbaf_lapkomisi() {
	global $wpdb, $user_ID;
	$showtxt = '';
	include(plugin_dir_path(__DIR__).'/memberlaporan.php');
	echo '<div class="wrap">
	<h1 class="wp-heading-inline">Laporan Komisi</h1>
	<hr class="wp-header-end">'.$showtxt.'</div>';
}

function cbaf_klienlist() {
	global $wpdb, $user_ID;
	$showtxt = '';
	include(plugin_dir_path(__DIR__).'/memberkliendb.php');
	echo '<div class="wrap">'.$showtxt.'</div>';
}

function cbaf_profil() {
	global $wpdb, $user_ID;
	$showtxt = '';
	include(plugin_dir_path(__DIR__).'/memberprofil.php');
	echo '<div class="wrap">
	<h1 class="wp-heading-inline">Profil</h1>
	<hr class="wp-header-end">
	'.$showtxt.'</div>';
}

function cbaf_jaringan() {
	global $wpdb, $user_ID;
	$showtxt = '';
	include(plugin_dir_path(__DIR__).'/memberjaringan.php');
	echo '<div class="wrap"><h1 class="wp-heading-inline">Jaringan</h1>
	<hr class="wp-header-end">'.$showtxt.'</div>';
}

function cbaf_pesanan() {
	global $wpdb, $user_ID;
	$showtxt = '';
	include(plugin_dir_path(__DIR__).'/memberpesanan.php');
	echo '<div class="wrap"><h1 class="wp-heading-inline">Pesanan Anda</h1>
	<hr class="wp-header-end">'.$showtxt.'</div>';
}
?>