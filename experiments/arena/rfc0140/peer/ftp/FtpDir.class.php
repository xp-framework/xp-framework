<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.ftp.FtpEntry', 'peer.ftp.FtpEntryList', 'io.FileNotFoundException');

  /**
   * FTP directory
   *
   * @see      xp://peer.ftp.FtpConnection#rootDir
   * @purpose  FtpEntry implementation
   */
  class FtpDir extends FtpEntry {

    /**
     * Constructor
     *
     * @param   string name
     * @param   peer.ftp.FtpConnection connection
     */
    public function __construct($name, FtpConnection $connection) {
      parent::__construct(rtrim($name, '/').'/', $connection);
    }

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
     * Create this entry
     *
     * @throws  peer.SocketException in case of an I/O error
     */
    public function create() {
      if (FALSE === ftp_mkdir($this->connection)) {
        throw new SocketException('Could not create directory "'.$name.'"');
      }
    }

    /**
     * Delete this entry
     *
     * @throws  peer.SocketException in case of an I/O error
     */
    public function delete() {
      if (FALSE === ftp_rmdir($this->connection->handle, $this->name)) {
        throw new SocketException('Could not delete directory "'.$name.'"');
      }
    }
    
    /**
     * Find an entry
     *
     * @param   string name
     * @return  peer.ftp.FtpEntry entry or NULL if nothing was found
     */
    public function findEntry($name) {
      if (!($f= ftp_rawlist($this->connection->handle, '-d '.$this->name.$name))) return NULL;

      // Ensure we only get one result
      if (1 != ($s= sizeof($f))) {
        throw new FormatException('List "'.$this->name.$name.'" yielded '.$s.' result(s), expected: 1 ('.xp::stringOf($f).')');
      }

      return $this->connection->parser->entryFrom($f[0], $this->connection);
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
      if (!($e= $this->findEntry($name))) {
        return FALSE;
      } else if ($e instanceof FtpDir) {
        throw new IllegalStateException('File "'.$name.'" is a directory');
      }
      return TRUE;
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
      if (!($e= $this->findEntry($name))) {
        throw new FileNotFoundException('File "'.$name.'" not found');
      } else if ($e instanceof FtpDir) {
        throw new IllegalStateException('File "'.$name.'" is a directory');
      }
      return $e;
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
      if ($e= $this->findEntry($name)) {
        throw new IOException('File "'.$name.'" already exists ('.$e->toString().')');
      }
      return new FtpFile($name, $this->connection);
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
      if (!($e= $this->findEntry($name))) {
        return new FtpFile($name, $this->connection);
      } else if ($e instanceof FtpDir) {
        throw new IllegalStateException('File "'.$name.'" is a directory');
      }
      return $e;
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
      if (!($e= $this->findEntry($name))) {
        return FALSE;
      } else if ($e instanceof FtpFile) {
        throw new IllegalStateException('Directory "'.$name.'" is a file');
      }
      return TRUE;
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
      if (!($e= $this->findEntry($name))) {
        throw new FileNotFoundException('Directory "'.$name.'" not found');
      } else if ($e instanceof FtpFile) {
        throw new IllegalStateException('Directory "'.$name.'" is a file');
      }
      return $e;
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
      if ($e= $this->findEntry($name)) {
        throw new IllegalStateException('Directory "'.$name.'" already exists ('.$e->toString().')');
      }
      
      return new FtpDir($name, $this->connection);
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
      if (!($e= $this->findEntry($name))) {
        return new FtpDir($name, $this->connection);
      } else if ($e instanceof FtpFile) {
        throw new IllegalStateException('Directory "'.$name.'" is a file');
      }
      return $e;
    }
  }
?>
