<?php
namespace App\Http\Logics;
use Intervention\Image\Facades\Image;

class ImageLogic {
    function resizeBase64Image($base64Image)
    {
        $base64String = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $base64String = str_replace(' ', '+', $base64String);

        $imageData = base64_decode($base64String);

        $image = imagecreatefromstring($imageData);
        if ($image === false) {
            return false;
        }

        $resizedImage = imagescale($image, 200, 200);

        ob_start();
        imagepng($resizedImage);
        $resizedImageData = ob_get_contents();
        ob_end_clean();

        $resizedBase64Image = base64_encode($resizedImageData);

        $newBase64Image = 'data:image/png;base64,' . $resizedBase64Image;

        imagedestroy($image);
        imagedestroy($resizedImage);

        return $newBase64Image;
    }
}
?>
