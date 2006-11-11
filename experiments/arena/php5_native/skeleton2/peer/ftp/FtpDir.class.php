<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.FtpEntry');

  /**
   * FTP directory
   *
   * @test     xp://net.xp_framework.unittest.peer.FtpRawListTest
   * @see      xp://peer.ftp.FtpConnection
   * @purpose  Represent an FTP directory
   */
  class FtpDir extends FtpEntry {
    public
      $entries  = NULL;

    /**
     * Check if directory exists
     *
     * @access  public
     * @return  bool
     */
    public function exists() {
      return ftp_size($this->_hdl, $this->name) != -1;
    }

    /**
     * Get entries (iterative function)
     *
     * @access  public
     * @return  &peer.ftp.FtpEntry FALSE to indicate EOL
     */
    public function &getEntry() {
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

      return FtpEntry::parseFrom($entry, $this->_hdl);
    }
  }
?>
