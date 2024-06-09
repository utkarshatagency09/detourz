<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2022, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaToolImage extends Controller {
    /**
     * Generate profile image by letter
     */
	public function profile(): void {
        $width  = $this->request->get['width']??50;
        $height = $this->request->get['height']??$width;
        $letter = $this->request->get['letter']??'?';
        $fontsize = 5;

        // Background
        $avatar = imagecreatetruecolor($width, $height);
        $bg_rgb = [rand(0, 255), rand(0, 255), rand(0, 255)];
        $bg_color = imagecolorallocate($avatar, $bg_rgb[0], $bg_rgb[1], $bg_rgb[2]);
        imagefill($avatar, 0, 0, $bg_color);

        // Text
        $yiq = (($bg_rgb[0] * 299) + ($bg_rgb[1]* 587) + ($bg_rgb[2] * 114)) / 1000;
        
        if ($yiq >= 150) {
            $text_color = imagecolorallocate($avatar, 0, 0, 0);
        } else {
            $text_color = imagecolorallocate($avatar, 255, 255, 255);
        }

        $posX = ($width - (imagefontwidth($fontsize) * strlen($letter))) * 0.5;
        $posY = ($height - imagefontheight($fontsize)) * 0.5;
        imagestring($avatar, $fontsize, $posX, $posY, strtoupper($letter), $text_color);
        
        // display image
        header('Content-type: image/png');
		imagepng($avatar);
		imagedestroy($avatar);
		exit();
	}

    /**
     * Generate placeholder image
     */
	public function placeholder(): void {
        $width  = $this->request->get['width']??200;
        $height = $this->request->get['height']??$width;
        $text = "$width x $height";
        $fontsize = 5;

        // Background
        $placeholder = imagecreatetruecolor($width, $height);
        $bg_rgb = [rand(0, 255), rand(0, 255), rand(0, 255)];
        $bg_color = imagecolorallocate($placeholder, $bg_rgb[0], $bg_rgb[1], $bg_rgb[2]);
        imagefill($placeholder, 0, 0, $bg_color);

        // Text
        $yiq = (($bg_rgb[0] * 299) + ($bg_rgb[1]* 587) + ($bg_rgb[2] * 114)) / 1000;
        
        if ($yiq >= 150) {
            $text_color = imagecolorallocate($placeholder, 0, 0, 0);
        } else {
            $text_color = imagecolorallocate($placeholder, 255, 255, 255);
        }

        $posX = ($width - (imagefontwidth($fontsize) * strlen($text))) * 0.5;
        $posY = ($height - imagefontheight($fontsize)) * 0.5;
        imagestring($placeholder, $fontsize, $posX, $posY, $text, $text_color);
        
        // display image
        header('Content-type: image/jpeg');
		imagejpeg($placeholder);
		imagedestroy($placeholder);
		exit();
	}
}
