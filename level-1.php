<?php
function resizeImage($file, $width, $height) {
    list($originalWidth, $originalHeight) = getimagesize($file);
    $sourceImage = imagecreatefromjpeg($file);

    $resizedImage = imagecreatetruecolor($width, $height);

    imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

    return $resizedImage;
}

function cropImage($file, $cropWidth, $cropHeight) {
    list($originalWidth, $originalHeight) = getimagesize($file);
    $sourceImage = imagecreatefromjpeg($file);

    $startX = ($originalWidth - $cropWidth) / 2;
    $startY = ($originalHeight - $cropHeight) / 2;

    $croppedImage = imagecreatetruecolor($cropWidth, $cropHeight);

    imagecopyresampled($croppedImage, $sourceImage, 0, 0, $startX, $startY, $cropWidth, $cropHeight, $cropWidth, $cropHeight);

    return $croppedImage;
}

$file = 'TODO';

$newWidth = 800;
$newHeight = 600;

$cropWidth = 400;
$cropHeight = 300;

$resizedImage = resizeImage($file, $newWidth, $newHeight);
imagejpeg($resizedImage, 'resized_image.jpg');

$croppedImage = cropImage($file, $cropWidth, $cropHeight);
imagejpeg($croppedImage, 'cropped_image.jpg');

imagedestroy($resizedImage);
imagedestroy($croppedImage);
?>
