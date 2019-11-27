<?php
// Usage:   TextToImage("Hiii");

function TextToImage(
  $text="hello world", 
  $newline_after_letters=40,  
  $font='./myfont.ttf', 
  $size=24,
  $rotate=0,
  $padding=2,
  $transparent=true, 
  $color=array('red'=>0,'grn'=>0,'blu'=>0), 
  $bg_color=array('red'=>255,'grn'=>255,'blu'=>255) 
){
	//other version: pastebin(dot).com/XVVUyWGD
	$amount_of_lines= ceil(strlen($text)/$newline_after_letters)+substr_count($text, '\n')+1;
	$all_lines=explode("\n", $text); $text=""; $amount_of_lines = count($all_lines);
	foreach($all_lines as $key=>$value){ 
		while( mb_strlen($value,'utf-8')>$newline_after_letters){	
			$text_final .= mb_substr($value, 0, $newline_after_letters, 'utf-8')."\n";
			$value = mb_substr($value, $newline_after_letters, null, 'utf-8');
		}  
		$text .= mb_substr($value, 0, $newline_after_letters, 'utf-8') . ( $amount_of_lines-1 == $key ? "" : "\n");
	}
	
	//
    Header("Content-type: image/png");
    $width=$height=$offset_x=$offset_y = 0;
    if(!is_file($font)) { file_put_contents($font,file_get_contents('https://github.com/potyt/fonts/raw/master/macfonts/Arial%20Unicode%20MS/Arial%20Unicode.ttf')); }

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
