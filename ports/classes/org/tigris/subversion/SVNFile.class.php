<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.tigris.subversion.SVNInterface',
    'io.File',
    'io.TempFile'
  );

  /**
   * This class is an easy to use interface to the concurrent versioning 
   * system executables.
   *
   * @purpose  interface to SVN
   * @see      http://www.tigris.org/subversion/
   */
  class SVNFile extends SVNInterface {
    public
      $filename= NULL;
      
    /**
     * Construct a new CVS Interface object
     *
     * @param   string filename
     * @throws  io.FileNotFoundException if filename is not a file
     */
    public function __construct($filename) {
      $this->filename= realpath($filename);
      
      if (!file_exists ($this->filename) || !is_file ($this->filename)) {
        throw new FileNotFoundException('Given file must be an existing file: '.$this->filename);
      }
    }
    
    /**
     * Update a file or directory
     *
     * @return  stdclass[] objects
     */
    public function update() {
      $results= $this->_execute ('update '.$this->path);
      
      $stats= array();
      foreach ($results as $r) {
        if ($regs= $this->getStatusFromString($r)) {
          $f= new stdClass;
          $f->status= $regs[1];
          $f->filename= basename($regs[4]);
          $f->uri= $this->path.DIRECTORY_SEPARATOR.$f->filename;
          $stats[]= $f;
        } else {
          // We could not identify this status, so ignore this file
          // TBI: Should we throw an excehption?
        }
      }
      
      return $stats;
    }
    
    /**
     * Commit the file (needs write access to repository)
     *
     * @param   string comment
     */
    public function commit($comment) {
      $f= new TempFile();
      $f->open (FILE_MODE_WRITE);
      $f->writeLine($comment);
      $f->close();

      $return= $this->_execute(sprintf('commit -F %s %s', $f->getURI(), $this->filename));
      
      $f->unlink();
      return $return;
    }
    
    /**
     * Removes a file from the repository. To complete this action, you
     * have to call commit. Use this with caution.
     *
     * @return  bool success
     */
    public function delete() {
      return $this->_execute('delete '.$this->filename);
    }

    /**
     * Adds a file to a repository. Please note, that it is neccessary
     * that the directory also already exists in SVN, otherwise
     * an error will be thrown.
     *
     * @return  bool success
     */    
    public function add() {
      return $this->_execute('add '.$this->filename);
    }
    
    /**
     * Compares two versions of this file. Leave both parameters NULL to
     * let SVN compare the local file agains the one in the repository.
     * Specify only $r1 to compare local version agains revision $r1.
     * Specify both params to diff two SVN-revisions against each other.
     * You can also use SVN-Tags here.
     *
     * @param   string revision_from
     * @param   string revision_to
     * @return  array diff lines from the diff
     */
    public function diff($r1= NULL, $r2= NULL) {
      $cmd= sprintf ('diff %s %s %s',
        (NULL !== $r1 ? '-r'.$r1 : ''),
        (NULL !== $r2 ? '-r'.$r2 : ''),
        $this->path
      );

      return $this->_execute($cmd);
    }
    
    /**
     * Move file (supports only moving in current directory)
     *
     * @param   string target
     * @return  bool
     */
    public function move($target) {
      return $this->_execute('move '.$this->filename.' '.dirname($this->filename).'/'.$target);
    }
  }
?>
