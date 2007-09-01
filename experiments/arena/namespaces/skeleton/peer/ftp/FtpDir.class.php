<?php
/* This class is part of the XP framework
 *
 * $Id: FtpDir.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace peer::ftp;

  ::uses('peer.ftp.FtpEntry');

  /**
   * FTP directory
   *
   * @see      xp://peer.ftp.FtpConnection
   * @purpose  Represent an FTP directory
   */
  class FtpDir extends FtpEntry {
    public
      $entries  = NULL,
      $_offset  = 0;

    /**
     * Check if directory exists
     *
     * @return  bool
     */
    public function exists() {
      return ftp_size($this->connection->handle, $this->name) != -1;
    }

    /**
     * Get entries (iterative function)
     *
     * @return  peer.ftp.FtpEntry FALSE to indicate EOL
     */
    public function getEntry() {
      if (NULL === $this->entries) {

        // Retrieve entries
        if (FALSE === ($list= ftp_rawlist($this->connection->handle, $this->name))) {
          throw(new peer::SocketException('Cannot list '.$this->name));
          return FALSE;
        }
        
        $this->entries= $list;
        $this->_offset= 0;
        if (empty($this->entries)) return FALSE;
      } else if (0 == $this->_offset) {
        $this->entries= NULL;
        return FALSE;
      }

      // Get rid of directory self-reference "." and parent directory 
      // reference, ".."
      do {        
        try {
          $entry= $this->connection->parser->entryFrom($this->entries[$this->_offset]);
        } catch (::Exception $e) {
          throw(new peer::SocketException(sprintf(
            'During listing of #%d (%s): %s',
            $this->_offset,
            $this->entries[$this->_offset],
            $e->getMessage()
          )));
          return FALSE;
        }
        
        // If we reach max, reset offset to 0 and break out of this loop
        if (++$this->_offset >= sizeof($this->entries)) {
          $this->_offset= 0;
          break;
        }
      } while ('.' == $entry->getName() || '..' == $entry->getName());
      
      // Inject connection and return
      $entry->connection= $this->connection;
      return $entry;
    }
  }
?>
