<?php
/* This class is part of the XP framework
 *
 * $Id: DefaultFtpListParser.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace peer::ftp;

  ::uses('peer.ftp.FtpListParser');

  /**
   * Parses output from a FTP LIST command from Un*x FTP daemons.
   *
   * @test     xp://net.xp_framework.unittest.peer.DefaultFtpListParserTest
   * @see      xp://peer.ftp.FtpListParser
   * @purpose  FTP LIST parser implementation
   */
  class DefaultFtpListParser extends lang::Object implements FtpListParser {

    /**
     * Parse raw listing entry.
     *
     * @param   string raw a single line
     * @return  peer.ftp.FtpEntry
     */
    public function entryFrom($raw) {
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
      
      if ('d' == $permissions{0}) {
        $e= new ($filename);
      } else {
        $e= new ($filename);
      }

      $d= new util::Date($month.' '.$day.' '.(strstr($date, ':') ? date('Y').' '.$date : $date));

      // Check for "recent" file which are specified "HH:MM" instead
      // of year for the last 6 month (as specified in coreutils/src/ls.c)
      if (strstr($date, ':')) {
        $now= util::Date::now();
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
