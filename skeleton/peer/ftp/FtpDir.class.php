<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.FtpEntry');

  /**
   * FTP directory
   *
   * @see      xp://peer.ftp.FtpConnection
   * @purpose  Represent an FTP directory
   */
  class FtpDir extends FtpEntry {
    var
      $entries  = NULL;

    /**
     * Check if directory exists
     *
     * @access  public
     * @return  bool
     */
    function exists() {
      return ftp_size($this->_hdl, $this->name) != -1;
    }
      
    /**
     * Get entries (iterative function)
     *
     * @access  public
     * @return  &peer.ftp.FtpEntry FALSE to indicate EOL
     */
    function &getEntry() {
      if (NULL === $this->entries) {

        // Retrive entries, getting rid of directory self-reference "." and
        // parent directory reference, ".." - these are always reported first
        // TBD: Check if this assumption is true
        $this->entries= array_slice(ftp_rawlist($this->_hdl, $this->name), 2);
        if (empty($this->entries)) return FALSE;
        $entry= $this->entries[0];
      } else if (FALSE === ($entry= next($this->entries))) {
        reset($this->entries);
        return FALSE;
      }

      // Parse entry
      // drwx---r-t 37 p159995 ftpusers 4096 Apr 4 20:16 .
      // -rw----r-- 1 p159995 ftpusers 415 May 23 2000 write.html
      sscanf(
        $entry, 
        '%s %d %s %s %d %s %d %[^ ] %s',
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
        $e= &new FtpDir($filename, $this->_hdl);
      } else {
        $e= &new FtpEntry($filename, $this->_hdl);
      }
      with ($e); {
        $e->setPermissions(substr($permissions, 1));
        $e->setNumlinks($numlinks);
        $e->setUser($user);
        $e->setGroup($group);
        $e->setSize($size);
        $e->setDate(new Date(strtotime(
          $month.' '.$day.' '.(strstr($date, ':') ? date('Y').' '.$date : $date))
        ));
      }
      return $e;
    }
  }
?>
