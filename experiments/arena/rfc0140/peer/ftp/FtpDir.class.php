<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.ftp.FtpEntry', 'peer.ftp.FtpEntryList');

  /**
   * FTP directory
   *
   * @see      xp://peer.ftp.FtpConnection#rootDir
   * @purpose  FtpEntry implementation
   */
  class FtpDir extends FtpEntry {

    /**
     * Returns a list of entries
     *
     * @return  peer.ftp.FtpEntryList
     * @throws  peer.SocketException in case of an I/O error
     */
    public function entries() {
      if (FALSE === ($list= ftp_rawlist($this->connection->handle, $this->name))) {
        throw new SocketException('Cannot list '.$this->name);
      }
        
      return new FtpEntryList($list, $this->connection);
    }

    /**
     * Delete this entry
     *
     * @throws  peer.SocketException in case of an I/O error
     */
    public function delete() {
      return ftp_rmdir($this->connection->handle, $this->name);
    }

    /**
     * Checks whether a file by the given name exists in this
     * directory.
     *
     * @param   string name
     * @return  bool TRUE if the file exists, FALSE otherwise
     * @throws  peer.SocketException in case of an I/O error
     */
    public function hasFile($name) {
      // ...
    }

    /**
     * Returns an FtpFile instance representing a file in this
     * directory.
     *
     * @param   string name
     * @return  peer.ftp.FtpFile the instance
     * @throws  io.FileNotFoundException in case the file was not found
     * @throws  peer.SocketException in case of an I/O error
     */
    public function getFile($name) {
      // ...
    }

    /**
     * Creates a file in this directory and returns an FtpFile instance
     * representing it.
     *
     * @param   string name
     * @return  peer.ftp.FtpFile the instance
     * @throws  io.IOException in case the file already exists
     * @throws  peer.SocketException in case of an I/O error
     */
    public function newFile($name) {
      // ...
    }

    /**
     * Returns an FtpFile instance representing a file in this
     * directory.
     *
     * Note: Same as getFile() but does not throw exceptions if the file
     * does not exist but will return an FtpFile in any case.
     *
     * @param   string name
     * @return  peer.ftp.FtpFile the instance
     * @throws  peer.SocketException in case of an I/O error
     */
    public function file($name) {
      // ...
    }

    /**
     * Checks whether a subdirectory by the given name exists in this
     * directory.
     *
     * @param   string name
     * @return  bool TRUE if the file exists, FALSE otherwise
     * @throws  peer.SocketException in case of an I/O error
     */
    public function hasDir($name) {
      // ...
    }

    /**
     * Returns an FtpDir instance representing a subdirectory in this
     * directory.
     *
     * @param   string name
     * @return  peer.ftp.FtpDir the instance
     * @throws  io.FileNotFoundException in case the file was not found
     * @throws  peer.SocketException in case of an I/O error
     */
    public function getDir($name) {
      // ...
    }

    /**
     * Creates a subdirectory in this directory and returns an FtpDir 
     * instance representing it.
     *
     * @param   string name
     * @return  peer.ftp.FtpDir the created instance
     * @throws  io.IOException in case the directory already exists
     * @throws  peer.SocketException in case of an I/O error
     */
    public function newDir($name) {
      // ...
    }

    /**
     * Returns an FtpDir instance representing a subdirectory in this
     * directory.
     *
     * Note: Same as getDir() but does not throw exceptions if the file
     * does not exist but will return an FtpDir in any case.
     *
     * @param   string name
     * @return  peer.ftp.FtpDir the instance
     * @throws  peer.SocketException in case of an I/O error
     */
    public function dir($name) {
      // ...
    }
  }
?>
