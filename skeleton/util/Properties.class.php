<?php
/* Diese Klasse ist Teil des XP-Frameworks
 * 
 * $Id$
 */

  uses('io.IOException', 'io.File');
  
  /**
   * Kapselt das Property-File
   *
   * Property-Files sind wie folgt aufgebaut
   * <pre>
   * [Sektion]
   * Key=Wert
   * Key2="Wert"
   * ; Kommentar
   * [Sektion2]
   * Key=Wert2
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
      Object::__construct();
    }
    
    /**
     * Das Property-File anlegen
     *
     * @access  public
     */
    function create() {
      $fd= new File($this->_file);
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
      $fd= new File($this->_file);
      $fd->open(FILE_MODE_WRITE);
      
      // Sektionen durchgehen
      foreach ($this->_data as $section=> $values) {
        $fd->write(sprintf("[%s]\n", $section));
        
        // Werte einer Sektion
        foreach ($values as $key=> $val) {
          if (';' == $key{0}) {
            $fd->write(sprintf("\n; %s\n", $val)); 
          } else {
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
      return (
        strcasecmp('on', $this->_data[$section][$key]) ||
        strcasecmp('yes', $this->_data[$section][$key]) ||
        strcasecmp('true', $this->_data[$section][$key])
      );
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
     * Einen Boolean-Werz hinzufügen. Fügt bei Bedarf auch die Sektion ein
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
