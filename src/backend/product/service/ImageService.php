<?php

namespace product;

class ImageService {

    private static string $uploadDir = '../pictures/uploads/';
    private static string $thumbnailPath = '../pictures/thumbnails/';

    public function saveImage() {
        $fileName = time() ."_" . basename($_FILES['image']['name']);

        $uploadFileDir = $this::$uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFileDir)) {

            $thumbnailName = $this::$thumbnailPath . 'thumbnail_' . $fileName;
            if ($this::createThumbnail($uploadFileDir, $thumbnailName)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Image uploaded successfully',
                    'image' => $uploadFileDir,
                    'thumbnail' => $thumbnailName
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to convert image thumbnail']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
        }
    }

    private function createThumbnail($imagePath, $thumbnailName): bool
    {
        $resizedImage = $this->resizeImage($imagePath, 200, 200);
        return imagejpeg($resizedImage, $thumbnailName);
    }

    private function resizeImage($file, $w, $h, $crop=FALSE) {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }
}
