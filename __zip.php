<?php
class MakeZip
{
  private ZipArchive $zipArchive;
  private int $startPathLength; // chars to remove from the start for the stored entity

  public function __construct(
    string $zipArchivename,
    public readonly string $startPath,
    public readonly mixed $convert_function,
  )
  {
    $this->zipArchive = new \ZipArchive;
    $this->zipArchive->open($zipArchivename, ZipArchive::CREATE);
    $this->startPathLength = strlen($this-> startPath);

    $this-> zipDir($startPath);
  }
  public function __destruct()
  {
    $this-> zipArchive-> close();
  }

  /**
   * Add files and sub-directories in a folder to zip file.
   * @param string $folder
   * @param ZipArchive $zipFile
   * @param int $exclusiveLength Number of text to be exclusived from the file path.
   */
  private function zipDir($folder)
  {
    echo $folder . '<br>' . PHP_EOL;
    foreach (new \DirectoryIterator($folder) as $f) {
      if ($f->isDot()) continue; //skip . ..
      if ($f->isDir()) {
        $this-> zipArchive-> addEmptyDir($f->getPathname());
        $this-> zipDir($f->getPathname());

        continue;
      }
      if ($f->isFile()) {
        if ($f->getBasename() === basename(__FILE__)) continue; // skip self 
        if ($f->getExtension() === 'zip') continue; // skip ZIP files

        $this-> zipArchive ->addFile( substr($f-> getPathname(), $this-> startPathLength) ); // remove './'

        continue;
      }

    }
  }
}

if ("go" === ($_POST['action'] ?? false)) {
  $host = str_replace('.', '_', $_SERVER['HTTP_HOST']);
  $date = date('Ymd-His');
  if ($_POST['encode'] ?? false)
    $convert_function = fn ($localPath) => iconv("CP858//IGNORE", "UTF-8", $localPath);
  else
    $convert_function = fn ($localPath) => $localPath;

  $start = microtime(true);

  $zip = new \MakeZip("./archiv-$host-$date.zip", './', $convert_function);
  unset($zip);

  $end = microtime(true);
  printf('Done in %0.6f sec.</br>', $end - $start);

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