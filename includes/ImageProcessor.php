<?php
class ImageProcessor {
    private $image;
    private $width;
    private $height;
    private $type;
    private $allowedTypes = [
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_GIF
    ];

    public function load($source) {
        if (is_string($source)) {
            if (!file_exists($source)) {
                throw new Exception('Image file does not exist');
            }
            $imageInfo = getimagesize($source);
            $this->type = $imageInfo[2];
            
            if (!in_array($this->type, $this->allowedTypes)) {
                throw new Exception('Invalid image type');
            }

            switch ($this->type) {
                case IMAGETYPE_JPEG: $this->image = imagecreatefromjpeg($source); break;
                case IMAGETYPE_PNG: $this->image = imagecreatefrompng($source); break;
                case IMAGETYPE_GIF: $this->image = imagecreatefromgif($source); break;
            }
        } elseif (is_resource($source) || $source instanceof GdImage) {
            $this->image = $source;
        } else {
            throw new Exception('Invalid image source');
        }

        if (!$this->image) {
            throw new Exception('Failed to load image');
        }

        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
        
        return $this;
    }

    public function resize($width = null, $height = null, $maintain_ratio = true) {
        if ($width === null && $height === null) {
            return $this;
        }

        if ($maintain_ratio) {
            if ($width === null) {
                $width = $this->width * ($height / $this->height);
            }
            if ($height === null) {
                $height = $this->height * ($width / $this->width);
            }

            $ratio = $this->width / $this->height;
            $targetRatio = $width / $height;

            if ($ratio > $targetRatio) {
                $newWidth = $width;
                $newHeight = $width / $ratio;
            } else {
                $newHeight = $height;
                $newWidth = $height * $ratio;
            }
        } else {
            $newWidth = $width ?? $this->width;
            $newHeight = $height ?? $this->height;
        }

        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency
        if ($this->type === IMAGETYPE_PNG || $this->type === IMAGETYPE_GIF) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled(
            $newImage, $this->image,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $this->width, $this->height
        );

        $this->image = $newImage;
        $this->width = $newWidth;
        $this->height = $newHeight;

        return $this;
    }

    public function crop($width, $height, $x = 'center', $y = 'center') {
        if ($x === 'center') {
            $x = ($this->width - $width) / 2;
        }
        if ($y === 'center') {
            $y = ($this->height - $height) / 2;
        }

        $newImage = imagecreatetruecolor($width, $height);
        
        if ($this->type === IMAGETYPE_PNG || $this->type === IMAGETYPE_GIF) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $width, $height, $transparent);
        }

        imagecopy($newImage, $this->image, 0, 0, $x, $y, $width, $height);
        
        $this->image = $newImage;
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    public function watermark($watermarkPath, $position = 'bottom-right', $opacity = 70) {
        $watermark = $this->load($watermarkPath)->getImage();
        $watermarkWidth = imagesx($watermark);
        $watermarkHeight = imagesy($watermark);

        switch ($position) {
            case 'top-left':
                $x = 10;
                $y = 10;
                break;
            case 'top-right':
                $x = $this->width - $watermarkWidth - 10;
                $y = 10;
                break;
            case 'bottom-left':
                $x = 10;
                $y = $this->height - $watermarkHeight - 10;
                break;
            case 'center':
                $x = ($this->width - $watermarkWidth) / 2;
                $y = ($this->height - $watermarkHeight) / 2;
                break;
            default: // bottom-right
                $x = $this->width - $watermarkWidth - 10;
                $y = $this->height - $watermarkHeight - 10;
        }

        $this->imagecopymerge_alpha($this->image, $watermark, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight, $opacity);
        
        return $this;
    }

    private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $opacity) {
        $opacity /= 100;
        for ($x = 0; $x < $src_w; $x++) {
            for ($y = 0; $y < $src_h; $y++) {
                $color = imagecolorsforindex($src_im, imagecolorat($src_im, $src_x + $x, $src_y + $y));
                $alpha = 127 - (127 - $color['alpha']) * $opacity;
                $color = imagecolorallocatealpha($dst_im, $color['red'], $color['green'], $color['blue'], $alpha);
                imagesetpixel($dst_im, $dst_x + $x, $dst_y + $y, $color);
            }
        }
    }

    public function save($path, $quality = 90) {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($this->image, $path, $quality);
                break;
            case 'png':
                $quality = floor((100 - $quality) / 10);
                imagepng($this->image, $path, $quality);
                break;
            case 'gif':
                imagegif($this->image, $path);
                break;
            default:
                throw new Exception('Unsupported image type');
        }
        
        return $this;
    }

    public function getImage() {
        return $this->image;
    }

    public function getDimensions() {
        return [
            'width' => $this->width,
            'height' => $this->height
        ];
    }

    public function __destruct() {
        if ($this->image) {
            imagedestroy($this->image);
        }
    }
}