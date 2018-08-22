<?php

function TextToImage_my(
  $text, 
  $separate_line_after_chars=40,  
  $font='./myfont.ttf', 
  $size=24,
  $rotate=0,
  $padding=2,
  $transparent=true, 
  $color=array('red'=>0,'grn'=>0,'blu'=>0), 
  $bg_color=array('red'=>255,'grn'=>255,'blu'=>255) 
){
    $amount_of_lines= ceil(strlen($text)/$separate_line_after_chars);
    $x=explode("\n", $text); $final='';
    foreach($x as $key=>$value){
        $returnes='';
        do{ $first_part=mb_substr($value, 0, $separate_line_after_chars, 'utf-8');
            $value= "\n".mb_substr($value, $separate_line_after_chars, null, 'utf-8');
            $returnes .=$first_part;
        }  while( mb_strlen($value,'utf-8')>$separate_line_after_chars);
        $final .= $returnes."\n";
    }
    $text=$final;
    Header("Content-type: image/png");
    $width=$height=$offset_x=$offset_y = 0;
    if(!is_file($font)) { file_put_contents($font,file_get_contents('https://github.com/edx/edx-certificates/raw/master/template_data/fonts/Arial%20Unicode.ttf')); }

    // get the font height.
    $bounds = ImageTTFBBox($size, $rotate, $font, "W");
    if ($rotate < 0)        {$font_height = abs($bounds[7]-$bounds[1]); } 
    elseif ($rotate > 0)    {$font_height = abs($bounds[1]-$bounds[7]); } 
    else { $font_height = abs($bounds[7]-$bounds[1]);}
    // determine bounding box.
    $bounds = ImageTTFBBox($size, $rotate, $font, $text);
    if ($rotate < 0){       $width = abs($bounds[4]-$bounds[0]);                    $height = abs($bounds[3]-$bounds[7]);
                            $offset_y = $font_height;                               $offset_x = 0;
    } 
    elseif ($rotate > 0) {  $width = abs($bounds[2]-$bounds[6]);                    $height = abs($bounds[1]-$bounds[5]);
                            $offset_y = abs($bounds[7]-$bounds[5])+$font_height;    $offset_x = abs($bounds[0]-$bounds[6]);
    } 
    else{                   $width = abs($bounds[4]-$bounds[6]);                    $height = abs($bounds[7]-$bounds[1]);
                            $offset_y = $font_height;                               $offset_x = 0;
    }

    $image = imagecreate($width+($padding*2)+1,$height+($padding*2)+1);

    $background = ImageColorAllocate($image, $bg_color['red'], $bg_color['grn'], $bg_color['blu']);
    $foreground = ImageColorAllocate($image, $color['red'], $color['grn'], $color['blu']);

    if ($transparent) ImageColorTransparent($image, $background);
    ImageInterlace($image, true);
  // render the image
    ImageTTFText($image, $size, $rotate, $offset_x+$padding, $offset_y+$padding, $foreground, $font, $text);
    imagealphablending($image, true);
    imagesavealpha($image, true);
  // output PNG object.
    imagePNG($image);
}

	//======helper function==========
	if(!function_exists('mb_substr_replace')){
	  function mb_substr_replace($string, $replacement, $start, $length = null, $encoding = "UTF-8") {
		if (extension_loaded('mbstring') === true){
			$string_length = (is_null($encoding) === true) ? mb_strlen($string) : mb_strlen($string, $encoding);
			if ($start < 0) { $start = max(0, $string_length + $start); }
			else if ($start > $string_length) {$start = $string_length; }
			if ($length < 0){ $length = max(0, $string_length - $start + $length);  }
			else if ((is_null($length) === true) || ($length > $string_length)) { $length = $string_length; }
			if (($start + $length) > $string_length){$length = $string_length - $start;} 
			if (is_null($encoding) === true) {  return mb_substr($string, 0, $start) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length); }
			return mb_substr($string, 0, $start, $encoding) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length, $encoding);
		}
		return (is_null($length) === true) ? substr_replace($string, $replacement, $start) : substr_replace($string, $replacement, $start, $length);
	  }
	}
