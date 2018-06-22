<?php

/*
    demo:
        $src = dirname(__FILE__).'/1.pic_hd.jpg';
        $new = dirname(__FILE__).'/1.pic_hd_thumb.jpg';
        $s = WesPhoto::thumb($src, $new, array(600, 600));

        $new = dirname(__FILE__).'/1.pic_hd_cut.jpg';
        WesPhoto::cut($src, $new, array(300, 300));
*/
        
class WesPhoto {

    //等比缩放图片
    //$src_file原图地址, $new_file新图地址, $thumbWH
    public static function thumb($src_file, $new_file, $thumbWH = null ){

        if ( null==$thumbWH || !is_array($thumbWH) ) {
    		$thumbWH = array("0"=> 600,"1"=> 600);
    	}

    	if ( !filesize($src_file) || !is_readable($src_file)) {
    		return false;
    	}



    	$imginfo = @getimagesize($src_file);

    	if( $imginfo[0] <= $thumbWH[0] && $imginfo[1] <= $thumbWH[1]) {
    		@copy($src_file,$new_file);
    		return true;
    	}

    	$newWidth = (int)(min($imginfo[0],$thumbWH[0]));
    	$newHeight = (int)($imginfo[1] * $newWidth / $imginfo[0]);

    	if ( $newHeight > $thumbWH[1] ) {
    		$newHeight = (int)(min($imginfo[1],$thumbWH[1]));
    		$newWidth  = (int)($imginfo[0] * $newHeight / $imginfo[1]);
    	}

    	$type = $imginfo[2];
    	$supported_types = array();
    	if (!extension_loaded("gd")) return false;
    	if (function_exists("imagegif")) $supported_types[] = 1;
    	if (function_exists("imagejpeg"))$supported_types[] = 2;
    	if (function_exists("imagepng")) $supported_types[] = 3;

        $imageCreateFunction = (function_exists("imagecreatetruecolor"))? "imagecreatetruecolor" : "imagecreate";

    	if (in_array($type, $supported_types) ){
    		switch ($type){
    			case 1 :
    				if (!function_exists("imagecreatefromgif")) return false;
    				$im = imagecreatefromgif($src_file);
                    $im = self::rotate($im, $src_file);
    				$new_im = imagecreate($newWidth, $newHeight);
    				if(function_exists("ImageCopyResampled"))
    				ImageCopyResampled($new_im, $im, 0, 0, 0, 0, $newWidth, $newHeight,$imginfo[0],$imginfo[1]);
    				else
    				ImageCopyResized($new_im, $im, 0, 0, 0, 0, $newWidth, $newHeight, $imginfo[0],$imginfo[1]);
    				imagegif($new_im, $new_file);
    				imagedestroy($im);
    				imagedestroy($new_im);
    				break;
    			case 2 :
    				$im = imagecreatefromjpeg($src_file);
                    $im = self::rotate($im, $src_file);
    				$new_im = $imageCreateFunction($newWidth, $newHeight);
    				if(function_exists("ImageCopyResampled"))
    				ImageCopyResampled($new_im, $im, 0, 0, 0, 0, $newWidth, $newHeight,$imginfo[0],$imginfo[1]);
    				else
    				ImageCopyResized($new_im, $im, 0, 0, 0, 0, $newWidth, $newHeight, $imginfo[0],$imginfo[1]);
    				imagejpeg($new_im, $new_file,90);
    				imagedestroy($im);
    				imagedestroy($new_im);
    				break;
    			case 3 :
    				$im = imagecreatefrompng($src_file);
                    $im = self::rotate($im, $src_file);
    				$new_im = $imageCreateFunction($newWidth, $newHeight);
    				if(function_exists("ImageCopyResampled"))
    				ImageCopyResampled($new_im, $im, 0, 0, 0, 0, $newWidth, $newHeight,$imginfo[0],$imginfo[1]);
    				else
    				ImageCopyResized($new_im, $im, 0, 0, 0, 0, $newWidth, $newHeight, $imginfo[0],$imginfo[1]);
    				imagepng($new_im, $new_file);
    				imagedestroy($im);
    				imagedestroy($new_im);
    				break;
    		}
    		return true;
    	}
    	return false;
    }

    //等比绽放后，截取中间图像
    //$o_photo原图,$d_photo新图,$cutWH 0=>宽 1=>高
    public static function cut($o_photo, $d_photo, $cutWH=null){

        if ( null==$cutWH || !is_array($cutWH) ) {
            $width = $height = 300;
        } else {
            $width = $cutWH[0];
            $height = $cutWH[1];
        }

        if ( $o_photo ) {
            $ImageType = @getimagesize($o_photo);

            switch ( @$ImageType[2] ) {
            case 1:
                $temp_img = imagecreatefromgif($o_photo);
                break;

            case 2:
                $temp_img = imagecreatefromjpeg($o_photo);
                break;

            case 3:
                $temp_img = imagecreatefrompng($o_photo);
                break;
            }
        }

        $temp_img = self::rotate($temp_img, $o_photo);


        $o_width  = imagesx($temp_img);
        $o_height = imagesy($temp_img);


        if($width>$o_width || $height>$o_height){

            $newwidth=$o_width;
            $newheight=$o_height;

            if($o_width>$width){
                $newwidth=$width;
                $newheight=$o_height*$width/$o_width;
            }

            if($newheight>$height){
                $newwidth=$newwidth*$height/$newheight;
                $newheight=$height;
            }

            $new_img = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($new_img, $temp_img, 0, 0, 0, 0, $newwidth, $newheight, $o_width, $o_height);
            imagejpeg($new_img , $d_photo, 90);
            imagedestroy($new_img);
            return true;
        }else{

            if($o_height*$width/$o_width>$height){
                $newwidth=$width;
                $newheight=$o_height*$width/$o_width;
                $x=0;
                $y=($newheight-$height)/2;
            }else{
                $newwidth=$o_width*$height/$o_height;
                $newheight=$height;
                $x=($newwidth-$width)/2;
                $y=0;
            }

            $new_img = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($new_img, $temp_img, 0, 0, 0, 0, $newwidth, $newheight, $o_width, $o_height);
            imagejpeg($new_img , $d_photo, 90);
            imagedestroy($new_img);

            $temp_img = imagecreatefromjpeg($d_photo);
            $o_width  = imagesx($temp_img);
            $o_height = imagesy($temp_img);

            $new_imgx = imagecreatetruecolor($width,$height);
            imagecopyresampled($new_imgx,$temp_img,0,0,$x,$y,$width,$height,$width,$height);
            imagejpeg($new_imgx , $d_photo, 90);
            imagedestroy($new_imgx);
            return true;
        }
        return false;
    }


    //处理图片旋转 解决ios图片上传旋转问题
    public static function rotate($image, $src_file) {
        $exif = exif_read_data($src_file);
        file_put_contents("/tmp/upload.log", date('Ymd -- ').var_export($exif, true)."\n\n", FILE_APPEND);
        switch($exif['Orientation']) {
            case 8:
                $image = imagerotate($image, 90, 0);
                break;
            case 3:
                $image = imagerotate($image, 180, 0);
                break;
            case 6:
                $image = imagerotate($image, -90, 0);
                break;
        }
        return $image;
    }
        

}
