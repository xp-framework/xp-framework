<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses('io.IOException');
    
  /**
   * Represents a Folder
   *
   * Usage:
   * <code>
   *   try(); {
   *     $d= new Folder('/etc/');
   *     while ($entry= $d->getEntry()) {
   *       printf("%s/%s\n", $d->uri, $entry);
   *     }
   *     $d->close();
   *   } if (catch('IOException', $e)) {
   *     $e->printStackTrace();
   *   }
   * </code>
   */
  class Folder extends Object {
    var 
      $uri= '',
      $dirname= '',
      $path= '';
    
    var
      $_hdir= FALSE;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string dirname the directory's name
     */
    function __construct($dirname= NULL) {
      if (NULL != $dirname) $this->setURI($dirname);
      parent::__construct();
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      $this->close();
      parent::__destruct();
    }
    
    /**
     * Close directory
     *
     * @access  public
     */
    function close() {
      if (FALSE != $this->_hdir) $this->_hdir->close();
      $this->_hdir= NULL;
    }

    /**
     * Set URI
     *
     * @access  private
     * @param   string uri the complete path name
     */
    function setURI($uri) {
    
      $this->uri= realpath($uri);
      
      // Bug in real_path (wenn Datei nicht existiert, ist die Rückgabe ein leerer String!)
      if ('' == $this->uri && $uri!= $this->uri) $this->uri= $uri;
      
      // "Trailing /" ergänzen
      if ($this->uri{strlen($this->uri)- 1} != '/') $this->uri.= '/';
      
      $this->path= dirname($uri);
      $this->dirname= basename($uri);
    }
    
    /**
     * Das Verzeichnis anlegen, rekursiv, wenn es sein muss!
     *
     * @access  public
     * @param   int permissions default 0700 Berechtigungen
     * @return  bool Hat geklappt (oder war bereits vorhanden)
     * @throws  IOException, wenn ein Verzeichnis nicht angelegt werden kann
     */
    function create($permissions= 0700) {
      if (is_dir($this->uri)) return TRUE;
      $i= 0;
      $umask= umask(000);
      while (FALSE !== ($i= strpos($this->uri, '/', $i))) {
        if (is_dir($d= substr($this->uri, 0, ++$i))) continue;
        if (FALSE === mkdir($d, $permissions)) {
          umask($umask);
          return throw(new IOException(sprintf(
            'mkdir("%s", %d) failed',
            $d,
            $permissions
          )));
        }
      }
      umask($umask);
      return TRUE;
    }

    /**
     * Returns whether this directory exists
     *
     * @access  public
     * @return  bool TRUE in case the directory exists
     */
    function exists() {
      return is_dir($this->uri);
    }
    
    /**
     * Read through the contents of the directory, ommitting the entries "." and ".."
     *
     * @access  public
     * @return  string entry directory entry (w/o path!), FALSE, if no more entries are left
     * @throws  IOException in case an error occurs
     */
    function getEntry() {
      if (
        (FALSE === $this->_hdir) &&
        (FALSE === ($this->_hdir= dir($this->uri)))
      ) {
        return throw(new IOException(sprintf(
          'Cannot open directory "%s"',
          $this->uri
        )));
      }
      
      while ($entry= $this->_hdir->read()) {
        if ($entry != '.' && $entry != '..') return $entry;
      }
      return FALSE;
    }
  }
?>
