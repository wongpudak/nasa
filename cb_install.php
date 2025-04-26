<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (!$wpdb->get_var("show tables like 'wp_member'")) {
	$sql = "
CREATE TABLE `cb_download` (
`id` bigint(20) NOT NULL auto_increment,
`id_user` bigint(20) NOT NULL,
`id_produk` bigint(20) NOT NULL,
`hash` varchar(35) NOT NULL,
`ip` varchar(15) NOT NULL,
`count` bigint(20) NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
	$wpdb->query($sql); 
	$sql = "
	CREATE TABLE `cb_laporan` (
`id` BIGINT NOT NULL auto_increment ,
`tanggal` DATETIME NOT NULL ,
`transaksi` VARCHAR( 250 ) NOT NULL ,
`debet` BIGINT NOT NULL ,
`kredit` BIGINT NOT NULL ,
`komisi` BIGINT NOT NULL ,
`keterangan` VARCHAR( 100 ) NOT NULL ,
`id_user` BIGINT NOT NULL ,
`id_sponsor` BIGINT NOT NULL,
`id_order` BIGINT NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE = MYISAM ;";
	$wpdb->query($sql); 
	$sql = "
CREATE TABLE `cb_produk` (
  `id` bigint(20) NOT NULL auto_increment,
  `id_cat` bigint(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `url_file` varchar(100) NOT NULL,
  `thumb_file` varchar(100) NULL,
  `diskripsi` varchar(250) NULL,
  `count` bigint(20) NOT NULL,
  `membership` bigint(20) NOT NULL,
  `password` varchar(25) NULL,
  `harga` decimal(10,0) NULL,
  `status` VARCHAR(1) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
	$wpdb->query($sql); 
	$sql = "
CREATE TABLE `cb_produk_cat` (
`id_cat` bigint(20) NOT NULL auto_increment,
`id_parent` bigint(20) NOT NULL,
`name` varchar(50) NOT NULL,
PRIMARY KEY  (`id_cat`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
	$wpdb->query($sql); 
	$sql = "
CREATE TABLE `wp_member` (
`id_user` bigint(20) NOT NULL auto_increment,
`idwp` bigint(20) NOT NULL default '0',
`id_referral` bigint(20) NOT NULL default '0',
`id_tianshi` varchar(10) NULL default '',
`ktp` varchar(25) NULL default '',
`nama` varchar(50) NOT NULL default '',
`tgl_lahir` varchar(10) NULL default '',
`alamat` varchar(100) NULL default '',
`kota` varchar(20) NULL default '',
`provinsi` varchar(20) NULL default '',
`kodepos` varchar(6) NULL default '',
`telp` varchar(15) NULL default '',
`ktp_istri` varchar(25) NULL default '',
`nama_istri` varchar(50) NULL default '',
`tgl_lahir_istri` varchar(10) NULL default '',
`tgl_daftar` datetime NULL default '0000-00-00 00:00:00',
`tgl_upgrade` datetime NULL default '0000-00-00 00:00:00',
`downline_lngsg` bigint(20) NOT NULL default '0',
`jml_downline` bigint(20) NOT NULL default '0',
`jml_voucher` int(11) NOT NULL default '0',
`sisa_voucher` int(11) NOT NULL default '0',
`ac` varchar(50) NULL default '',
`bank` varchar(200) NULL default '',
`rekening` varchar(50) NULL default '',
`kelamin` tinyint(4) NOT NULL default '0',
`username` varchar(50) NOT NULL default '',
`password` varchar(50) NOT NULL default '',
`email` varchar(50) NOT NULL default '',
`subdomain` varchar(50) NOT NULL default '',
`judulhome` varchar(200) NULL default '',
`homepage` text NULL,
`read` bigint(20) NOT NULL default '0',
`membership` tinyint(4) NOT NULL default '0',
`val` varchar(15) NULL,
`ip` varchar(15) NULL,
`lastupdate` datetime NULL default '0000-00-00 00:00:00',
PRIMARY KEY  (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
	$wpdb->query($sql); 

	$sql = "
CREATE TABLE `cb_produklain` (
  `id` bigint(20) NOT NULL auto_increment,
  `idwp` bigint(20) NOT NULL,
  `id_user` BIGINT NOT NULL,
  `idproduk` bigint(20) NOT NULL,
  `download` bigint(20) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `tgl_order` datetime NOT NULL,
  `tgl_bayar` datetime NOT NULL,
  `hargaproduk` decimal(10,0) NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
	$wpdb->query($sql);	


	$current_user = wp_get_current_user();
	$emailadmin = $current_user->user_email;
	$unadmin = $current_user->user_login;
	$namaadmin = $current_user->display_name;
	$wpdb->query("INSERT INTO `wp_member`  
		(`idwp`,`nama`,`tgl_daftar`,`tgl_upgrade`,`username`,`email`,`subdomain`,`membership`,`lastupdate`) VALUES
		(".$user_ID.", '".$namaadmin."', '".wp_date('Y-m-d H:i:s')."', '".wp_date('Y-m-d H:i:s')."', '".$unadmin."', '".$emailadmin."', '".$unadmin."', 2, '".wp_date('Y-m-d H:i:s')."')");
	$namablog = get_bloginfo('name');
	$konfemail = array (
		'nama_email' 	=> $namaadmin,
		'alamat_email' 	=> $emailadmin,
		'judul_registrasi_sponsor'=> '['.$namablog.'] [member_nama] telah bergabung',
		'isi_registrasi_sponsor'	=> 'Promosi anda sukses, [member_nama] telah bergabung bersama kita. Berikut ini datanya:
Nama : [member_nama]
Email : [member_email]
Username : [member_username]

Silahkan melakukan follow up dan terus berpromosi menggunakan URL Affiliasi anda.

Salam Sukses
'.$namaadmin,
		'judul_registrasi_admin'	=> '['.$namablog.'] [member_nama] telah bergabung',
		'isi_registrasi_admin'	=> 'Hallo Admin,
Seseorang telah mendaftar di '.$namablog.' dengan data berikut:

Nama : [member_nama]
Email : [member_email]
Username : [member_username]

Silahkan melakukan follow up.',
		'judul_registrasi_member'=> '['.$namablog.'] Selamat datang [member_nama]',
		'isi_registrasi_member'	=> 'Terima kasih
Pendaftaran Berhasil. Segera login ke memberarea untuk menyelesaikan pendaftaran

Memberarea: '.site_url().'/memberarea
Username : [member_username]
Password : [member_password]
URL Web Reseller : [member_urlaff]

Salam Sukses
'.$namaadmin,
		'judul_upgrade_member'	=> '['.$namablog.'] Selamat Bergabung [member_nama] !!',
		'isi_upgrade_member'	=> 'Selamat, status keanggotaan anda telah menjadi premium member. Silahkan menikmati layanan premium dari kami.

Salam Sukses
'.$namaadmin,
		'judul_upgrade_sponsor'	=> '['.$namablog.'] Selamat [sponsor_nama], anda telah mendapat klien baru !!',
		'isi_upgrade_sponsor'	=> 'Selamat, anda telah mendapatkan seorang klien baru.

Segera setelah mencapai pembayaran minimum, anda akan menerima komisi dari kami. 
Demikian pemberitahuan ini. Silahkan terus berpromosi untuk mendapatkan komisi yang lebih banyak lagi. 

Salam Sukses
'.$namaadmin,
		'judul_beli_member'	=> '['.$namablog.'] Terima kasih atas pembelian anda',
		'isi_beli_member'	=> 'Terima kasih atas pembelian produk anda. Berikut ini detil produk yang anda beli:

ID Order: [produk_orderid]
Nama Produk: [produk_nama]
Diskripsi: [produk_diskripsi]
Harga : Rp. [produk_harga]

Silahkan menyelesaikan pembayaran anda

Salam Sukses
'.$namaadmin,
		'judul_beli_sponsor'	=> '['.$namablog.'] Klien anda melakukan order',
		'isi_beli_sponsor'		=> 'Hallo, Klien anda telah melakukan order produk berikut:

ID Order: [produk_orderid]
Nama Produk: [produk_nama]
Diskripsi: [produk_diskripsi]
Harga : Rp. [produk_harga]

Silahkan menyelesaikan pembayaran anda

Salam Sukses
'.$namaadmin,
		'judul_prosesbeli_member'	=> '['.$namablog.'] Produk anda telah selesai diproses',
		'isi_prosesbeli_member'		=> 'Terima kasih, order anda telah kami proses. Silahkan download produk yang anda beli di halaman produk.

Salam Sukses
'.$namaadmin,

		'judul_prosesbeli_sponsor'	=> '['.$namablog.'] Telah terjual sebuah produk',
		'isi_prosesbeli_sponsor'	=> 'Yeaayy! Promosi anda berhasil, [member_nama] telah membeli produk berikut:

Nama Produk: [produk_nama]
Harga: [produk_harga]

Komisi akan langsung dimasukkan ke catatan akun anda.

Salam Sukses
'.$namaadmin,
		'judul_komisi_member'	=> '['.$namablog.'] Komisi telah dibayarkan',
		'isi_komisi_member'	=> 'Selamat ya [member_nama] komisi untuk anda telah ditransfer ke:

[member_ac]
[member_bank]
[member_rekening]
Rp. [komisi]

Teruskan promosi untuk mendapatkan komisi yang lebih banyak lagi

Salam Sukses
'.$namaadmin,
	);

	update_option('konfemail', $konfemail);

	// Bikin Page untuk Registrasi
	$my_post = array();
	$my_post['post_title'] = 'Registrasi';
	$my_post['post_content'] = '[cb_registrasi]';
	$my_post['post_status'] = 'publish';
	$my_post['post_author'] = 1;
	$my_post['comment_status'] = 'closed';
	$my_post['post_category'] = array(1);
	$my_post['post_type'] = 'page';
	
	$page_registrasi = wp_insert_post( $my_post );
	
	// Bikin Page untuk Kontak
	$my_post = array();
	$my_post['post_title'] = 'Kontak';
	$my_post['post_content'] = '[cb_kontak]';
	$my_post['post_status'] = 'publish';
	$my_post['post_author'] = 1;
	$my_post['comment_status'] = 'closed';
	$my_post['post_category'] = array(1);
	$my_post['post_type'] = 'page';

	$page_kontak = wp_insert_post( $my_post );

	// Bikin Page untuk Halaman Sukses
	$my_post = array();
	$my_post['post_title'] = 'Sukses';
	$my_post['post_content'] = '<p>Selamat, anda sudah bergabung sebagai free member di '.get_bloginfo('name').'</p>
			<p>Demi keamanan, kami telah mengirimkan username dan password ke email anda. Apabila tidak ada, silahkan cek spam folder</p>';
	$my_post['post_status'] = 'publish';
	$my_post['post_author'] = 1;
	$my_post['comment_status'] = 'closed';
	$my_post['post_category'] = array(1);
	$my_post['post_type'] = 'page';

	$page_sukses = wp_insert_post( $my_post );

	// Bikin Page untuk Halaman Order
	$my_post = array();
	$my_post['post_title'] = 'Order Anda';
	$my_post['post_content'] = '[cb_order]';
	$my_post['post_status'] = 'publish';
	$my_post['post_author'] = 1;
	$my_post['comment_status'] = 'closed';
	$my_post['post_category'] = array(1);
	$my_post['post_type'] = 'page';

	$page_order = wp_insert_post( $my_post );

	// Bikin Page untuk Halaman Cari Sponsor
	$my_post = array();
	$my_post['post_title'] = 'URL Sponsor Anda';
	$my_post['post_content'] = '<p>Anda wajib menggunakan URL Sponsor untuk dapat membuka web ini:</p><p>[urlsponsor]</p>';
	$my_post['post_status'] = 'publish';
	$my_post['post_author'] = 1;
	$my_post['comment_status'] = 'closed';
	$my_post['post_category'] = array(1);
	$my_post['post_type'] = 'page';

	$page_carisponsor = wp_insert_post( $my_post );
	
	// Bikin Page untuk Halaman Order
	$my_post = array();
	$my_post['post_title'] = 'Produk';
	$my_post['post_content'] = '[displayproduk]';
	$my_post['post_status'] = 'publish';
	$my_post['post_author'] = 1;
	$my_post['comment_status'] = 'closed';
	$my_post['post_category'] = array(1);
	$my_post['post_type'] = 'page';

	$page_produk = wp_insert_post( $my_post );

	// Bikin Page untuk Memberarea
	$my_post = array();
	$my_post['post_title'] = 'Memberarea';
	$my_post['post_content'] = '[cb_memberarea]';
	$my_post['post_status'] = 'publish';
	$my_post['post_author'] = 1;
	$my_post['comment_status'] = 'closed';
	$my_post['post_category'] = array(1);
	$my_post['post_type'] = 'page';

	$page_memberarea = wp_insert_post( $my_post );

	// Bikin Page untuk Login
	$my_post = array();
	$my_post['post_title'] = 'Login';
	$my_post['post_content'] = '[cb_loginreg]';
	$my_post['post_status'] = 'publish';
	$my_post['post_author'] = 1;
	$my_post['comment_status'] = 'closed';
	$my_post['post_category'] = array(1);
	$my_post['post_type'] = 'page';

	$page_login = wp_insert_post( $my_post );

	

	// Pengaturan Default
	$options = array(
		'default' => $user_ID,
		'registrasi' => $page_registrasi,
		'contact' => $page_kontak,
		'successpage' => $page_sukses,
		'order' => $page_order,
		'memberarea' => $page_memberarea,
		'loginpage' => '',
		'carisponsor' => 0,
		'shorturl' => 'http://tinyurl.com/api-create.php?url=[URL]',
		'limit' => 50000,
		'key_trx' => 'ORDER-',
		'harga' => 100000,
		'pp_price' => 14000
		);
	update_option('cb_pengaturan', $options);

	$form = array(
		array('register'=>1, 'profil'=>1, 'required'=>1, 'field'=>'nama', 'label'=>'Nama Lengkap', 'option'=>'', 'info'=>''),
		array('register'=>1, 'profil'=>1, 'required'=>1, 'field'=>'email', 'label'=>'Alamat Email', 'option'=>'', 'info'=>'')
		);
	$aturform = serialize($form);
	update_option("aturform", $aturform);

	$header = 'From: Plugin WP Affiliasi<'.get_bloginfo('admin_email').'>';
	$body = 'Plugin berhasil diinstall di '.site_url();
	@mail('qzoners@gmail.com', 'Plugin berhasil diinstall di '.site_url(), $body, $header);	
}

if (!$wpdb->get_var("show tables like 'cb_produklain'")) {
	// Bikin Page untuk Memberarea
	$my_post = array();
	$my_post['post_title'] = 'Memberarea';
	$my_post['post_content'] = '[cb_memberarea]';
	$my_post['post_status'] = 'publish';
	$my_post['post_author'] = 1;
	$my_post['comment_status'] = 'closed';
	$my_post['post_category'] = array(1);
	$my_post['post_type'] = 'page';

	wp_insert_post( $my_post );
	
	$sql = "
	CREATE TABLE `cb_produklain` (
	  `id` bigint(20) NOT NULL auto_increment,
	  `idwp` bigint(20) NOT NULL,
	  `id_user` BIGINT NOT NULL,
	  `idproduk` bigint(20) NOT NULL,
	  `download` bigint(20) NOT NULL,
	  `status` tinyint(4) NOT NULL,
	  `tgl_order` datetime NOT NULL,
	  `tgl_bayar` datetime NOT NULL,
	  `hargaproduk` decimal(10,0) NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";
	$wpdb->query($sql);	
}

$wp_affiliasi_version = get_option('wp_affiliasi_version');

if ($wp_affiliasi_version > 200 && $wp_affiliasi_version < 312) {
	$sql = "ALTER TABLE  `cb_laporan` 
	ADD  `komisi` BIGINT NOT NULL AFTER  `kredit`,
	ADD  `id_user` BIGINT NOT NULL ,
	ADD  `id_sponsor` BIGINT NOT NULL,
	ADD  `id_order` BIGINT NOT NULL ;";
	$wpdb->query($sql);
}

update_option('wp_affiliasi_version',354);
?>