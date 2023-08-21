// =========== https://github.com/ttodua/useful-php-scripts ================ 
// =========================================================================
//     zip_folder(__DIR__.'/path/to/input/folder',   __DIR__.'/path/to/output_zip_file.zip') ;
// =========================================================================

function zip_folder ($input_folder, $output_zip_file) {
    $zipClass = new ZipArchive();
    $input_folder = realpath($input_folder);
    $addDirDo = static function($input_folder, $name) use ($zipClass, &$addDirDo ) {
        $name .= '/';
        $input_folder .= '/';
        // Read all Files in Dir
        $dir = opendir ($input_folder);
        while ($item = readdir($dir))    {
            if ($item == '.' || $item == '..') continue;
            $itemPath = $input_folder . $item;
            if (filetype($itemPath) == 'dir') {
                $zipClass->addEmptyDir($name . $item);
                $addDirDo($input_folder . $item, $name . $item);
            } else {
                $zipClass->addFile($itemPath, $name . $item);
            }
        }
    };
    if($input_folder !== false && $output_zip_file !== false)
    {
        $res = $zipClass->open($output_zip_file, \ZipArchive::CREATE);
        if($res === true)   {
            $zipClass->addEmptyDir(basename($input_folder));
            $addDirDo($input_folder, basename($input_folder));
            $zipClass->close(); 
        }
        else   { exit ('Could not create a zip archive, migth be write permissions or other reason.'); }
    }
}
