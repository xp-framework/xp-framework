<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('org.cvshome.CVSFile');

  $p= new ParamString();
  if (!$p->exists(1)) {
    printf("Usage: %s <filename>\n", $p->value(0));
    exit(-2);
  }
  
  $f= new CVSFile($p->value(1));
  try {
    $status= $f->getStatus();
  } catch (Exception $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  switch ($status->status) {
    case CVS_PATCHED:   $s= 'patched'; break;
    case CVS_UPDATED:   $s= 'updated'; break;
    case CVS_ADDED:     $s= 'added'; break;
    case CVS_MODIFIED:  $s= 'modified'; break;
    case CVS_CONFLICT:  $s= 'conflict'; break;
    case CVS_UPTODATE:  $s= 'up-to-date'; break;
    case CVS_UNKNOWN:   $s= '(unknown)'; break;
    default:            $s= '???'; break;
  }
  
  Console::writeLinef(
    'File %s (local: %s, repository: %s) is %s [%d]',
    $status->filename,
    $status->workingrevision,
    $status->repositoryrevision,
    $s,
    $status->status
  );
  foreach ($status->tags as $name => $version) {
    Console::writeLinef('- Tag %-30s @ r%s', $name, $version);
  }
?>
