<?php
/*
 * $Id$
 *
 * Diese Klasse ist Bestandteil des XP-Frameworks
 * (c) 2001 Timm Friebe, Schlund+Partner AG
 *
 * @see http://doku.elite.schlund.de/projekte/xp/skeleton/
 *
 */

  // Verschiedene Modi
  define('FILE_MODE_READ',      'r');          // Lesen
  define('FILE_MODE_READWRITE', 'r+');         // Lesen + Schreiben
  define('FILE_MODE_WRITE',     'w');          // Schreiben
  define('FILE_MODE_REWRITE',   'w+');         // Lesen + Schreiben, auf 0 Bytes kürzen
  define('FILE_MODE_APPEND',    'a');          // Anfügen (nur schreiben)
  define('FILE_MODE_READAPPEND','a+');         // Anfügen (Lesen + Schreiben)
  
  // Standard- In/Out/Error
  define('STDIN',               'php://stdin');
  define('STDOUT',              'php://stdout');
  define('STDERR',              'php://stderr');
  
  uses(
    'io.IOException',
    'io.FileNotFoundException'
  );
    
  /**
   * File als Objekt
   * Kapselt die Dateioperation, fasst sie in einer Klasse zusammen und versieht
   * sie mit schönen Exceptions
   */
  class File extends Object {
    var 
      $uri= '', 
      $filename= '',
      $path= '',
      $extension= '',
      $mode= FILE_MODE_READ;
    
    // Private Variablen
    var $_fd= NULL;
    
    /**
     * Constructor
     *
     * @param  (array)params  @see Object#__construct oder
     * @param  (string)params Dateiname
     */
    function __construct($params= NULL) {
      if (is_string($params)) {
        $this->setURI($params);
        $params= NULL;
      }
      Object::__construct($params);
    }

    /**
     * URI setzen. Definiert andere Attribute wie path, filename und extension
     *
     * @param   (string)uri Die URI
     */
    function setURI($uri) {
    
      // PHP-Scheme
      if ('php://' == substr($uri, 0, 6)) {
        $this->path= NULL;
        $this->extension= NULL;
        $this->filename= $this->uri= $uri;
        return;
      }
      
      $this->uri= realpath($uri);
      
      // Bug in real_path (wenn Datei nicht existiert, ist die Rückgabe ein leerer String!)
      if ('' == $this->uri && $uri!= $this->uri) $this->uri= $uri;
      
      $this->path= dirname($uri);
      $this->filename= basename($uri);
      $this->extension= NULL;
      if (preg_match('/\.(.+)$/', $this->filename, $regs)) $this->extension= $regs[1];
    }

    /**
     * Datei öffnen
     *
     * @param   (string)mode Öffnen-Modus
     * @throws  IllegalStateException, wenn keine URI angegeben ist
     * @throws  FileNotFoundException, wenn Datei nicht gefunden wurde
     */
    function open($mode= FILE_MODE_READ) {
      if (empty($this->uri)) return throw(new IllegalStateException('no uri defined'));
      $this->mode= $mode;
      if (
        ('php://' != substr($this->uri, 0, 6)) &&
        ($mode== FILE_MODE_READ) && 
        (!$this->exists())
      ) return throw(new FileNotFoundException($this->uri));
      $this->_fd= @fopen($this->uri, $this->mode);
      if (!$this->_fd) return throw(new IOException('cannot open '.$this->uri.' mode '.$this->mode));
    }
    
    /**
     * Existiert Datei?
     *
     * @return  (bool)doesExist Datei existiert => TRUE, sonst FALSE
     * @throws  IllegalStateException, wenn keine URI angegeben ist
     */
    function exists() {
      if (!isset($this->uri)) return throw(new IllegalStateException('no uri defined'));
      return file_exists($this->uri);
    }
    
    /**
     * Dateigröße ermitteln
     *
     * @return  (int)size Dateigröße in Bytes
     * @throws  IllegalStateException, wenn keine URI angegeben ist
     * @throws  IOException, wenn Dateigröße nicht ermittelt werden kann
     */
    function size() {
      if (!isset($this->uri)) return throw(new IllegalStateException('no uri defined'));
      $size= @filesize($this->uri);
      if (FALSE === $size) return throw(new IOException('cannot get filesize for '.$this->uri));
      return $size;
    }
    
    /**
     * Beschneiden (AU!!!)
     *
     * @param   (int)size default 0 Neue Größe in Bytes
     * @throws  IllegalStateException, wenn keine URI angegeben ist
     * @throws  IOException, wenn Dateigröße nicht ermittelt werden kann
     */
    function truncate($size= 0) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('no file open'));
      $return= @ftruncate($this->_fd, $size);
      if (FALSE === $return) return throw(new IOException('cannot truncate file '.$this->uri));
      return $return;
    }

    /**
     * Letzter Zugriff
     *
     * @return  (int)atime Das Datum des letzten Zugriffs auf die Datei als Unix-TimeStamp
     * @throws  IllegalStateException, wenn keine URI angegeben ist
     * @throws  IOException, wenn das Datum nicht ermittelt werden kann
     */
    function lastAccessed() {
      if (!isset($this->uri)) return throw(new IllegalStateException('no uri defined'));
      $atime= fileatime($this->uri);
      if (FALSE === $atime) return throw(new IOException('cannot get atime for '.$this->uri));
      return $atime;
    }
    
    /**
     * Letzte Änderung
     *
     * @return  (int)mtime Das Datum der letzten Änderung an die Datei als Unix-TimeStamp
     * @throws  IllegalStateException, wenn keine URI angegeben ist
     * @throws  IOException, wenn das Datum nicht ermittelt werden kann
     */
    function lastModified() {
      if (!isset($this->uri)) return throw(new IllegalStateException('no uri defined'));
      $mtime= fileatime($this->uri);
      if (FALSE === $mtime) return throw(new IOException('cannot get mtime for '.$this->uri));
      return $mtime;
    }

    /**
     * Erstellungsdatum
     *
     * @return  (int)ctime Das Erstellungsdatum der Datei
     * @throws  IllegalStateException, wenn keine URI angegeben ist
     * @throws  IOException, wenn das Datum nicht ermittelt werden kann
     */
    function createdAt() {
      if (!isset($this->uri)) return throw(new IllegalStateException('no uri defined'));
      $mtime= filectime($this->uri);
      if (FALSE === $mtime) return throw(new IOException('cannot get mtime for '.$this->uri));
      return $mtime;
    }

    /**
     * Zeile auslesen. \r oder \n werden abgeschnitten
     *
     * @param   (int)bytes default 4096 Anzahl max. zu lesender Bytes
     * @return  (string)line Gelesene Bytes
     * @throws  IOException, wenn nicht gelesen werden kann
     * @throws  IllegalStateException, wenn keine Datei offen
     */
    function readLine($bytes= 4096) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= chop(@fgets($this->_fd, $bytes));
      if ($result === FALSE) {
        return throw(new IOException('cannot read '.$bytes.' bytes from '.$this->uri));
      }
      return $result;
    }
    
    /**
     * Zeichen auslesen
     *
     * @return  (char)result Gelesenes Zeichen
     * @throws  IOException, wenn nicht gelesen werden kann
     * @throws  IllegalStateException, wenn keine Datei offen
     */
    function readChar() {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= @fgetc($this->_fd);
      if (!$result) {
        return throw(new IOException('cannot read '.$bytes.' bytes from '.$this->uri));
      }
      return $result;
    }

    /**
     * Lesen.
     *
     * @param   (int)bytes default 4096 Anzahl max. zu lesender Bytes
     * @return  (string)line Gelesene Bytes
     * @throws  IOException, wenn nicht gelesen werden kann
     * @throws  IllegalStateException, wenn keine Datei offen
     */
    function read($bytes= 4096) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= @fread($this->_fd, $bytes);
      if ($result === FALSE) {
        return throw(new IOException('cannot read '.$bytes.' bytes from '.$this->uri));
      }
      return $result;
    }

    /**
     * Schreiben.
     *
     * @return  (bool)success
     * @throws  IOException, wenn nicht geschrieben werden kann
     * @throws  IllegalStateException, wenn keine Datei offen
     */
    function write($string) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= @fputs($this->_fd, $string);
      if (!$result) {
        throw(new IOException('cannot write '.strlen($string).' bytes to '.$this->uri));
      }
      return $result;
    }
    
    /**
     * Check auf <<EOF>>
     *
     * @return  (bool)isEof
     * @throws  IllegalStateException, wenn keine Datei offen
     */
    function eof() {
      if (!isset($this->_fd)) {
        return throw(new IllegalStateException('file not open'));
      }
      return feof($this->_fd);
    }
    
    /**
     * Filepointer bewegen
     *
     * @param  (int)position default 0 Die Position
     * @param  (int)mode default SEEK_SET @see http://php.net/fseek
     * @throws  IOException, wenn der Pointer nicht an die Position gesetzt werden konnte
     * @throws  IllegalStateException, wenn keine Datei offen
     * @return (bool)success
     */
    function seek($position= 0, $mode= SEEK_SET) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= fseek($this->_fd, $position);
      if ($result != 0) return throw(new IOException('seek error, position '.$position.' in mode '.$mode));
      return TRUE;
    }
    
    /**
     * Filepointerposition ermitteln
     *
     * @throws  IllegalStateException, wenn keine Datei offen
     * @throws  IOException, wenn die Position nicht ermittelt werden kann
     * @return  (int)position
     */
    function tell($position= 0, $mode= SEEK_SET) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= ftell($this->_fd);
      if (FALSE === $result) return throw(new IOException('cannot retreive file pointer\'s position'));
      return $result;
    }

    /**
     * Wrapper für flock mit Error-Behandlung
     *
     * @access  private
     * @param   (int)Operation
     * @param   (int)Block
     * @throws  IllegalStateException, wenn keine Datei offen
     * @throws  IOException, wenn die Datei nicht gelockt werden kann
     * @return  (boolean)success
     * @see     http://php.net/flock
     */
    function _lock($op, $block= NULL) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= flock($this->_fd, $op, $lock);
      if (FALSE === $result) return throw(new IOException('cannot retreive file pointer\'s position'));
      return $result;
    }
    
    /**
     * Datei shared-redable locken
     *
     * @see File#_lock
     */
    function lockShared($block= NULL) {
      return $this->_lock(LOCK_SH, $block);
    }
    
    /**
     * Datei exklusiv locken
     *
     * @see File#_lock
     */
    function lockExclusive($block= NULL) {
      return $this->_lock(LOCK_EX, $block);
    }
    
    /**
     * Datei unlocken
     *
     * @see File#_lock
     */
    function unLock() {
      return $this->_lock(LOCK_UN);
    }

    /**
     * Datei schließen
     *
     * @throws  IllegalStateException, wenn keine Datei offen
     * @return  (bool)success
     */
    function close() {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= @fclose($this->_fd);
      unset($this->_fd);
      return $result;
    }
  }
?>
