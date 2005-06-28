<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

/* Copy all example and class files 
 * into destination directory
 */
 
  require ('lang.base.php');
  uses (
    'util.cmd.ParamString',
    'io.File',
    'io.Folder',
    'util.Properties'
  );
  
  function getXPClassName($uri, $base) {
    $uri= preg_replace ('/(\/+)/', '/', $uri);
    $uri= preg_replace ('/^'.str_replace ('/', '\\/', $base).'/', '', $uri);
    $uri= preg_replace ('/\.class\.php$/', '', $uri);
    $uri= str_replace ('/', '.', $uri);
    return $uri;    
  }
  
  /**
   * Makes a flat copy of the given directory
   *
   * @param   string base basedir
   * @param   string dest destdir
   * @param   string gBase
   */  
  function recurseCopyFlat($base, $dest, $gBase) {
    $classPrefix= getXPClassName ($base, $gBase);
    $classPrefix= (empty ($classPrefix) ? '' : $classPrefix.'.');
    
    //echo "---> Folder: $base\n";
    $folder= &new Folder($base);
    while (FALSE !== ($entry= $folder->getEntry())) {
      // Keep this "echo", as long as 
      // http://bugs.php.net/bug.php?id=20186 is not fixed
      echo "     >> $entry\n";
      if ('.class.php' == substr($entry, -10)) {
        echo "---> Copying $entry\n";
        try(); {
          $f= &new File ($base.'/'.$entry);
          $f->copy ($dest.'/'.$classPrefix.$entry);
          //exec ('cp '.$base.'/'.$entry.' '.$dest.'/'.$classPrefix.$entry)."\n";
        } if (catch ('IOException', $e)) {
          $e->printStackTrace ();
          continue;
        }
      }

      if (!is_file ($base.'/'.$entry) && 'CVS' != $entry && '.svn' != $entry) {
        echo "---> Diving into $base/$entry\n";
        recurseCopyFlat ($base.'/'.$entry, $dest, $gBase);
        continue;
      }
    }
    unset ($folder);
  }
  
  $param= &new ParamString();
  if (NULL === ($dest= $param->value ('dest', 'd'))) {
    echo $argv[0]." <destination directory>\n";
    exit;
  }
  
  $prop= &new Properties ('packages.ini');
  if ($prop->exists ()) {
    $sect= $prop->getFirstSection();
    do {
      if (!empty ($sect))
        $packages[$sect]= $prop->readSection($sect);
    } while (FALSE !== ($sect= $prop->getNextSection()));
  }

  if (!isset ($packages['core'])) {
    $packages['core']= array (
      'path' => SKELETON_PATH,
      'name' => 'Core Packages',
      'base' => SKELETON_PATH
    );
  }
  
  foreach ($packages as $type=> $info) {
    recurseCopyFlat ($info['path'], $dest, $info['base']);
  }
  echo "===> Finished copying files.\n";

?>
