<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.File',
    'io.FileUtil',
    'util.log.Logger',
    'lang.archive.Archive',
    'util.PropertyManager',
    'rdbms.ConnectionManager',
    'io.collections.FileCollection',
    'io.collections.iterate.RegexFilter',
    'io.collections.iterate.AllOfFilter',
    'io.collections.iterate.AnyOfFilter',
    'io.collections.iterate.NegationOfFilter',
    'io.collections.iterate.NameEqualsFilter',
    'io.collections.iterate.FilteredIOCollectionIterator'
  );

  // {{{ function prepareForArchive()
  //     Perform class rewriting for archives
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
  
  // {{{ array<string, string> findFiles(string base, string[] includes, string[] excludes)
  //     Find files for repository which should be included in archive
  function findFiles($base, $include, $exclude) {
    $collection= &new FileCollection($base);

    // Build positive filter list
    $includes= array();
    foreach ($include as $i) { $includes[]= &new RegexFilter('#^'.$base.DIRECTORY_SEPARATOR.$i.'#'); }

    // Build negative filter list
    $excludes= array(
      new RegexFilter('/.svn/'),
      new RegexFilter('/CVS/'),
      new RegexFilter('/.orig$/')
    );
    foreach ($exclude as $e) { $excludes[]= &new RegexFilter('#^'.$base.DIRECTORY_SEPARATOR.$e.'#'); }

    for ($iterator= &new FilteredIOCollectionIterator($collection, new AllOfFilter(array(
        new AnyOfFilter($includes),
        new NegationOfFilter(new AnyOfFilter($excludes))
      )), TRUE);
      $iterator->hasNext();
    ) {
      $element= &$iterator->next();
      
      // Do not add directories
      if (is_dir($element->getURI())) continue;
      
      $files[ltrim(substr($element->getURI(), strlen($base)), DIRECTORY_SEPARATOR)]= $element->getURI();
    }
    
    Console::writeLine('   > Got '.sizeof($files).' files in '.$base);
    return $files;
  }
  // }}}

  // Params
  $p= &new ParamString();
  
  // Display help
  if ($p->exists('help', 'h')) {
    Console::writeLinef('...');
    exit();
  }
 
  $prop= &new Properties($p->value(1));
  
  // Build replacement list
  $replacements= array(
    '%TRUNK%' => rtrim(preg_replace('#^(.*/trunk/)(.*)$#', '$1', realpath($p->value(1))), DIRECTORY_SEPARATOR)
  );
  
  $application= $prop->readSection('application');
  $filelist= array();
  
  Console::writeLinef('===> Packaging application %s (xp-%s-%s)',
    $application['name'],
    $application['short'],
    $application['version']
  );
  
  Console::writeLine('===> Building file list ...');

  $section= $prop->getFirstSection();
  do {
    if (0 != strncmp('repository::', $section, 11)) continue;
    
    Console::writeLine('---> Adding '.$section);
    $base= $prop->readString($section, 'base');
    $base= strtr($base, $replacements);
    
    try(); {
      $files= findFiles($base, $prop->readArray($section, 'include'), $prop->readArray($section, 'exclude'));
    } if (catch('Exception', $e)) {
      $e->printStackTrace();
      exit(-1);
    }
    
    $filelist= array_merge($files, $filelist);
  } while ($section= $prop->getNextSection());
  
  Console::writeLine('===> Fastening package ...');
  $archiveName= sprintf('xp-%s-%s.xar',
    $application['short'],
    $application['version']
  );
  $archive= &new Archive(new File($archiveName));
  $archive->open(ARCHIVE_CREATE);
  
  foreach ($filelist as $pkgname => $realname) {
  
    $file= &new File($realname);
    
    try(); {
      $bytes= FileUtil::getContents($file);
    } if (catch('IOException', $e)) {
      Console::writeLine('!--> Could not add file '.$realname.':'.$e->toString());
      Console::writeLine();
      continue;
    }
    
    // Prepare classes for inclusion in archive
    if ('.class.php' == substr($realname, -10)) $bytes= prepareForArchive($pkgname, $bytes);
    
    $archive->addFileBytes(
      $pkgname,
      basename($pkgname),
      dirname($pkgname),
      $bytes
    );
  }
  
  $archive->create();
  
  Console::writeLine('===> Finished, package is '.$archiveName.' ('.number_format(filesize($archiveName), 0, FALSE, '.').' bytes)');
?>
