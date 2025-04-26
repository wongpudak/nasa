<?php
// Update ke versi 337
$cekolom = $wpdb->query("SHOW COLUMNS FROM `cb_produklain` LIKE 'id_user'");
if ($cekolom == 0) {
	$wpdb->query("ALTER TABLE `cb_produklain` 
		ADD `id_user` BIGINT NOT NULL AFTER `idwp`,
		ADD `hargaproduk` decimal(10,0) NULL
		");
}

$cekolom = $wpdb->query("SHOW COLUMNS FROM `wp_member` LIKE 'lastupdate'");
if ($cekolom == 0) {
	$wpdb->query("ALTER TABLE `wp_member` 
		ADD `lastupdate` DATETIME NOT NULL
		");
}

$smsoption = get_option('smsoption');
if (isset($smsoption['smscamit'])) {
	$newsms = array(
		'smsapi' => $smsoption['smsapi'],
		'sms_registrasi_member' => $smsoption['smscamit'],
		'sms_upgrade_member' => $smsoption['smsupmember'],
		'sms_beli_member' => 'Terima kasih. Untuk menyelesaikan pembelian [produk_nama], Silahkan trf [produk_hargaunik] ke rekening kami',
		'sms_prosesbeli_member' => 'Order [produk_nama] telah kami proses. Silahkan download di memberarea',
		'sms_komisi_member' => 'Terima kasih telah promosi produk kami. Kami telah mentransfer sebesar [komisi] ke rekening anda.',
		'sms_registrasi_sponsor' => $smsoption['smssponsor'],
		'sms_upgrade_sponsor' => $smsoption['smsupsponsor'],
		'sms_beli_sponsor' => 'Klien anda [member_nama] telah memesan [produk_nama]. Silahkan difollow up untuk menyelesaikan pembayaran',
		'sms_prosesbeli_sponsor' => '[member_nama] telah menyelesaikan pembayaran. Komisi telah tercatat di akun anda.'
	);
	update_option('smsoption', $newsms);
}
// Ubah option email menjadi 1 option utk menghemat tabel option

$konfemail = array(
	'nama_email' => get_option('nama_email'),
	'alamat_email' => get_option('alamat_email'),
	'judul_registrasi_member' => get_option('judul_email_daftar'),
	'judul_upgrade_member' => get_option('judul_email_aktif'),
	'judul_beli_member' => '',
	'judul_prosesbeli_member' => '',
	'judul_komisi_member' => get_option('judul_email_bayar'),
	'judul_registrasi_sponsor' => get_option('judul_notifsponsor'),
	'judul_upgrade_sponsor' => get_option('judul_email_sale'),
	'judul_beli_sponsor' => '',
	'judul_prosesbeli_sponsor' => '',
	'judul_registrasi_admin' => get_option('judul_notifadmin'),
	'isi_registrasi_member' => get_option('isi_email_daftar'),
	'isi_upgrade_member' => get_option('isi_email_aktif'),
	'isi_beli_member' => '',
	'isi_prosesbeli_member' => '',
	'isi_komisi_member' => get_option('isi_email_bayar'),
	'isi_registrasi_sponsor' => get_option('isi_notifsponsor'),
	'isi_upgrade_sponsor' => get_option('isi_email_sale'),
	'isi_beli_sponsor' => '',
	'isi_prosesbeli_sponsor' => '',
	'isi_registrasi_admin' => get_option('isi_notifadmin')
);

$konfemail = str_replace('{{namamember}}', '[member_nama]', $konfemail);
$konfemail = str_replace('{{emailmember}}', '[member_email]', $konfemail);
$konfemail = str_replace('{{username}}', '[member_username]', $konfemail);
$konfemail = str_replace('{{password}}', '[member_password]', $konfemail);
$konfemail = str_replace('{{urlreseller}}', '[member_urlaff]', $konfemail);
$konfemail = str_replace('{{telpmember}}', '[member_telp]', $konfemail);
$konfemail = str_replace('{{kotamember}}', '[member_kota]', $konfemail);
$konfemail = str_replace('{{namabank}}', '[member_bank]', $konfemail);
$konfemail = str_replace('{{rekening}}', '[member_rekening]', $konfemail);
$konfemail = str_replace('{{atasnama}}', '[member_ac]', $konfemail);
$konfemail = str_replace('{{komisi}}', '[komisi]', $konfemail);
$konfemail = str_replace('{{namasponsor}}', '[sponsor_nama]', $konfemail);
$konfemail = str_replace('{{hpsponsor}}', '[sponsor_telp]', $konfemail);
$konfemail = str_replace('{{wasponsor}}', '[sponsor_whatsapp]', $konfemail);
$konfemail = str_replace('{{emailsponsor}}', '[sponsor_email]', $konfemail);

update_option('konfemail', $konfemail);

// Hapus option lama

$mailoptionlist = array('nama_email','alamat_email','judul_email_daftar','isi_email_daftar','judul_email_aktif','isi_email_aktif','judul_email_sale','judul_email_beli','isi_email_beli','judul_email_prolain','isi_email_prolain','judul_email_salelain','isi_email_salelain','judul_email_bayar','isi_email_bayar','notifadmin','judul_notifadmin','isi_notifadmin','notifsponsor','judul_notifsponsor','isi_notifsponsor');
foreach ($mailoptionlist as $maillist) {
	delete_option($maillist);
}

// Ubah option Autoresponder
$options = get_option('cb_pengaturan');

$auto['free']['action'] = $options['action1'];
for ($i=0; $i < 10 ; $i++) { 
	$auto['free'][$i]['field'] = $options['field'][$i];
	$auto['free'][$i]['value'] = $options['value'][$i];
}

$auto['premium']['action'] = $options['action2'];
for ($i=10; $i < 20 ; $i++) { 
	$auto['premium'][$i-10]['field'] = $options['field'][$i];
	$auto['premium'][$i-10]['value'] = $options['value'][$i];
}

$auto = json_encode($auto);
$auto = str_replace('{{nama}}', '[member_nama]', $auto);
$auto = str_replace('{{email}}', '[member_email]', $auto);
$auto = str_replace('{{username}}', '[member_username]', $auto);
$auto = str_replace('{{password}}', '[member_password]', $auto);
$auto = str_replace('{{affiliasi}}', '[member_subdomain]', $auto);
$auto = str_replace('{{telp}}', '[member_telp]', $auto);
$auto = str_replace('{{kota}}', '[member_kota]', $auto);
$auto = str_replace('{{validasi}}', '[validasi]', $auto);
$auto = str_replace('{{namasponsor}}', '[sponsor_nama]', $auto);
$auto = str_replace('{{hpsponsor}}', '[sponsor_telp]', $auto);
$auto = str_replace('{{emailsponsor}}', '[sponsor_email]', $auto);
$auto = json_decode($auto,TRUE);

update_option('konfautoresponder',$auto);


// Update versi WP Affiliasi
update_option('wp_affiliasi_version',340);
?>