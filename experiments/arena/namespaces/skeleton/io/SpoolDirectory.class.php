<?php
/* This class is part of the XP framework
 *
 * $Id: SpoolDirectory.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace io;

  ::uses(
    'io.File',
    'io.Folder'
  );

  /**
   * This class handles all neccessary file and directory operations
   * for doing reliable spool operations.
   *
   * @purpose   Reliable spool directory class
   */    
  class SpoolDirectory extends lang::Object {
    public
      $root;
    
    public
      $_hNew=   NULL,
      $_hDone=  NULL,
      $_hTodo=  NULL,
      $_hError= NULL;

    /**
     * Creates a spooldirectory object
     *
     * @param   string root Root of the spool hierarchy
     */    
    public function __construct($root) {
      $this->root= $root;
    }

    /**
     * Opens all neccessary directorys and creates them if nonexistant
     *
     * @return  bool success
     * @throws  io.IOException if there are permission problems
     */    
    public function open() {
      try {
        $this->_hNew=   new Folder ($this->root.DIRECTORY_SEPARATOR.'new');
        $this->_hTodo=  new Folder ($this->root.DIRECTORY_SEPARATOR.'todo');
        $this->_hDone=  new Folder ($this->root.DIRECTORY_SEPARATOR.'done');
        $this->_hError= new Folder ($this->root.DIRECTORY_SEPARATOR.'error');

        if (!$this->_hNew->exists())    $this->_hNew->::create ();
        if (!$this->_hTodo->exists())   $this->_hTodo->::create ();
        if (!$this->_hDone->exists())   $this->_hDone->::create ();
        if (!$this->_hError->exists())  $this->_hError->::create ();                  
      } catch (IOException $e) {
        throw ($e);
      }
      
      return TRUE;
    }

    /**
     * Creates a new spool entry. The abstract is used to name the
     * file in a way that associates it with its content (e.g. a 
     * unique id). If the abstract is omitted, a id
     * generator will be used.
     *
     * @param   string abstract default NULL
     * @return  io.File opened spool file
     * @throws  io.IOException if file could not be created
     */    
    public function createSpoolEntry($abstract= NULL) {
      if (NULL === $abstract)
        $abstract= date ('Y-m-d-H-i-s').md5 (microtime());
      else
        $abstract= date ('Y-m-d-H-i-s').'_'.$abstract;
      
      try {
        $f= new File ($this->_hNew->getURI().DIRECTORY_SEPARATOR.$abstract.'.spool');
        $f->open (FILE_MODE_WRITE);
      } catch (IOException $e) {
        throw ($e);
      }
      
      return $f;
    }

    /**
     * Enqueues the spoolentry into the todo-queue. 
     *
     * @param   io.File Spoolfile
     * @return  bool success
     * @throws  io.IOException if file could not be closed and moved.
     */    
    public function enqueueSpoolEntry($f) {
      try {
        $f->close();
        $f->move ($this->_hTodo->getURI().DIRECTORY_SEPARATOR.$f->getFileName());
      } catch (IOException $e) {
        throw ($e);
      }
      
      return TRUE;
    }

    /**
     * Retrieves the next spool entry.
     *
     * @return  io.File spoolfile next spoolfile. Its opened in read/write mode.
     * @throws  io.IOException if an error occurs
     */    
    public function getNextSpoolEntry() {
      try {
        if (FALSE !== ($entry= $this->_hTodo->getEntry())) {
          $f= new File ($this->_hTodo->getURI().DIRECTORY_SEPARATOR.$entry);
          $f->open (FILE_MODE_READWRITE);
        }
      } catch (IOException $e) {
        throw ($e);
      }
      
      return $f;
    }
    
    /**
     * Mark the given spool entry as done.
     *
     * @param   io.File spoolfile
     * @return  bool success
     * @throws  io.IOException if file could not be closed and moved.
     */
    public function finishSpoolEntry($f) {
      try {
        $f->close();
        $f->move ($this->_hDone->getURI().DIRECTORY_SEPARATOR.$f->getFileName());
      } catch (IOException $e) {
        throw ($e);
      }
      
      return TRUE;
    }

    /**
     * Mark the given spool entry as failed.
     *
     * @param   io.File spoolfile
     * @return  bool success
     * @throws  io.IOException if file could not be closed and moved.
     */    
    public function failSpoolEntry($f) {
      try {
        $f->close();
        $f->move ($this->_hError->getURI().DIRECTORY_SEPARATOR.$f->getFileName());
      } catch (IOException $e) {
        throw ($e);
      }
      
      return TRUE;
    }
  }
  
?>
