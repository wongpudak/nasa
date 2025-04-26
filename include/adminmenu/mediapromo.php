<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } ?>
<?php
if (is_admin()) :
	if (isset($_POST['url']) && is_array($_POST['url'])) {
		$i = 0;
		foreach ($_POST['url'] as $url) {
			if ($url != '' && $url != 'http://') {
			$banner[$i]['url'] = $url;
			$banner[$i]['lebar'] = $_POST['lebar'][$i];
			$banner[$i]['tinggi'] = $_POST['tinggi'][$i];
			$i++;
			}
		}
		// Simpan data
		update_option("banner", $banner);
		echo '<div class="notice notice-success is-dismissible"><p>Banner Telah Diperbarui</p></div>';
	}
?>

<div class="wrap">
	<script type="text/javascript">
	function addInput(divName){
	  var newdiv = document.createElement('div');
	  newdiv.innerHTML = "<p>URL Banner : <input type=\"text\" name=\"url[]\" size=\"30\"/> Lebar (px): <input type=\"text\" name=\"lebar[]\" size=\"10\"/> Tinggi (px): <input type=\"text\" name=\"tinggi[]\" size=\"10\"/></p>";
	  document.getElementById(divName).appendChild(newdiv);
	  counter++;
	}
	</script>
	<h2>Pengaturan Banner Promosi</h2>
	<form action="" method="post">
	<div id="dynamicInput">
	<?php 
	;
	if ($databanner = get_option('banner')) {
		foreach ($databanner as $banner) {
			echo '<p>URL Banner : <input type="text" name="url[]" value="'.$banner['url'].'" size="30"/> 
			Lebar (px):<input type="text" name="lebar[]" value="'.$banner['lebar'].'" size="10"/>
			Tinggi (px):<input type="text" name="tinggi[]" value="'.$banner['tinggi'].'" size="10"/></p>';	
		}
	} else {
		echo '<p>URL Banner : <input type="text" name="url[]" size="30"/> 
			Lebar (px): <input type="text" name="lebar[]" size="10"/>
			Tinggi (px): <input type="text" name="tinggi[]" size="10"/></p>';	
	} 
	?>
	</div>
	<input type="button" class="button button-primary" value="Tambah Banner" onClick="addInput('dynamicInput');">
	<input type="submit" class="button button-primary" value="Update Banner"/>
	</form>
</div>

<?php endif;?>