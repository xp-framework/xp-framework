<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * This file contains some common functions
   *
   */
   
  /**
   * Encodes header according to RFC 2045, 6.7
   *
   * @access  public
   * @param	  string hdr header to encode
   * @param   string charset default 'iso-8859-1' the charset in which to encode
   * @return  string quoted-printable representation
   * @see     rfc://2045#6.7
   */
  function qp_encode_header($hdr, $charset= 'iso-8859-1') {
    $r= array(' ' => '_');
    foreach (array_merge(array(61, 46), range(0, 31), range(127, 255)) as $i) {
      $r[chr($i)]= '='.strtoupper(dechex($i));
    }
    return sprintf('=?%s?Q?%s?=', $charset, strtr($hdr, $r));
  }

  /**
   * Retrieve the real name for the given username. The realname must be
   * available through the /etc/passwd-file.
   *
   * @access  public
   * @param   string sysname
   * @return  string realname;
   */
  function getUsername($sysname) {
  
    // Try to get realname from password database
    // This source assumes CVS users are local system users and listed in /etc/passwd
    $fd= fopen('/etc/passwd', 'r');
    while ($line= fgets($fd, 4096)) {
      list($uname, $pass, $uid, $id, $info, $home, $shell)= explode(':', chop($line));
      if ($sysname === $uname) { 
        $realname= $uname;
        if (FALSE !== ($i= strpos($info, ','))) $realname= substr($info, 0, $i); 
        else $realname= $info;

        break;
      }
    }
    fclose($fd);

    return $realname;
  }

  /**
   * Retrieve last tag revision
   *
   * @access  public
   * @param   string file
   * @param   string tagName
   * @param   string operation
   * @return  string
   */  
  function getLastTagRevision($file, $tagName, $operation) {
    if ('add' == $operation)
      return 'ADD';
    
    // For simplicity we assume the info is in the first 4096 bytes
    $fh= fopen ($file.',v', 'r');
    if (!$fh) return 'FPE';
    $c= fread ($fh, 4096);
    fclose ($fh);
    
    // Parse lines
    $l= explode ("\n", $c);
    $tags= FALSE;
    foreach ($l as $line) {
      if ('symbols;' == trim ($line))
        return 'N/A';
        
      if ('symbols' == trim ($line))
        $tags= TRUE;
    
      if ($tags && (FALSE !== strpos ($line, $tagName))) {
        list ($tagInfo, $revision)= explode (':', trim ($line));
        return str_replace (';', '', $revision);
      }
    }
    
    // Parsed didn't find anything
    return 'PFN';
  }

  // Web based CVS
  $cvsurl= array(
    'checkout'      => 'http://cvs.xp-framework.net/horde/chora/co.php'
  );
?>
