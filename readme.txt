=== WP Affiliate ===
Contributors: Lutvi Avandi
Tags: affiliate, membership, reseller, woocommerce
Requires at least: 6.0
Tested up to: 6.2.2
Stable tag: v3.5.8

Mengubah WordPress biasa menjadi sebuah web affiliasi dengan sistem multi level yang bekerja secara otomatis. 

== Description ==

WP Affiliasi akan menambah sebuah table database member di WordPress anda. WordPress anda akan berubah menjadi sebuah web affiliasi. Anda bisa menggunakannya untuk menjual produk dengan system affiliasi. Untuk memaksimalkan kerja plugin ini, anda butuh themes dengan 1 kolom yang berisi sales letter di halaman depannya. Pada update versi 1.1 keatas, anda bisa juga membuat blog eksklusif. Artikel berharga anda hanya bisa dibaca kalau member login. Dan untuk donatur blog, bisa anda beri hak akses spesial untuk artikel spesial anda.

== Installation ==

- Extract file yang sudah didownload dari member area
- Upload folder /wp-affiliasi/ ke folder wp-content/plugins 
- Bisa juga upload file zip-nya melalui dashboard menu Plugins - Add New - Upload
- Aktifkan plugin melalui menu Plugins - Installed
- Masuk ke menu Lisensi lalu isi username dan password cafebisnis anda untuk mengaktifkan lisensi.
- Panduan lebih lengkap dapat dibaca di dashboard pada menu Pengaturan - Panduan

DAFTAR SHORTCODE WP-AFFILIASI:

- Untuk membuat page-page khusus wp-affiliasi, silahkan gunakan kode2 berikut:
	- [cb_registrasi]				: untuk membuat halaman registrasi
	- [cb_kontak]					: untuk membuat halaman kontak admin / sponsor
	- [cb_memberarea]				: untuk membuat halaman memberarea
	- [cb_order]					: untuk membuat halaman order
	- [cb_loginreg]					: untuk membuat halaman login dan registrasi
	- [pagemember data="home"] 		: untuk membuat halaman home memberarea
	- [pagemember data="profil"] 	: untuk membuat halaman profil memberarea
	- [pagemember data="laporan"] 	: untuk membuat halaman laporan keuangan memberarea
	- [pagemember data="promosi"] 	: untuk membuat halaman promosi memberarea
	- [pagemember data="jaringan"] 	: untuk membuat halaman jaringan memberarea
	- [pagemember data="download"] 	: untuk membuat halaman download memberarea
	- [pagemember data="klien"] 	: untuk membuat halaman daftar klien memberarea
	- [pagemember]					: halaman khusus yang hanya bisa diakses oleh member
	- [displayproduk]				: untuk menampilkan produk2 yg dijual terpisah
	- [cb_leaderboard]				: untuk menampilkan leaderboard
	- [memberlist]					: untuk menampilkan daftar member terbaru
	- [urlsponsor]					: Menampilkan form pemilihan sponsor
	- [cb_jmlmember data="STATUS"]	: Menampilkan statistik jumlah member berdasarkan status (free, premium, novalid, total)
	- [khususfree]...[/khususfree]	: konten yg ada diantara 2 shortcode ini hanya bisa dilihat oleh 
									  free member. Premium maupun pengunjung tidak bisa
	- [khususpremium]...[/khususpremium]	: konten yg ada diantara 2 shortcode ini hanya bisa dilihat oleh 
									  premium member. Free member maupun pengunjung tidak bisa
	- [produkpage id="1"]...[/produkpage]	: Konten khusus pembeli produk dg ID 1. Ganti ID sesuai ID produknya
	- [premium]...[/premium]		: untuk menyembunyikan konten khusus premium member. Selain premium akan muncul keterangan
	- [freemember]...[/freemember]	: untuk menyembunyikan konten khusus free member dan premium. Pengunjung hanya muncul 	
									  keterangan
	- {member}...{/member}			: konten yg ada diantara 2 shortcode ini hanya bisa dilihat oleh 
									  member. Pengunjung tidak bisa.
	- {visitor}...{/visitor}		: konten yg ada diantara 2 shortcode ini hanya bisa dilihat oleh 
									  pengunjung. Member tidak bisa.							  
	- {premium}...{/premium}		: konten yg ada diantara 2 shortcode ini hanya bisa dilihat oleh 
									  premium member. Free member maupun pengunjung tidak bisa.
	- {freemember}...{/freemember}	: konten yg ada diantara 2 shortcode ini hanya bisa dilihat oleh 
									  free member. Premium member maupun pengunjung tidak bisa.
	
		
- Menampilkan data pemilik web replika:	[sponsor_FIELD]
- Menampilkan data member: [member_FIELD]
- Menampilkan data sponsor member yg sedang login: [mysponsor_FIELD]
- Pastikan kodenya ada di dalam class="datasponsor"
	Daftar FIELD :
	- idwp
	- id_referral
	- id_tianshi
	- ktp
	- nama
	- tgl_lahir
	- alamat
	- kota
	- provinsi
	- kodepos
	- telp
	- ktp_istri
	- nama_istri
	- tgl_lahir_istri
	- tgl_daftar
	- tgl_upgrade	
	- jml_voucher
	- sisa_voucher
	- ac
	- bank
	- rekening
	- kelamin
	- username
	- password
	- email
	- subdomain
	- read 
	- foto
	- urlaff
	- urlpendek
	- whatsapp
	- status
	- custompic_profil
	- jmldownline (jumlah seluruh downline langsung)
	- jmlinvalid (jumlah downline yang belum pernah login)
	- jmlfree (jumlah downline yg masih free member)
	- jmlpremium (jumlah downline yg sudah premium)
	- omset (total harga produk yang berhasil dipromosikan)
	- totalorder (jumlah produk yang berhasil dipromosikan)
	- totalkomisi (total komisi yang sudah dicairkan)
	- komisicair (jumlah komisi yang sudah dicairkan)
	- komisitertahan (jumlah komisi yg belum dicairkan)

- Kode Khusus:
	- Gunakan angka 8888888888 (angka 8 sebanyak 10x) untuk menampilkan nomor whatsapp sponsor
	- Gunakan angka 9999999999 (angka 9 sebanyak 10x) untuk menampilkan nomor whatsapp member
	- Gunakan angka 7777777777 (angka 7 sebanyak 10x) untuk menampilkan nomor whatsapp sponsor dari member yg login
	- Gunakan https://cafebisnis.com/fotosponsor.jpg untuk menampilkan foto sponsor
	- Gunakan https://cafebisnis.com/fotomember.jpg untuk menampilkan foto member

DAFTAR FILTER HOOK (masih ujicoba)

cbaff_registrasi_from 	: untuk menambah field di form
cbaff_registrasi_sukses	: setelah member sukses melakukan pendaftaran
cbaff_beli_sukses		: setelah member sukses melakukan pembelian produklain
cbaff_aktifasi_sukses	: setelah aktifasi order selesai
cb_notif_pesan			: untuk memasukkan format konten pesan notif

DAFTAR ACTION HOOK (masih ujicoba)

cb_notif			: menjalankan notifikasi