<?php
$wp_load = substr( dirname( __FILE__ ), 0, strpos( dirname( __FILE__ ), 'wp-content' ) ) . 'wp-load.php';
if ( ! empty( $wp_load ) && file_exists( $wp_load ) ) {
	require_once $wp_load;
} else {
	die('Could not load WordPress');
}
if ( current_user_can( 'manage_options' ) ) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Script-Content-Type" content="text/javascript">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Expires" content="0"> <!-- disable caching -->
<title>Direktori Browser</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script type="text/javascript">
function makeSelection(frm, id) {
if(!frm || !id)
return;
var elem = frm.elements[id];
if(!elem)
return;
var val = document.querySelector('input[name="nameSelection"]:checked').value;
opener.targetElement.value = val;
this.close();
}
</script>
</head>
<body>
<div class="container">
<p>Pilih Folder Tempat Meletakkan File2 yang akan didownload: </p>
<?php
$folder = $depan = '';
if (isset($_GET['go'])) {
	$direktori = stripcslashes($_GET['go']);
} else {
	$direktori = str_replace('folder.php','',__FILE__);
}

if (stristr($direktori,'/')) {
	$pecah = explode('/',$direktori);
	$depan = $sep = '/';	
} else {
	$pecah = explode('\\',$direktori);
	$sep = '\\';
}
echo '<p>Lokasi saat ini:<br/>';
foreach($pecah as $pecah) {
	if ($pecah) {
		$folder .= $pecah.$sep;
		echo '<a href="folder.php?go='.$depan.$folder.'">'.$pecah.'</a>'.$sep;
	}
}
echo "</p>";
$files = glob($direktori . "*");

echo '<form id="frm" name="frm" action="#">
<ul class="list-unstyled">';
foreach($files as $file) {
	if(is_dir($file)) {
		echo '<li><input type="radio" name="nameSelection" value="'.$file.'"/> <a href="folder.php?go='.$file.$sep.'">'.str_replace($direktori,'',$file).'</a></li>';
	}
}
?>
</ul>
<p><input type="button" class="btn btn-primary" value="Pilih Folder" onclick="makeSelection(this.form, 'nameSelection');"></p>
</form>
</div>
</body>
</html>
<?php } ?>