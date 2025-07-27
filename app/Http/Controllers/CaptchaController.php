<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Response;

class CaptchaController extends Controller
{
    public function generate()
{
    // Código aleatorio de 6 caracteres
    $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));

    // Guardar en sesión
    Session::put('captcha_code', $code);

    // Crear imagen
    $width = 180;
    $height = 60;
    $image = imagecreatetruecolor($width, $height);

    // Colores
    $background = imagecolorallocate($image, 255, 255, 255); // blanco

    // Fondo blanco
    imagefilledrectangle($image, 0, 0, $width, $height, $background);

   // Ruta de la fuente Roboto (ajústala si está en otra carpeta)
        $fontPath = storage_path('fonts/Roboto_Condensed-Bold.ttf');

        
    // Líneas curvas de colores
    for ($i = 0; $i < 4; $i++) {
        $curveColor = imagecolorallocate(
            $image, rand(50, 200), rand(50, 200), rand(50, 200)
        );
        imagearc(
            $image,
            rand(-$width, $width * 2), rand(-$height, $height * 2),
            rand($width / 2, $width * 2), rand($height / 2, $height * 2),
            rand(0, 360), rand(0, 360),
            $curveColor
        );
    }

    // Añadir líneas horizontales de colores atravesando el medio
for ($i = 0; $i < 3; $i++) {
    $lineColor = imagecolorallocate(
        $image, rand(50, 180), rand(50, 180), rand(50, 180)
    );
    $y = rand($height * 0.4, $height * 0.6); // cerca del medio
    imageline($image, 0, $y, $width, $y, $lineColor);
}


    // Puntos de colores
    for ($i = 0; $i < 200; $i++) {
        $dotColor = imagecolorallocate(
            $image, rand(50, 200), rand(50, 200), rand(50, 200)
        );
        imagesetpixel($image, rand(0, $width), rand(0, $height), $dotColor);
    }

    // Letras de colores
    $x = 10;
    $gap = ($width - 20) / strlen($code);
    for ($i = 0; $i < strlen($code); $i++) {
        $letterColor = imagecolorallocate(
            $image, rand(0, 120), rand(0, 120), rand(0, 120)
        );
        imagettftext(
            $image,
            rand(22, 28),
            rand(-15, 15),
            $x + ($i * $gap),
            rand($height * 0.7, $height * 0.9),
            $letterColor,
            $fontPath,
            $code[$i]
        );
    }
    // Devolver imagen como respuesta
    ob_start();
    imagepng($image);
    $imageData = ob_get_clean();
    imagedestroy($image);

logger('Captcha generado: ' . $code);
logger('Session ID (captcha): ' . session()->getId());



   return response($imageData)
    ->header('Content-Type', 'image/png')
    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
    ->header('Pragma', 'no-cache')
    ->header('Expires', '0');

}


}
