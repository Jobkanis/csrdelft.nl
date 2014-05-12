<?php

require_once 'configuratie.include.php';
require_once 'fotoalbum.class.php';

if (!LoginLid::mag('P_LOGGED_IN')) {
	header('location: ' . CSR_ROOT);
	exit;
}

set_time_limit(0);

//Album maken
$pad = $_GET['album'];

$mapnaam = explode('/', $pad);
array_pop($mapnaam);
$mapnaam = array_pop($mapnaam);

$fotoalbum = new FotoAlbum($pad, $mapnaam);

$fotos = $fotoalbum->getFotos();

//Headers
header('Content-type: application/x-tar');
header('Content-Disposition: attachment; filename="' . $mapnaam . '.tar"');

//tar-command maken
$cmd = "tar cC " . escapeshellarg($fotoalbum->locatie);
foreach ($fotos as $foto) {
	$cmd .= ' ' . escapeshellarg($foto->bestandsnaam);
}

//teh magic
$fh = popen($cmd, 'r');
while (!feof($fh)) {
	print fread($fh, 8192);
}
pclose($fh);
?>
