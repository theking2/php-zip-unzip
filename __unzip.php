<?php

if( 'go'=== ($_POST['action']??false)) {
$zip = new ZipArchive;
  
// Zip File Name
if ($zip->open($_POST['filename']) === TRUE) {
  
    // Unzip Path
    $zip->extractTo('./');
    $zip->close();
    echo 'Unzipped Process Successful!';
} else {
    echo 'Unzipped Process failed';
}
exit(0);
}?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restore archive</title>
</head>
<body>
    <h1>Restore archive</h1>
    <form method="post">
        <label><select name="filename">
            <?php
            foreach(glob('*.zip') as $filename) {
                printf('<option value="%1$s">%1$s', $filename);
            }
            ?>
        </select></label>
        <input type="submit" name="action" value="go">
    </form>
</body>
</html>
