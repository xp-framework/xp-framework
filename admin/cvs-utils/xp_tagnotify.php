#!/usr/bin/php -q
<?php
/* CVS commit notifier
 * This shall be called from CVSROOT/loginfo in the following way:
 * 
 * ALL     (cat | /home/cvs/bin/notify.php your@email.com %{Vvs} 1>/dev/null 2>/dev/null &)
 * 
 * DEFAULT /home/cvs/bin/xp_tagnotify.php your@email.com
 *
 * $Id$
 */

  require (dirname (__FILE__).'/common.inc');
  
  $operationVerb= array (
    'add' => 'added',
    'mov' => 'moved',
    'del' => 'deleted'
  );
  
  $me=          array_shift ($argv);
  $to=          array_shift ($argv);
  $tagName=     array_shift ($argv);
  $operation=   array_shift ($argv);
  $repository=  array_shift ($argv);
  $changed=     FALSE;
  $localPath= str_replace (getenv ('CVSROOT').'/', '', $repository);
  
  $fileInfo= array();
  while (!empty ($argv)) {
    $f= new StdClass();
    $f->filename= array_shift ($argv);
    $f->revision= array_shift ($argv);
    $f->oldrevision= getLastTagRevision ($repository.'/'.$f->filename, $tagName, $operation);
    $fileInfo[]= $f;
  }
  
  $realname= getUsername (getenv ('USER'));
  
  $msg= sprintf ("Update of %s\n\n", $repository);
  $head= sprintf ("%s %s the tag %s:\n",
    getenv ('USER'),
    $operationVerb[$operation],
    $tagName
  );
  $head.= str_repeat ("=", 60)."\n\n";
  $msg.= $head;
  
  switch ($operation) {
    default:
    case 'add': $vFormat= '  %-30s  %3$6s'; break;
    case 'mov': $vFormat= '  %-30s  %6s --> %-6s'; break;
    case 'del': $vFormat= '  %-30s  %3$6s --> [  ]'; break;
  }

  foreach ($fileInfo as $file) {
    $msg.= sprintf ($vFormat."\n",
      $file->filename,
      '['.$file->oldrevision.']',
      '['.$file->revision.']'
    );
    $changed= TRUE;
  }
  
  $msg.= "\n";
  
  // Append signature
  $msg.= "-- \n".$realname."\n";
  
  // Send mail
  if ($changed) {
    mail (
      $to,
      '[CVS]    tag: '.$localPath,
      $msg,
      'X-CVS: '.getenv ('CVSROOT')."\n".
      'From: '.qp_encode_header($realname).' <'.getenv('USER').'@'.getenv('HOSTNAME').">\n".
      'Reply-To: '.$to
    );
  }
  
  // Return 0, otherwise cvs would block the tag command
  exit (0);

?>
