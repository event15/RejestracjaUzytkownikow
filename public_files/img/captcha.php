<?php
include '../../lib/functions.php';

// należy utworzyć lub kontynuować sesję i zapisać ciąg znaków CAPTCHA
// w $_SESSION, by był dostępny w ramach innych wywołań
if (!isset($_SESSION))
{
    session_start();
    header('Cache-control: private');
}

// utworzenie obrazka o wymiarach 65x20 pikseli
$width = 65;
$height = 20;
$image = imagecreate(65, 20);

// wypełnienie obrazka kolorem tła
$bg_color = imagecolorallocate($image, 0x33, 0x66, 0xFF);
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// pobranie losowego tekstu
$text = random_text(5);

// ustalenie współrzędnych x i y do wyśrodkowania tekstu
$font = 5;
$x = imagesx($image) / 2 - strlen($text) * imagefontwidth($font) / 2;
$y = imagesy($image) / 2 - imagefontheight($font) / 2;

// wypisanie tekstu na obrazku
$fg_color = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
imagestring($image, $font, $x, $y, $text, $fg_color);

// zapisanie ciągu znaków CAPTCHA do późniejszego porównania
$_SESSION['captcha'] = $text;

// zwrócenie obrazka
header('Content-type: image/png');
imagepng($image);

imagedestroy($image);