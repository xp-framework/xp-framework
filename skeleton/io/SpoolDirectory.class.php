<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses (
    'io.File',
    'io.Folder'
  );

  /**
   * This class handles all neccessary file and directory operations
   * for doing reliable spool operations.
   *
   * @purpose Reliable spool directory class
   */    
  class SpoolDirectory extends Object {
    var
      $root;
    
    var
      $_hNew=   NULL,
      $_hDone=  NULL,
      $_hTodo=  NULL,
      $_hError= NULL;

    /**
     * Creates a spooldirectory object
     *
     * @param string root Root of the spool hierarchy
     */    
    function __construct($root) {
      $this->root= $root;
    }

    /**
     * Opens all neccessary directorys and creates them if nonexistant
     *
     * @access public
     * @return bool success
     * @throws IOException if there are permission problems
     */    
    function open() {
      try(); {
        $this->_hNew=   &new Folder ($root.'/new/');
        $this->_hTodo=  &new Folder ($root.'/todo/');
        $this->_hDone=  &new Folder ($root.'/done/');
        $this->_hError= &new Folder ($root.'/error/');

        if (!$this->_hNew->exists())    $this->_hNew->create ();
        if (!$this->_hTodo->exists())   $this->_hTodo->create ();
        if (!$this->_hDone->exists())   $this->_hDone->create ();
        if (!$this->_hError->exists())  $this->_hError->create ();                  
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }
      
      return TRUE;
    }

    /**
     * Creates a new spool entry. The abstract is used to name the
     * file in a way that associates it with its content (e.g. a 
     * unique id). If the abstract is omitted, a id
     * generator will be used.
     *
     * @access public
     * @param string abstract default NULL
     * @return io.File opened spool file
     * @throws IOException if file could not be created
     */    
    function &createSpoolEntry($abstract= NULL) {
      if (NULL === $abstract)
        $abstract= date ('Y-m-d-H-i-s'.md5 (microtime()));
      
      try(); {
        $f= &new File ($this->_hNew->getURI().'/'.$abstract.'.spool');
        $f->open (FILE_MODE_READ);
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }
      
      return $f;
    }

    /**
     * Enqueues the spoolentry into the todo-queue. 
     *
     * @access public
     * @param io.File Spoolfile
     * @return bool success
     * @throws IOException if file could not be closed and moved.
     */    
    function enqueueSpoolEntry(&$f) {
      try(); {
        $f->close();
        $f->move ($this->_hTodo->getURI().'/'.$f->getFileName());
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }
      
      return TRUE;
    }

    /**
     * Retrieves the next spool entry.
     *
     * @access public
     * @return io.File spoolfile next spoolfile. Its opened in read/write mode.
     * @throws IOException if an error occurs
     */    
    function &getNextSpoolEntry() {
      try(); {
        if (FALSE !== ($entry= $this->_hTodo->getEntry())) {
          $f= &new File ($this->_hTodo->getURI().'/'.$entry);
          $f->open (FILE_MODE_READWRITE);
        }
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }
      
      return $f;
    }
    
    /**
     * Mark the given spool entry as done.
     *
     * @access public
     * @param io.File spoolfile
     * @return bool success
     * @throws IOException if file could not be closed and moved.
     */
    function finishSpoolEntry(&$f) {
      try(); {
        $f->close();
        $f->move ($this->_hDone->getURI().'/'.$f->getFileName());
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }
      
      return TRUE;
    }

    /**
     * Mark the given spool entry as failed.
     *
     * @access public
     * @param io.File spoolfile
     * @return bool success
     * @throws IOException if file could not be closed and moved.
     */    
    function failSpoolEntry(&$f) {
      try(); {
        $f->close();
        $f->move ($this->_hError->getURI().'/'.$f->getFileName());
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }
      
      return TRUE;
    }
  }
  
?>
