<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses(    
    'io.File',
    'io.Folder',
    'io.FileUtil',
    'lang.archive.Archive'
  );
  
  // {{{ function prepareForArchive()
  function prepareForArchive($filename, $source) {
  
    // Only rewrite PHP source
    if ('.php' != substr($filename, -4)) return $source;
    
    $src= '';
    $line= 0;
    $tokens= token_get_all($source);
    if (xp::errorAt(__LINE__- 1)) {
      xp::error('Error in file '.$filename);
    }
    
    for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
      switch ($tokens[$i][0]) {
        case T_FILE: 
          $tokens[$i][1]= "'".$filename."'"; 
          break;

        case T_LINE:
          $tokens[$i][1]= $line;
          break;
      }

      if (is_array($tokens[$i])) {
        $src.= $tokens[$i][1];
        $line+= substr_count($tokens[$i][1], "\n");
      } else {
        $src.= $tokens[$i];
        $line+= substr_count($tokens[$i], "\n");
      }
    }
    
    return $src;
  }
  // }}}
  
  // {{{ function recurse()
  function recurse(&$archive, $base, $path) {
    $f= &new Folder($path);
    
    while (FALSE !== ($entry= $f->getEntry())) {
      if ('.' == $entry{0}) continue;
      
      $file= &new File($f->getURI().DIRECTORY_SEPARATOR.$entry);
      if (is_dir($file->getURI())) {
        recurse($archive, $base, $file->getURI());
        continue;
      }
      
      // if ('.class.php' != substr($file->getURI(), -10)) continue;
      
      // Remove base
      $filename= ltrim(substr($file->getURI(), strlen($base)), '/');
      
      if ($archive->contains($filename)) {
        Console::writeLine('!!! File '.$filename.' already in archive, skipping...');
        continue;
      }
      
      try(); {
        $contents= FileUtil::getContents($file);
      } if (catch('IOException', $e)) {
        $e->printStackTrace();
        continue;
      }
      
      $archive->addFileBytes(
        $filename,
        $file->getFilename(),
        $file->getPath(),
        prepareForArchive($filename, $contents)
      );
    }
    
    $f->close();
  }
  // }}}

  // Params
  $p= &new ParamString();
  
  // Display help
  if ($p->exists('help', 'h')) {
    Console::writeLinef('%s [--file=<filename>] [path] ...');
    Console::writeLine('  --file=<filename>  Write archive into <filename>, default STDOUT');
    Console::writeLine('  [path]              Adds all files in that path to the archive');
    exit();
  }
  
  $archive= &new Archive(new File($p->value('file', 'f', 'php://stdout')));
  $archive->open(ARCHIVE_CREATE);
  
  for ($i= 1; $i < $p->count; $i++) {
    $value= $p->value($i);
    if ('-' == $value{0}) continue;
    
    $start= realpath($value);
    recurse(
      $archive,
      $start,
      $start
    );
  }
  
  $archive->create();
  Console::writeLinef('===> %d files added', sizeof($archive->_index));
?>
