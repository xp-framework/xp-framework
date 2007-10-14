<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.ftp.FtpEntry', 
    'peer.ftp.FtpEntryList', 
    'io.FileNotFoundException',
    'io.IOException',
    'peer.ProtocolException'
  );

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
     * @throws  io.IOException in case of an I/O error
     */
    public function entries() {
      if (FALSE === ($list= ftp_rawlist($this->connection->handle, $this->name))) {
        throw new IOException('Cannot list "'.$this->name.'"');
      }
        
      return new FtpEntryList($list, $this->connection);
    }

    /**
     * Delete this entry
     *
     * @throws  io.IOException in case of an I/O error
     */
    public function delete() {
      if (FALSE === ftp_rmdir($this->connection->handle, $this->name)) {
        throw new IOException('Could not delete directory "'.$this->name.'"');
      }
    }
    
    /**
     * Find an entry
     *
     * @param   string name
     * @return  peer.ftp.FtpEntry entry or NULL if nothing was found
     * @throws  peer.ProtocolException in case listing fails
     */
    protected function findEntry($name) {
      if (!($f= ftp_rawlist($this->connection->handle, '-d '.$this->name.$name))) return NULL;

      // Ensure we only get one result
      if (1 != ($s= sizeof($f))) {
        throw new ProtocolException('List "'.$this->name.$name.'" yielded '.$s.' result(s), expected: 1 ('.xp::stringOf($f).')');
      }

      return $this->connection->parser->entryFrom($f[0], $this->connection);
    }

    /**
     * Checks whether a file by the given name exists in this
     * directory.
     *
     * @param   string name
     * @return  bool TRUE if the file exists, FALSE otherwise
     * @throws  lang.IllegalStateException in case the file exists but is a directory
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
     * @throws  lang.IllegalStateException in case the file exists but is a directory
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
     * @throws  lang.IllegalStateException in case the file already exists
     */
    public function newFile($name) {
      if ($e= $this->findEntry($name)) {
        throw new IllegalStateException('File "'.$name.'" already exists ('.$e->toString().')');
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
     * @throws  lang.IllegalStateException in case the file is a directory
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
     * @throws  lang.IllegalStateException in case the directory is a file
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
     * @throws  io.FileNotFoundException in case the directory was not found
     * @throws  lang.IllegalStateException in case the directory exists but is a file
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
     * Create a new directory
     *
     * @param   string name
     * @throws  io.IOException if directory cannot be created
     * @throws  peer.ProtocolException in case the created directory cannot be located or is a file
     */
    protected function makeDir($name) {
      if (FALSE === ftp_mkdir($this->connection->handle, $this->name.$name)) {
        throw new IOException('Could not create directory "'.$name.'"');
      }
      
      if (!($created= $this->findEntry($name))) {
        throw new ProtocolException('MKDIR "'.$name.'" succeeded but could not find created directory afterwards');
      } else if (!$created instanceof FtpDir) {
        throw new ProtocolException('MKDIR "'.$name.'" succeeded but directory listing reveals a file');
      }
      return $created;
    }

    /**
     * Creates a subdirectory in this directory and returns an FtpDir 
     * instance representing it.
     *
     * @param   string name
     * @return  peer.ftp.FtpDir the created instance
     * @throws  lang.IllegalStateException in case the directory already exists
     * @throws  io.IOException in case the directory could not be created
     */
    public function newDir($name) {
      if ($e= $this->findEntry($name)) {
        throw new IllegalStateException('Directory "'.$name.'" already exists ('.$e->toString().')');
      }

      return $this->makeDir($name);
    }

    /**
     * Returns an FtpDir instance representing a subdirectory in this
     * directory.
     *
     * Note: Same as getDir() but does not throw exceptions if the 
     * directory does not exist but will create it and thus return an 
     * FtpDir in any case.
     *
     * @param   string name
     * @return  peer.ftp.FtpDir the instance
     * @throws  lang.IllegalStateException in case the directory exists and is a file
     * @throws  io.IOException in case the directory could not be created
     */
    public function dir($name) {
      if (!($e= $this->findEntry($name))) {
        return $this->makeDir($name);
      } else if ($e instanceof FtpFile) {
        throw new IllegalStateException('Directory "'.$name.'" is a file');
      }
      return $e;
    }
  }
?>
