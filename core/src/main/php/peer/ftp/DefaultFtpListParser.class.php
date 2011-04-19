<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.DateUtil', 'peer.ftp.FtpListParser');

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
     * @param   util.Date ref default NULL
     * @return  peer.ftp.FtpEntry
     */
    public function entryFrom($raw, FtpConnection $conn= NULL, $base= '/', Date $ref= NULL) {
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
      
      // If the entry contains a timestamp, the year is omitted, "Apr 4 20:16" 
      // instead of "Apr 4 2009". This compact format is used when the file 
      // time is within six months* from the current date, in either direction!
      //
      // *] #define SIXMONTHS       ((365 / 2) * 86400) := 15724800
      //    See http://svn.freebsd.org/base/projects/releng_7_xen/bin/ls/print.c
      if (strstr($date, ':')) {
        $ref || $ref= Date::now();
        $d= new Date($month.' '.$day.' '.$ref->getYear().' '.$date);
        if ($d->getTime() - $ref->getTime() > 15724800) {
          $d= DateUtil::addMonths($d, -12);
        }
      } else {
        $d= new Date($month.' '.$day.' '.$date);
      }

        
      try {
        $e->setPermissions(substr($permissions, 1));
        $e->setNumlinks($numlinks);
        $e->setUser($user);
        $e->setGroup($group);
        $e->setSize($size);
        $e->setDate($d);
      } catch (IllegalArgumentException $e) {
        throw new FormatException('Cannot parse "'.$raw.'": '.$e->getMessage());
      }
      return $e;
    }
  } 
?>
