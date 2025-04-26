<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } ?>
<?php
if (isset($_GET['status']) && $_GET['status'] == 'cancel') {
	echo 'Pembayaran dibatalkan';
} else {
	if (isset($options['pp_sand']) && $options['pp_sand']) { 
		$pp = 'https://www.sandbox.paypal.com/cgi-bin/webscr'; 
	} else {
		$pp = 'https://www.paypal.com/cgi-bin/webscr';
	}
?>
<form method="post" name="paypal_form" action="<?php echo $pp;?>">
<!-- PayPal Configuration -->
<input type="hidden" name="business" value="<?php echo $options['pp_email'];?>">
<input type="hidden" name="cmd" value="_xclick">
<?php if (!empty($cekorder) && $cekorder->idproduk == 0) { ?>
<input type="hidden" name="return" value="<?php echo site_url().'/?page_id='.get_the_ID().'&page=order&idproduk=premium';?>">
<?php } else { ?>
<input type="hidden" name="return" value="<?php echo site_url().'/?page_id='.get_the_ID().'&page=download&id='.$cekorder->idproduk;?>">
<?php } ?>
<input type="hidden" name="cancel_return" value="<?php echo site_url().'/?page_id='.get_the_ID().'&page=order&order='.$idorder.'&bank=paypal&status=cancel';?>">
<input type="hidden" name="notify_url" value="<?php echo site_url().'/wp-content/plugins/wp-affiliasi/pp-aktifasi.php';?>">
<input type="hidden" name="rm" value="2">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="bn" value="toolkit-php">
<input type="hidden" name="cbt" value="Klik Disini untuk Melanjutkan >>">

<!-- Payment Page Information -->
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="no_note" value="0">
<input type="hidden" name="cn" value="Komentar">
<input type="hidden" name="cs" value="">

<!-- Product Information -->
<input type="hidden" name="item_name" value="<?php echo $trx_id;?>">
<input type="hidden" name="amount" value="<?php echo $harga;?>">
<input type="hidden" name="quantity" value="1">
<input type="hidden" name="item_number" value="<?php echo $idorder;?>">
<input type="hidden" name="undefined_quantity" value="">

</form>
<script type="text/javascript">
	document.paypal_form.submit();
</script>
<?php } ?>