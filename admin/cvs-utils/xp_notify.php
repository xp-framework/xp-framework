#!/usr/bin/php -q
<?php
/* CVS commit notifier
 * This shall be called from CVSROOT/loginfo in the following way:
 * 
 * ALL     (cat > /tmp/loginfo_`md5 -q -s %{Vvs}` ; php -q /home/cvs/bin/xp_notify.php /tmp/loginfo_`md5 -q -s %{Vvs}` your@email.com %{Vvs} 1>/dev/null 2>/dev/null &)
 * 
 * $Id$
 */

  require (dirname (__FILE__).'/common.inc');
  
  // Change directory to imitate PHP4.2.3 behaviour
  chdir(getenv('CVSROOT'));

  // Read message from file, die silently if not possible
  if (FALSE === ($fd= @fopen($argv[1], 'r'))) {
    exit();
  } 
  $msg= '';
  while ($buf= fgets($fd, 4096)) {
    $msg.= $buf;
  }
  fclose($fd);
  unlink($argv[1]);

  // Append boundary
  $msg.= "\n";

  // Argument #2 is the mail address no notify
  $to= $argv[2];
  
  // Argument #3 contains the directory, the committed files and their versions
  $args= explode(' ', $argv[3]);
  
  // First element is directory relative to CVSROOT environment variable
  // Commits in multiple directories are scheduled as two or more commits, actually
  $dir= $args[0];
  
  // Handle "- New directory". This is probably not safe for files or directories
  // called "-".
  if ('-' == $args[1]) {
    $msg.= sprintf("Index: %s\n--- (add)\n+++ Initial revision\n\n", $dir);
    $args= array();
  }

  // Recurse through files. This is probably not safe for files with , in their names
  foreach (array_slice($args, 1) as $idx) {
    list($old_ver, $new_ver, $file)= explode(',', $idx);
  
    // Diff the file if it's not binary. This is an uncomplete list, of course
    if (preg_match('/\.(gif|png|jpg|tar\.gz|exe|dll|zip)$/', $file)) continue;

    if ('NONE' == $new_ver) {

      // If new version is "NONE", this means the file has been removed from CVS
      $msg.= sprintf("Index: %s/%s\n--- %s\n+++ (delete)\n", $dir, $file, $old_ver);
    } elseif ('NONE' == $old_ver) {

      // If old version is "NONE", this means the file has been added
      $msg.= sprintf("Index: %s/%s\n--- (add)\n+++ %s\n", $dir, $file, $new_ver);	
    } else {

      // Execute diff between the two versions
      $cmd= sprintf('cvs -Ql rdiff -r%s -r%s -u %s/%s 2>&1', $old_ver, $new_ver, $dir, $file); 
      $msg.= "[{$cmd}]\n".`$cmd`;
    }

    // Append boundary
    $msg.= "\n";
  }

  $realname= getUsername (getenv ('USER'));

  // Append signature
  $msg.= "-- \n".$realname."\n";

  // Finally, send mail
  mail(
    $to, 
    '[CVS] commit: '.$dir,
    $msg,
    'X-CVS: '.getenv('CVSROOT')."\n".
    'From: '.qp_encode_header($realname).' <'.$uname.'@'.getenv('HOSTNAME').">\n".
    'Reply-To: '.$to
  );
?>  
