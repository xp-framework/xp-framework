<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.FtpFile', 'peer.ftp.FtpDir');

  /**
   * Parses output from a FTP LIST command.
   *
   * Example lines (Un*x):
   * <pre>
   *   drwx---r-t 37 p159995 ftpusers 4096 Apr 4 20:16 .
   *   -rw----r-- 1 p159995 ftpusers 415 May 23 2000 write.html
   * </pre>
   *
   * Example lines (Windows):
   * <pre>
   *   01-04-06  04:51PM       <DIR>          _db_import
   *   12-23-05  04:49PM                  807 1and1logo.gif
   *   11-08-06  10:04AM                   27 info.txt 
   * </pre>
   *
   * @see      xp://peer.ftp.FtpConnection
   * @see      php://ftp_rawlist
   * @purpose  Interface
   */
  interface FtpListParser {
    
    /**
     * Parse raw listing entry.
     *
     * @param   string raw a single line
     * @param   peer.ftp.FtpConnection connection
     * @param   string base default "/"
     * @return  peer.ftp.FtpEntry
     */
    public function entryFrom($raw, FtpConnection $conn= NULL, $base= '/');
  
  }
?>
