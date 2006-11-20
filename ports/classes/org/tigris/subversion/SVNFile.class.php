<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses (
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
    var
      $filename= NULL;
      
    /**
     * Construct a new CVS Interface object
     *
     * @access  public
     * @param   string filename
     * @throws  io.FileNotFoundException if filename is not a file
     */
    function __construct($filename) {
      $this->filename= realpath($filename);
      
      if (!file_exists ($this->filename) || !is_file ($this->filename)) {
        return throw (new FileNotFoundException ('Given file must be an existing file: '.$this->filename));
      }
    }
    
    /**
     * Update a file or directory
     *
     * @access  public
     * @return  stdclass[] objects
     */
    function update() {
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
     * @access  public
     * @param   string comment
     */
    function commit($comment) {
      $f= &new TempFile();
      try(); {
        $f->open (FILE_MODE_WRITE);
        $f->writeLine($comment);
        $f->close();
      } if (catch('IOException', $e)) {
        return throw($e);
      }

      $return= &$this->_execute(sprintf('commit -F %s %s', $f->getURI(), $this->filename));
      
      $f->unlink();
      return $return;
    }
    
    /**
     * Removes a file from the repository. To complete this action, you
     * have to call commit. Use this with caution.
     *
     * @access  public
     * @return  bool success
     */
    function delete() {
      return $this->_execute('delete '.$this->filename);
    }

    /**
     * Adds a file to a repository. Please note, that it is neccessary
     * that the directory also already exists in SVN, otherwise
     * an error will be thrown.
     *
     * @access  public
     * @return  bool success
     */    
    function add() {
      return $this->_execute('add '.$this->filename);
    }
    
    /**
     * Compares two versions of this file. Leave both parameters NULL to
     * let SVN compare the local file agains the one in the repository.
     * Specify only $r1 to compare local version agains revision $r1.
     * Specify both params to diff two SVN-revisions against each other.
     * You can also use SVN-Tags here.
     *
     * @access  public
     * @param   string revision_from
     * @param   string revision_to
     * @return  array diff lines from the diff
     */
    function diff($r1= NULL, $r2= NULL) {
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
     * @access  public
     * @param   string target
     * @return  bool
     */
    function move($target) {
      return $this->_execute('move '.$this->filename.' '.dirname($this->filename).'/'.$target);
    }
  }
?>
