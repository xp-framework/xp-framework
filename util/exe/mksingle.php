<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  require('lang.base.php');
  uses (
    'util.cmd.ParamString',
    'io.File',
    'io.FileUtil',
    'text.PHPParser',
    'util.log.Logger',
    'util.log.FileAppender'
  );
  
  $l= &Logger::getInstance();
  $log= &$l->getCategory();
  $log->addAppender (new FileAppender());
  
  $p= &new ParamString();
  if (!$p->exists (1)) {
    printf ("Usage: %s scriptname.php > fullfile.php\n", basename ($p->value (0)));
    exit();
  }
  
  function resolveName($classname) {
    $l= &Logger::getInstance();
    $log= &$l->getCategory();
    if (is_file ($classname)) return $classname;
    
    foreach (explode (':', ini_get ('include_path')) as $p) {
      if (is_file ($fn= sprintf ('%s/%s.class.php', 
        $p, 
        str_replace ('.', DIRECTORY_SEPARATOR, $classname)
      ))) {
        return realpath($fn);
      }
      
      if (is_file ($fn= sprintf ('%s/%s', $p, $classname))) {
        return realpath($fn);
      }
    }
    $log->error ('Could not resolve', $classname);
    
    return FALSE;
  }
  
  function fileAddContents($filename, &$filelist) {
    $l= &Logger::getInstance();
    $log= &$l->getCategory();
    $data= '';
    
    // Get the real name of the file
    $filename= resolveName ($filename);
    if (FALSE === $filename) return '';
        
    // Skip it, if it's already been added
    if (isset ($filelist[$filename])) return '';
    
    $p= &new PHPParser ($filename);
    $p->parse();
    
    // Add dependencies
    foreach ($p->requires as $r)  { $data.= fileAddContents ($r, $filelist); }
    foreach ($p->uses as $u)      { $data.= fileAddContents ($u, $filelist); }

    $log->info ('Adding', $filename);
    
    $data.= FileUtil::getContents (new File ($filename));
    $filelist[$filename]= TRUE;
    return $data;
  }
  
  $filelist= array ();
  $fileData= fileAddContents ($p->value (1), $filelist);
  
  // Strip off all uses(), require() and include()
  // Note: this is a bit of a hack, possibly includes are removed that are still needed. 
  $fileData= preg_replace ('/^\s+(uses|require|require_once|include|include_once)\s*\([^\)]*\);\s*$/mU', '', $fileData);
  
  print $fileData;
?>
