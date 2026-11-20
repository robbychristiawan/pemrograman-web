<?php
$file = 'C:/xampp/htdocs/pemro_web/admin_web/images/logo/logoTEM.png';
$info = getimagesize($file);
echo "Image info:\n";
print_r($info);

$im = imagecreatefrompng($file);
$w = imagesx($im);
$h = imagesy($im);
echo "Size: {$w}x{$h}\n";

// Check corners for background color
$c00 = imagecolorat($im, 0, 0);
$colors = imagecolorsforindex($im, $c00);
echo "Corner color (0,0): red={$colors['red']}, green={$colors['green']}, blue={$colors['blue']}, alpha={$colors['alpha']}\n";

unlink(__FILE__);
?>