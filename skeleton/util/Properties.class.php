<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses('io.IOException', 'io.File', 'util.Hashmap');
  
  /**
   * An interface to property-files (aka "ini-files")
   *
   * Property-files syntax is easy.
   * <pre>
   * [section]
   * key1=value
   * key2="value"
   * key3="value|value|value"
   * key4="a:value|b:value"
   * ; comment
   *
   * [section2]
   * key=value
   * </pre>
   */
  class Properties extends Object {
    var
      $_file,
      $_data= NULL;
      
    /**
     * Constructor
     *
     * @param   string filename Dateiname (.ini-Datei)
     */
    function __construct($filename) {
      $this->_file= $filename;
      parent::__construct();
    }
    
    /**
     * Das Property-File anlegen
     *
     * @access  public
     */
    function create() {
      $fd= &new File($this->_file);
      $fd->open(FILE_MODE_WRITE);
      $fd->close();
    }
    
    /**
     * Gibt zurück, ob das Property-File existiert
     *
     * @access  public
     * @return  bool Existiert
     */
    function exists() {
      return file_exists($this->_file);
    }
    
    /**
     * Properties aus Datei laden, falls nötig
     *
     * @access  private
     * @throws  IOException, wenn der Dateiname nicht gefunden werden kann
     */
    function _load($force= FALSE) {
      if (!$force && NULL != $this->_data) return;
      
      $this->_data= parse_ini_file($this->_file, 1);
      if (FALSE === $this->_data) return throw(new IOException($this->_file.' not found'));
    }
    
    /**
     * Das Property-File neu einlesen
     *
     * @access  public
     */
    function reset() {
      return $this->_load(TRUE);
    }
    
    /**
     * Properties speichern
     *
     * @access  public
     */
    function save() {
      $fd= &new File($this->_file);
      $fd->open(FILE_MODE_WRITE);
      
      // Sektionen durchgehen
      foreach ($this->_data as $section=> $values) {
        $fd->write(sprintf("[%s]\n", $section));
        
        // Werte einer Sektion
        foreach ($values as $key=> $val) {
          if (';' == $key{0}) {
            $fd->write(sprintf("\n; %s\n", $val)); 
          } else {
            if (is_a($val, 'Hashmap')) {
              $str= '';
              foreach ($val->_hash as $k=> $v) {
                $str.= '|'.$k.':'.$v;
              }
              $val= substr($str, 1);
            }
            if (is_array($val)) $val= implode('|', $val);
            if (is_string($val)) $val= '"'.$val.'"';
            $fd->write(sprintf(
              "%s=%s\n",
              $key,
              strval($val)
            ));
          }
        }
        $fd->write("\n");
      }
      $fd->close();
    }

    /**
     * Die erste Sektion der Properties holen
     *
     * @access  public
     * @return  string Sektionsname
     */
    function getFirstSection() {
      $this->_load();
      reset($this->_data);
      return key($this->_data);
    }
    
    /**
     * Die nächste Sektion der Properties holen
     *
     * @access  public
     * @return  mixed Sektionsname, bzw. FALSE, wenn keine weiteren existieren
     */
    function getNextSection() {
      $this->_load();
      if (!next($this->_data)) return FALSE;
      return key($this->_data);
    }
    
    /**
     * Eine ganze Sektion zurückgeben
     *
     * @access  public
     * @param   string name Name der Sektion
     * @param   default default NULL Die Rückgabe, falls die Sektion nicht existiert
     * @return  mixed Sektion als assoziativer Array, bzw. $default
     */
    function readSection($name, $default= NULL) {
      $this->_load();
      return isset($this->_data[$name]) 
        ? $this->_data[$name] 
        : $default
      ;
    }
    
    /**
     * Einen Wert als String zurückgeben
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   default default '' Die Rückgabe, falls der Key bzw. die Sektion nicht existiert
     * @return  mixed Value-String, bzw. $default
     */ 
    function readString($section, $key, $default= '') {
      $this->_load();
      return isset($this->_data[$section][$key])
        ? $this->_data[$section][$key]
        : $default
      ;
    }
    
    /**
     * Einen Wert als Array zurückgeben. Arrays liegen als foo|bar|baz vor
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   default default NULL Die Rückgabe, falls der Key bzw. die Sektion nicht existiert
     * @return  mixed Value-Array, bzw. $default
     */
    function readArray($section, $key, $default= NULL) {
      $this->_load();
      return isset($this->_data[$section][$key])
        ? explode('|', $this->_data[$section][$key])
        : $default
      ;
    }
    
    /**
     * Return a value as hash
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   default default NULL Die Rückgabe, falls der Key bzw. die Sektion nicht existiert
     * @return  mixed Value-Hash (util.Hashmap), bzw. $default
     */
    function &readHash($section, $key, $default= NULL) {
      $this->_load();
      if (!isset($this->_data[$section][$key])) return $default;
      
      $return= array();
      foreach (explode('|', $this->_data[$section][$key]) as $val) {
        list($k, $v)= explode(':', $val);
        $return[$k]= $v;
      }
      
      return new Hashmap($return);
    }

    /**
     * Einen Wert als Range zurückgeben. Ranges liegen als min..max vor
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   default default NULL Die Rückgabe, falls der Key bzw. die Sektion nicht existiert
     * @return  mixed Value-Array, bzw. $default
     */
    function readRange($section, $key, $default= NULL) {
      $this->_load();
      if (!isset($this->_data[$section][$key])) return $default;
      
      list($min, $max)= explode('..', $this->_data[$section][$key]);
      return range($min, $max);
    }
    
    /**
     * Einen Wert als Integer zurückgeben
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   default default 0 Die Rückgabe, falls der Key bzw. die Sektion nicht existiert
     * @return  mixed Value als Integer, bzw. $default
     */ 
    function readInteger($section, $key, $default= 0) {
      $this->_load();
      return isset($this->_data[$section][$key])
        ? intval($this->_data[$section][$key])
        : $default
      ;
    }

    /**
     * Einen Wert als Float zurückgeben
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   float default default 0.0 Die Rückgabe, falls der Key bzw. die Sektion nicht existiert
     * @return  mixed Value als Integer, bzw. $default
     */ 
    function readFloat($section, $key, $default= 0.0) {
      $this->_load();
      return isset($this->_data[$section][$key])
        ? doubleval($this->_data[$section][$key])
        : $default
      ;
    }

    /**
     * Einen Wert als Boolean zurückgeben
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   int default default FALSE Die Rückgabe, falls der Key bzw. die Sektion nicht existiert
     * @return  mixed TRUE, key 'on', 'yes' oder 'true' ist, FALSE sonst, bzw. $default
     */ 
    function readBool($section, $key, $default= FALSE) {
      $this->_load();
      if (!isset($this->_data[$section][$key])) return $default;
      return ('1' === $this->_data[$section][$key]);
    }
    
    /**
     * Gibt zurück, ob eine Sektion existiert
     *
     * @access  public
     * @param   string name Name der Sektion
     * @return  bool Existiert
     */
    function hasSection($name) {
      $this->_load();
      return isset($this->_data[$name]);
    }

    /**
     * Eine Sektion hinzufügen, falls sie nicht existiert
     *
     * @access  public
     * @param   string name Name der Sektion
     * @return  string Sektionsname
     */
    function writeSection($name, $overwrite= FALSE) {
      $this->_load();
      if ($overwrite || !$this->hasSection($name)) $this->_data[$name]= array();
      return $name;
    }
    
    /**
     * Einen String hinzufügen. Fügt bei Bedarf auch die Sektion ein
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   string value Der Wert
     */
    function writeString($section, $key, $value) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= cast($value, 'string');
    }
    
    /**
     * Einen Integer hinzufügen. Fügt bei Bedarf auch die Sektion ein
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   int value Der Wert
     */
    function writeInteger($section, $key, $value) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= cast($value, 'integer');
    }
    
    /**
     * Einen Float hinzufügen. Fügt bei Bedarf auch die Sektion ein
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   float value Der Wert
     */
    function writeFloat($section, $key, $value) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= cast($value, 'float');
    }

    /**
     * Einen Boolean-Werz hinzufügen. Fügt bei Bedarf auch die Sektion ein
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   bool value Der Wert
     */
    function writeBool($section, $key, $value) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= $value ? 'yes' : 'no';
    }
    
    /**
     * Einen Array hinzufügen. Fügt bei Bedarf auch die Sektion ein
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   array value Der Wert
     */
    function writeArray($section, $key, $value) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= $value;
    }

    /**
     * Add a hashmap
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   array value Der Wert
     */
    function writeHash($section, $key, $value) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      if (is_a($value, 'Hashmap')) {
        $this->_data[$section][$key]= &$value;
      } else {
        $this->_data[$section][$key]= &new Hashmap($value);
      }
    }
    
    /**
     * Einen Kommentar hinzifügen
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string comment Kommentar
     */
    function writeComment($section, $comment) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][';'.sizeof($this->_data[$section])]= $comment;
    }
  }
?>
