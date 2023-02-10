<?php
class HZip
{
  /**
   * Add files and sub-directories in a folder to zip file.
   * @param string $folder
   * @param ZipArchive $zipFile
   * @param int $exclusiveLength Number of text to be exclusived from the file path.
   */
  private static function folderToZip($folder, &$zipFile, $exclusiveLength, $convert_function)
  {

    $handle = opendir($folder);
    echo $folder . '<br>' . PHP_EOL;
    while (false !== $f = readdir($handle)) {
      if (($f !== '.')
        && ($f !== '..')
      ) {
        $filePath = "$folder/$f";
        // Remove prefix from file path before add to zip.
        $localPath = substr($filePath, $exclusiveLength);
        if (is_file($filePath)
          && ((pathinfo($filePath)['extension']??'') !== 'zip')
          && ($f !== basename(__FILE__))
        ) {
          $zipFile->addFile($filePath,$convert_function($localPath) );
        } elseif (is_dir($filePath)) {
          // Add sub-directory.
          $zipFile->addEmptyDir($localPath);
          self::folderToZip($filePath, $zipFile, $exclusiveLength, $convert_function);
        }
      }
    }
    closedir($handle);
  }

  /**
   * Zip a folder (include itself).
   * Usage:
   *   HZip::zipDir('/path/to/sourceDir', '/path/to/out.zip');
   *
   * @param string $sourcePath Path of directory to be zip.
   * @param string $outZipPath Path of output zip file.
   */
  public static function zipDir($sourcePath, $outZipPath, $convert_function)
  {
    $start = microtime(true);

    $pathInfo = pathInfo($sourcePath);
    $parentPath = $pathInfo['dirname'];
    $dirName = $pathInfo['basename'];

    $z = new ZipArchive();
    $z->open($outZipPath, ZIPARCHIVE::CREATE);
    $z->addEmptyDir($dirName);
    self::folderToZip($sourcePath, $z, strlen("$parentPath/"), $convert_function);
    $z->close();

    $end = microtime(true);
    printf('Done in %0.6f sec.</br>', $end - $start);
  }
}

if ("go" === ($_POST['action']?? false)) {
  $host = str_replace('.', '_', $_SERVER['HTTP_HOST']);
  $date = date('Ymd-His');
  if( $_POST['encode']??false )
    $convert_function = fn($localPath) => iconv("CP858//IGNORE", "UTF-8", $localPath);
  else
    $convert_function = fn($localPath) => $localPath;

  HZip::zipDir('./', "./archiv-$host-$date.zip", $convert_function);
  exit(0);
}
if ($_POST['action'] ?? true)
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Archive</title>
</head>

<body>
  <h1>Create Archive</h1>
  <form method="post">
    <label>Windows encode?<input type="checkbox" name="encode" id="encode"></label>
    <label><input type="submit" name="action" value="go"></label>
  </form>
</body>

</html>