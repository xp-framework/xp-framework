<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli', 'strict');
  uses('peer.ftp.FtpConnection', 'io.Folder', 'io.File');
  
  /// {{{ Build file list
  function getFiles($root, $relbase= '') {
    $list= array();
    
    $folder= &new Folder($root);
    while (FALSE !== ($entry= $folder->getEntry())) {
      if ('.' == $entry || '..' == $entry) continue;
      
      if (is_dir($folder->getUri().DIRECTORY_SEPARATOR.$entry)) {
        $slist= getFiles(
          $folder->getUri().DIRECTORY_SEPARATOR.$entry, 
          ($relbase ? $relbase.DIRECTORY_SEPARATOR : '').$entry
        );
        
        $list= array_merge($list, $slist);
        continue;
      }
      
      $list[]= ($relbase ? $relbase.DIRECTORY_SEPARATOR : '').$entry;
    }
    
    $folder->close();
    return $list;
  }
  /// }}}
  
  /// {{{ Change to the given directory, create if needed
  function ftpChdir(&$conn, $dest) {
    $parts= explode('/', ltrim($dest, '/'));

    try(); {
      for ($i= 0; $i <= sizeof($parts); $i++) {
        $tmp= '/'.implode('/', array_slice($parts, 0, $i));
        
        $d= &$conn->getDir($tmp);
        try(); {
          $conn->setDir($d);
        } if (catch('SocketException', $e)) {
        
          // Directory does not exist...
          $conn->makeDir($d);
          $conn->setDir($d);
          
          Console::writeLine('---> Directory '.$tmp.' created.');
        }
      }
    }
  }
  /// }}}
  
  /// {{{ Put the file onto the server
  function putFile(&$conn, $file, $dest) {
    $destdir= dirname($dest);
    $destfile= basename($dest);
    
    // Go into the target directory, create if necessary
    ftpChdir($conn, $destdir);
    // Console::writeLine($destdir);
    
    // Upload file
    $conn->put($file, $dest, FTP_BINARY);
  }
  /// }}}
  
  /// {{{ main
  $param= &new ParamString();
  
  if ($param->exists('help') || $param->count < 3) {
    Console::writeLinef('%s --server=ftp://user:pass@server <fromdir> <todir>', $param->value(0));
    Console::writeLine('  <fromdir>      top directory to copy files from');
    Console::writeLine('  <todir>        target directory');
    exit(2);
  }
  
  $server= $param->value('server');
  $from= rtrim($param->value($param->count - 2), '/');
  $to= rtrim($param->value($param->count - 1), '/');
  
  Console::writeLine('===> Getting file list');
  $files= getFiles($from);
  Console::writeLine('---> Got '.sizeof($files). ' files');

  $conn= &new FtpConnection($server);
  try(); {
    $conn->connect();
    $conn->setPassive(FALSE);
  } if (catch('IOException', $e)) {
    $e->printStackTrace();
    exit(-2);
  }
  
  // Copy the files
  Console::writeLine('===> Uploading files.');
  foreach ($files as $file) {
    Console::writeline('---> Copying file '.$file);
    
    try(); {
      putFile($conn, $from.DIRECTORY_SEPARATOR.$file, $to.'/'.$file);
    } if (catch('Exception', $e)) {
      $e->printStackTrace();
      exit(-1);
    }
  }
  
  Console::writeLine('===> Done.');
  /// }}}
    
?>
