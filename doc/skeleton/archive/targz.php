<?php
/* Demonstriert die Benutzung der TarArchive-Klasse
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses('io.ZipFile', 'util.archive.TarArchive');
  
  if (!isset($_SERVER['argv'][1])) {
    printf("Usage: %s <tar.gz_file> [<file_match>]\n", basename($_SERVER['argv'][0]));
    exit;
  }
  
  $match= (isset($_SERVER['argv'][2])
    ? ':'.$_SERVER['argv'][2].':'
    : NULL
  );
  
  $fd= new TarArchive(new ZipFile($_SERVER['argv'][1]));
  try(); {
    $fd->open(FILE_MODE_READ, 9);
    while (FALSE !== ($e= $fd->getEntry())) {
      printf(
        "%s%s %s/%s %6.2f %s %s\n", 
        $e->getFileTypeString(),
        $e->getPermissionString(),
        empty($e->uname) ? $e->uid : $e->uname,
        empty($e->gname) ? $e->gid : $e->gname,
        $e->size / 1024,
        date('Y-m-d H:i', $e->mtime),
        $e->filename
      );
      
      // Extract matching files
      if (NULL != $match && preg_match($match, $e->filename)) {
        echo $fd->getEntryData($e);
      }
      
    }
    $fd->close();
  } if (catch('IOException', $e)) {
    die($e->printStackTrace());
  }
?>
