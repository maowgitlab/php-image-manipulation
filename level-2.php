<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && isset($_POST['action'])) {
    $file = $_FILES['file']['tmp_name'];
    $action = $_POST['action'];
    $targetDir = 'uploads/';
    $targetFile = $targetDir . basename($_FILES['file']['name']);
    move_uploaded_file($file, $targetFile);

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

    if ($action == 'resize' && isset($_POST['width']) && isset($_POST['height'])) {
        $width = intval($_POST['width']);
        $height = intval($_POST['height']);
        $resizedImage = resizeImage($targetFile, $width, $height);
        $outputFile = $targetDir . 'resized_' . basename($targetFile);
        imagejpeg($resizedImage, $outputFile);
        imagedestroy($resizedImage);
    } elseif ($action == 'crop' && isset($_POST['crop_width']) && isset($_POST['crop_height'])) {
        $cropWidth = intval($_POST['crop_width']);
        $cropHeight = intval($_POST['crop_height']);
        $croppedImage = cropImage($targetFile, $cropWidth, $cropHeight);
        $outputFile = $targetDir . 'cropped_' . basename($targetFile);
        imagejpeg($croppedImage, $outputFile);
        imagedestroy($croppedImage);
    }

    header('Location: level-2.php?image=' . urlencode($outputFile));
}

if (isset($_POST['clear'])) {
    if (isset($_GET['image']) && file_exists($_GET['image'])) {
        unlink($_GET['image']);
    }
    header('Location: level-2.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Manipulation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white p-5 rounded-md shadow-md">
        <h1 class="text-2xl font-bold mb-4">Image Manipulation Tool</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="file">Select image:</label>
                <input type="file" name="file" id="file" class="w-full px-3 py-2 border rounded-md">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="action">Select action:</label>
                <select name="action" id="action" class="w-full px-3 py-2 border rounded-md">
                    <option value="resize">Resize</option>
                    <option value="crop">Crop</option>
                </select>
            </div>
            <div class="mb-4" id="resize-options">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="width">Width:</label>
                <input type="text" name="width" id="width" class="w-full px-3 py-2 border rounded-md">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="height">Height:</label>
                <input type="text" name="height" id="height" class="w-full px-3 py-2 border rounded-md">
            </div>
            <div class="mb-4" id="crop-options" style="display: none;">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="crop_width">Crop Width:</label>
                <input type="text" name="crop_width" id="crop_width" class="w-full px-3 py-2 border rounded-md">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="crop_height">Crop Height:</label>
                <input type="text" name="crop_height" id="crop_height" class="w-full px-3 py-2 border rounded-md">
            </div>
            <div class="mb-4">
                <button type="submit" class="w-full bg-blue-500 text-white px-3 py-2 rounded-md">Upload and Manipulate</button>
            </div>
        </form>
        <?php if (isset($_GET['image'])): ?>
            <div class="mt-4">
                <h2 class="text-xl font-bold mb-2">Result:</h2>
                <img src="<?php echo htmlspecialchars($_GET['image']); ?>" alt="Manipulated Image" class="rounded-md">
                <form action="" method="post" class="mt-4">
                    <button type="submit" name="clear" class="w-full bg-red-500 text-white px-3 py-2 rounded-md">Clear</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <script>
        const actionSelect = document.getElementById('action');
        const resizeOptions = document.getElementById('resize-options');
        const cropOptions = document.getElementById('crop-options');

        actionSelect.addEventListener('change', function() {
            if (this.value === 'resize') {
                resizeOptions.style.display = 'block';
                cropOptions.style.display = 'none';
            } else if (this.value === 'crop') {
                resizeOptions.style.display = 'none';
                cropOptions.style.display = 'block';
            }
        });
    </script>
</body>
</html>

