<?php
/* Diese Klasse ist Teil des XP-Frameworks
 * 
 * $Id$
 */

  uses('io.IOException');
  
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
     * Properties aus Datei laden, falls nötig
     *
     * @access  private
     * @throws  IOException, wenn der Dateiname nicht gefunden werden kann
     */
    function _load() {
      if (NULL != $this->_data) return;
      
      $this->_data= parse_ini_file($this->_file, 1);
      if (FALSE === $this->_data) return throw(new IOException($this->_file.' not found'));
    }
    
    /**
     * Properties speichern
     *
     * @access  public
     */
    function save() {
      $fd= fopen($this->_file, 'w');
      foreach ($this->_data as $section=> $values) {
        fputs($fd, sprintf("[%s]\n", $section));
        foreach ($values as $key=> $val) {
          fputs($fd, sprintf(
            "%s=%s\n",
            $key,
            (is_string($val) ? '"'.$val.'"' : $val)
          ));
        }
      }
      fclose($fd);
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
     * Einen Wert als Boolean zurückgeben
     *
     * @access  public
     * @param   string section Name der Sektion
     * @param   string key Name der Keys
     * @param   default default FALSE Die Rückgabe, falls der Key bzw. die Sektion nicht existiert
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

  }
?>
