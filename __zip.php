<?php declare(strict_types=1);
phpinfo(1);
class MakeZip
{
  private ZipArchive $zipArchive;
  private int $startPathLength; // chars to remove from the start for the stored entity

  public function __construct( string $zipArchiveName, string $startPath )
  {
    $this->zipArchive = new \ZipArchive;
    $this->zipArchive->open( $zipArchiveName, ZipArchive::CREATE );
    $this->startPathLength = strlen( $startPath );

    $this-> zipDir($startPath);
  }
  public function __destruct()
  {
    $this->zipArchive->close();
  }

  /**
   * Add files and sub-directories in a folder to zip file.
   * @param string $folder
   * @param ZipArchive $zipFile
   * @param int $exclusiveLength Number of text to be exclusived from the file path.
   */
  private function zipDir( $folder )
  {
    echo $folder . '<br>' . PHP_EOL;
    foreach( new \DirectoryIterator( $folder ) as $f ) {
      if( $f->isDot() )
        continue; //skip . ..
      if( $f->isDir() ) {
        if( $f->getExtension() === 'git' )
          continue; // skip .git folder
        $this->zipArchive->addEmptyDir( $f->getPathname() );
        $this->zipDir( $f->getPathname() );

        continue;
      }
      if( $f->isFile() ) {
        if( $f->getBasename() === basename( __FILE__ ) )
          continue; // skip self 
        if( $f->getExtension() === 'zip' )
          continue; // skip ZIP files

        $this->zipArchive->addFile( substr( $f->getPathname(), $this->startPathLength ) ); // remove './'

        continue;
      }

    }
  }
}

$start = microtime(true);

$host = str_replace( '.', '_', $_SERVER[ 'HTTP_HOST' ] );
$date = date( 'Ymd-His' );

$zip = new \MakeZip("./archiv-$host-$date.zip", './');
unset($zip);


$end = microtime(true);
printf('Done in %0.6f sec.</br>', $end - $start);
echo "<a href=\"./archiv-$host-$date.zip\">Download</a>";
