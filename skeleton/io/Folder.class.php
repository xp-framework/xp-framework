<?php
/* Diese Klasse ist Teil des XP-Frameworks
 * 
 * $Id$
 */

  uses('io.IOException');
    
  /**
   * Verzeichnis als Objekt
   * Kapselt Verzeichnis-Operationen und versieht sie mit schönen Exceptions
   *
   * <xmp>
   *   try(); {
   *     $d= new Folder('/etc/');
   *     while ($entry= $d->getEntry()) {
   *       printf("%s/%s\n", $d->uri, $entry);
   *     }
   *     $d->close();
   *   } if (catch('IOException', $e)) {
   *     $e->printStackTrace();
   *   }
   * </xmp>
   */
  class Folder extends Object {
    var 
      $uri= '',
      $dirname= '',
      $path= '';
    
    /**
     * Constructor
     *
     * @param  string dirname Verzeichnisname
     */
    function __construct($dirname= NULL) {
      if (NULL != $dirname) $this->setURI($dirname);
      parent::__construct();
    }
    
    /**
     * Destructor
     */
    function __destruct() {
      $this->close();
      parent::__destruct();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function close() {
      if ($this->_hdir) $this->_hdir->close();
    }

    /**
     * URI setzen. Definiert andere Attribute wie path & filename
     *
     * @access  public
     * @param   string uri Die URI
     * @throws  IOException, wenn uri kein Verzeichnis ist
     */
    function setURI($uri) {
    
      $this->uri= realpath($uri);
      
      // Bug in real_path (wenn Datei nicht existiert, ist die Rückgabe ein leerer String!)
      if ('' == $this->uri && $uri!= $this->uri) $this->uri= $uri;
      
      if (FALSE === ($this->_hdir= dir($this->uri))) return throw(new IOException(sprintf(
        'not a dir: "%s"',
        $this->uri
      )));
      
      $this->path= dirname($uri);
      $this->dirname= basename($uri);
    }
    
    /**
     * Das Verzeichnis anlegen, rekursiv, wenn es sein muss!
     *
     * @access  public
     * @param   int permissions default 0600 Berechtigungen
     * @return  bool Hat geklappt
     * @throws  IOException, wenn ein Verzeichnis nicht angelegt werden kann
     */
    function create($permissions= 0600) {
      if (is_dir($this->uri)) return TRUE;
      $i= 0;
      while (FALSE !== ($i= strpos($this->uri, '/', $i))) {
        if (is_dir($d= substr($this->uri, 0, ++$i))) continue;
        if (!mkdir($d, $permissions)) return throw(new IOException(sprintf(
          'mkdir("%s", %d) failed',
          $d,
          $permissions
        )));
      }
      return TRUE;
    }

    /**
     * Check, ob das Verzeichnis existiert
     *
     * @access  public
     * @return  bool Ob das Verzeichnis existiert
     */
    function exists() {
      return is_dir($this->uri);
    }
    
    /**
     * Das Verzeichnis auslesen, die Einträge . und .. weglassen
     *
     * @access  public
     * @return  mixed (bool)FALSE, wenn keine Elemente mehr übrig, (string)Verzeichniseintrag (ohne Pfadnamen!) sonst
     */
    function getEntry() {
      if (!$this->_hdir) return FALSE;
      while ($entry= $this->_hdir->read()) {
        if ($entry != '.' && $entry != '..') return $entry;
      }
      return FALSE;
    }
  }
?>
