<?php
  
$zip = new ZipArchive;
  
// Zip File Name
if ($zip->open('archiv-20220901-154057.zip') === TRUE) {
  
    // Unzip Path
    $zip->extractTo('./');
    $zip->close();
    echo 'Unzipped Process Successful!';
} else {
    echo 'Unzipped Process failed';
}