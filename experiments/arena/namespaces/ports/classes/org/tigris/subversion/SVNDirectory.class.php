<?php
/* This class is part of the XP framework
 *
 * $Id: SVNDirectory.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace org::tigris::subversion;

  ::uses(
    'io.Folder',
    'org.tigris.subversion.SVNInterface'
  );

  /**
   * SVN directory
   *
   * @purpose  Interface to SVN binary
   */
  class SVNDirectory extends SVNInterface {
    public
      $path     = NULL,
      $_folder  = NULL;
    
    /**
     * Constructor
     *
     * @param   string path
     */
    public function __construct($path) {
      $this->path= $path;
      $this->_folder= new io::Folder($path);
    }
    
    /**
     * Update a directory
     *
     * @param   bool recursive default FALSE
     * @return  stdclass[] objects
     * @throws  org.cvshome.CVSInterfaceException
     */
    public function update($recursive= FALSE) {
      try {
        $results= $this->_execute(sprintf('update %s %s',
          ($recursive ? '' : '-N'),
          $this->path
        ));
      } catch ( $e) {
        throw ($e);
      }
      
      $stats= array();
      foreach ($results as $r) {
        if ($regs= $this->getStatusFromString($r)) {
          $f= new ::stdClass;
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
      $f= new io::TempFile();
      try {
        $f->open(FILE_MODE_WRITE);
        $f->writeLine($comment);
        $f->close();
      } catch (io::IOException $e) {
        throw($e);
      }

      $return= $this->_execute(sprintf('commit -F %s %s', $f->getURI(), $this->path));
      
      $f->unlink();
      return $return;
    }
    
    /**
     * Removes a directory from the repository. To complete this action,
     * you have to call commit. Use this with caution.
     *
     * @return  bool success
     */
    public function delete() {
      return $this->_execute('delete '.$this->path);
    }

    /**
     * Adds directory to a repository. Please note, that commit is required.
     *
     * @return  bool success
     */    
    public function add() {
      return $this->_execute('add '.$this->path);
    }
  }  
?>
