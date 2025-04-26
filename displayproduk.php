<?php 
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
$options = get_option('cb_pengaturan');
if (isset($_GET['kategori']) && $_GET['kategori'] != '') {
	$kategoriaktif[$_GET['kategori']] = ' selected';
}
$showtxt = '
<div class="menuproduk">
<div class="leftmenuproduk">
<form action="" method="get" id="search" class="formsearchproduk">
<input type="text" name="cari" class="searchproduk"/>
<a onclick="document.getElementById(\'search\').submit();" class="submitsearch">cari</a>
</form>
</div>
<div class="rightmenuproduk">
<form action="" method="get" id="kategori" class="formsearchproduk">
<select name="kategori" class="searchproduk">
	<option value="all"'.($kategoriaktif['all']??='').'>Semua Produk</option>
	<option value="lain"'.($kategoriaktif['lain']??='').'>Produk Lain</option>
	<option value="free"'.($kategoriaktif['free']??='').'>Bonus Free Member</option>
	<option value="premium"'.($kategoriaktif['premium']??='').'>Bonus Premium Member</option>';
	$procat = $wpdb->get_results("SELECT * FROM `cb_produk_cat`");
	if (count($procat) > 0) {
		foreach ($procat as $procat) {
			$showtxt .= '<option value="'.$procat->id_cat.'"'.($kategoriaktif[$procat->id_cat]??='').'>'.$procat->name.'</option>';
		}
	}
$showtxt .= '
</select>
<a onclick="document.getElementById(\'kategori\').submit();" class="submitsearch">go</a>
</form>
</div>
<div style="clear:both"></div>
</div>
';

if (isset($user_ID) && $user_ID > 0) {
	$udahbeli = $wpdb->get_results("SELECT `idproduk` FROM `cb_produklain` WHERE `status`=1 AND `idwp`=".$user_ID);
	if (count($udahbeli) > 0) {
		foreach ($udahbeli as $udahbeli) {
			$produkku[$udahbeli->idproduk] = 1;
		}
	}
	if (defined('CB_MEMBER')) {
		$member = unserialize(CB_MEMBER);
		$custommember = unserialize($member->homepage);
	}
}

$perpage = 10;
$where = '';
if (isset($_GET['list']) && is_numeric($_GET['list'])) {
	$list = ($_GET['list']-1)*$perpage;
	$page = $_GET['list'];
} else {
	$list = 0;
	$page = 0;
}

if (isset($_GET['kategori'])) {	
	switch ($_GET['kategori']) {
		case 'lain':
			$where = "`membership`=3";
			break;
		case 'free':
			$where = "`membership`=1";
			break;
		case 'premium':
			$where = "`membership`=2";
			break;	
		case 'premium':
			$where = "";
			break;				
		default:
			if (is_numeric($_GET['kategori'])) {
				$where = "`id_cat`=".$_GET['kategori'];
			} 
			break;
	}
} 

if (isset($_GET['cari']) && $_GET['cari'] != '') {
	$where = " (`nama` LIKE '%".$wpdb->_real_escape($_GET['cari'])."%' OR `diskripsi` LIKE '%".$wpdb->_real_escape($_GET['cari'])."%')";
} 

if (isset($where) && $where != '') { $where = " WHERE ".$where; }

$produk = $wpdb->get_results("SELECT * FROM `cb_produk` ".$where." ORDER BY `membership` DESC LIMIT ".$list.",".$perpage);

foreach ($produk as $produk) {		
	if ($produk->thumb_file != '') {
		$thumb = $produk->thumb_file;
	} else {
		$thumb = plugins_url('wp-affiliasi/img/nopic.jpg');
	}
	if ($produk->status == 1 || (isset($produkku[$produk->id]) && $produkku[$produk->id] == 1)) {
		$showtxt .= '
	<div id="produkblock">
		<div id="produkbox">
			<div id="produkpic" style="background:url(\''.$thumb.'\') no-repeat;background-size:cover;"></div>
			<div id="produktitle">'.$produk->nama.'</div>
			<div id="produkdesc">'.$produk->diskripsi;
			if ($produk->membership == 3) { 
				$showtxt .= '<br/><small>Harga: '.($options['matauang']??='Rp. ').number_format($produk->harga).'</small>'; 
			} elseif ($produk->membership == 2) {
				$showtxt .= '<br/><small><em>Bonus Premium Member</em></small>'; 
			} else {
				$showtxt .= '<br/><small><em>Bonus Free Member</em></small>'; 
			}

			$showtxt .= '</div>
		</div>
		<div id="produkactionbox">
			<a href="'.site_url().'/?page_id='.$produk->count.'" id="produkinfo">INFO</a>';			
			if ($produk->membership == 3 && isset($produkku[$produk->id]) && $produkku[$produk->id] == 1) {
				# Produk Lain dan Member sudah beli
				$tombol = 'download';
			} elseif ($produk->membership == 2 && isset($member->membership) && $member->membership == 2) {
				# Produk Premium dan Member sudah Premium
				$tombol = 'download';
			} elseif ($produk->membership == 1 && isset($member->membership) && $member->membership >= 1) {
				# Produk Free dan Member sudah Login
				$tombol = 'download';
			} else {
				if ($produk->membership == 3) {
					$tombol = '<a href="'.site_url().'/?page_id='.$options['order'].'&orderproduk='.$produk->id.'" id="produkorder">BELI</a>';
				} else {
					$tombol = '<a href="'.site_url().'/?page_id='.$options['order'].'" id="produkorder">UPGRADE</a>';
				} 
			}

			if ($tombol == 'download') {
				$showtxt .= '<a href="'.esc_url( add_query_arg( 'download_id', $produk->id, home_url( '/' ) ) ).'"  id="produkorder">DOWNLOAD</a>';
			} else {
				$showtxt .= $tombol;
			}
		$showtxt .= '
			<div style="clear:both;"></div>
		</div>
	</div>';
	}
}

$showtxt .= '<div style="clear:both;"></div>';

if (isset($_GET)) {
	$link = '';
	foreach ($_GET as $key => $value) {
		if ($key != 'list') {
			$link .= '&'.$key.'='.$value;
		}
	}
}

$jml = $wpdb->get_var("SELECT count(*) FROM `cb_produk` ".$where); 

$jmlpage = floor(($jml/$perpage)+1);
if ($jmlpage > 1) {
	$showtxt .= '<div id="pronavbar">';
	if ($jmlpage > 10) {
		if ($page <= 7){
			for ($i=1;$i<=10;$i++) {
				$showtxt .= '<a class="pronavi" href="?list='.$i.$link.'">'.$i.'</a> ';
			}
			$showtxt .= '... <a class="pronavi" href="?list='.$jmlpage.$link.'">'.$jmlpage.'</a> ';
		} elseif ($page > 5 && $page < ($jmlpage-7)) {
			$showtxt .= '<a class="pronavi" href="?list=1">1</a> ... ';
			for ($i=($page-5);$i<=($page+5);$i++) {
				if ($i == $page) {
				$showtxt .= '<a class="proaktif">'.$i.'</a> ';
				} else {
				$showtxt .= '<a class="pronavi" href="?list='.$i.$link.'">'.$i.'</a> ';
				}
			}
			$showtxt .= '... <a class="pronavi" href="?list='.$jmlpage.$link.'">'.$jmlpage.'</a> ';
		} else {
			$showtxt .= '<a class="navi" href=?list=1">1</a> ... ';
			for ($i=($jmlpage-10);$i<=$jmlpage;$i++) {
				if ($i == $page) {
				$showtxt .= '<a class="proaktif">'.$i.'</a> ';
				} else {
				$showtxt .= '<a class="pronavi" href="?list='.$i.$link.'">'.$i.'</a> ';
				}
			}
		}
	} else {
		for ($i=1;$i<=$jmlpage;$i++) {
			if ($i == $page) {
			$showtxt .= '<a class="proaktif">'.$i.'</a> ';
			} else {
			$showtxt .= '<a class="pronavi" href="?list='.$i.$link.'">'.$i.'</a> ';
			}
		}
	}
	$showtxt .= '</div>';
}