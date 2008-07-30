<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.FtpListParser');

  /**
   * Parses output from a FTP LIST command from Un*x FTP daemons.
   *
   * @test     xp://net.xp_framework.unittest.peer.DefaultFtpListParserTest
   * @see      xp://peer.ftp.FtpListParser
   * @purpose  FTP LIST parser implementation
   */
  class DefaultFtpListParser extends Object implements FtpListParser {

    /**
     * Parse raw listing entry.
     *
     * @param   string raw a single line
     * @param   peer.ftp.FtpConnection connection
     * @param   string base default "/"
     * @return  peer.ftp.FtpEntry
     */
    public function entryFrom($raw, FtpConnection $conn= NULL, $base= '/') {
      sscanf(
        $raw, 
        '%s %d %s %s %d %s %d %[^ ] %[^$]',
        $permissions,
        $numlinks,
        $user,
        $group,
        $size,
        $month,
        $day,
        $date,
        $filename
      );
      
      // Only qualify filenames if they appear unqualified in the listing
      if ('/' !== $filename{0}) {
        $filename= $base.$filename;
      }
      
      // Create a directory or an entry
      if ('d' === $permissions{0}) {
        $e= new FtpDir($filename, $conn);
      } else {
        $e= new FtpFile($filename, $conn);
      }

      $d= new Date($month.' '.$day.' '.(strstr($date, ':') ? date('Y').' '.$date : $date));

      // Check for "recent" file which are specified "HH:MM" instead
      // of year for the last 6 month (as specified in coreutils/src/ls.c)
      if (strstr($date, ':')) {
        $now= Date::now();
        if ($d->getMonth() > $now->getMonth()) $d->year--;
      }

      $e->setPermissions(substr($permissions, 1));
      $e->setNumlinks($numlinks);
      $e->setUser($user);
      $e->setGroup($group);
      $e->setSize($size);
      $e->setDate($d);
      return $e;
    }
  } 
?>
