<?php
function chmod_r($path, $filemode) {
    if (!is_dir($path)) {
        return chmod($path, $filemode);
    }
    
    $dh = opendir($path);
    while (($file = readdir($dh)) !== false) {
        if($file != '.' && $file != '..') {
            $fullpath = $path . '/' . $file;
            if(is_link($fullpath)) {
                return false;
            } elseif(!is_dir($fullpath)) {
                if (!chmod($fullpath, $filemode)) {
                    return false;
                }
            } elseif(!chmod_r($fullpath, $filemode)) {
                return false;
            }
        }
    }
    closedir($dh);
    return chmod($path, $filemode);
}

// Set permissions
chmod_r('.', 0644); // Files
chmod_r('.', 0755); // Directories
