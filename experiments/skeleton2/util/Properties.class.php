<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses(
    'io.IOException',
    'io.File',
    'util.Hashmap'
  );

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
   *
   * @purpose  Wrapper around parse_ini_file
   */
  class Properties extends Object {
    public
      $_file    = '',
      $_data    = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string filename
     */
    public function __construct($filename) {
      $this->_file= $filename;
      
    }
    
    /**
     * Retrieves the file name containing the properties
     *
     * @access  public
     * @return  string
     */
    public function getFilename() {
      return $this->_file;
    }
    
    /**
     * Create the property file
     *
     * @access  public
     * @throws  io.IOException if the property file could not be created
     */
    public function create() {
      $fd= new File($this->_file);
      $fd->open(FILE_MODE_WRITE);
      $fd->close();
    }
    
    /**
     * Returns whether the property file exists
     *
     * @access  public
     * @return  bool
     */
    public function exists() {
      return file_exists($this->_file);
    }
    
    /**
     * Helper method that loads the data from the file if needed
     *
     * @access  private
     * @param   bool force default FALSE
     * @throws  io.IOException
     */
    private function _load($force= FALSE) {
      if (!$force && NULL != $this->_data) return;
      if (FALSE === ($this->_data= parse_ini_file($this->_file, 1))) {
        throw (new IOException('The file "'.$this->_file.'" could not be read'));
      }
    }
    
    /**
     * Reload all data from the file
     *
     * @access  public
     */
    public function reset() {
      return self::_load(TRUE);
    }
    
    /**
     * Save properties to the file
     *
     * @access  public
     * @throws  io.IOException if the property file could not be written
     */
    public function save() {
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
     * Get the first configuration section
     *
     * @see     xp://util.Properties#getNextSection
     * @access  public
     * @return  string the first section's name
     */
    public function getFirstSection() {
      self::_load();
      reset($this->_data);
      return key($this->_data);
    }
    
    /**
     * Get the next configuration section
     *
     * Example:
     * <code>
     *   if ($section= $prop->getFirstSection()) do {
     *     var_dump($section, $prop->readSection($section));
     *   } while ($section= $prop->getNextSection());
     * </code>
     *
     * @see     xp://util.Properties#getFirstSection
     * @access  public
     * @return  mixed string section or FALSE if this was the last section
     */
    public function getNextSection() {
      self::_load();
      if (!next($this->_data)) return FALSE;

      return key($this->_data);
    }
    
    /**
     * Read an entire section into an array
     *
     * @access  public
     * @param   string name
     * @param   default default array() what to return in case the section does not exist
     * @return  array
     */
    public function readSection($name, $default= array()) {
      self::_load();
      return isset($this->_data[$name]) 
        ? $this->_data[$name] 
        : $default
      ;
    }
    
    /**
     * Read a value as string
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   default default '' what to return in case the section or key does not exist
     * @return  string
     */ 
    public function readString($section, $key, $default= '') {
      self::_load();
      return isset($this->_data[$section][$key])
        ? $this->_data[$section][$key]
        : $default
      ;
    }
    
    /**
     * Read a value as array
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   default default NULL what to return in case the section or key does not exist
     * @return  array
     */
    public function readArray($section, $key, $default= array()) {
      self::_load();
      return isset($this->_data[$section][$key])
        ? explode('|', $this->_data[$section][$key])
        : $default
      ;
    }
    
    /**
     * Read a value as hash
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   default default NULL what to return in case the section or key does not exist
     * @return  &util.Hashmap
     */
    public function readHash($section, $key, $default= NULL) {
      self::_load();
      if (!isset($this->_data[$section][$key])) return $default;
      
      $return= array();
      foreach (explode('|', $this->_data[$section][$key]) as $val) {
        if (strstr($val, ':')) {
          list($k, $v)= explode(':', $val, 2);
          $return[$k]= $v;
        } else {
          $return[]= $val;
        } 
      }
      
      return new Hashmap($return);
    }

    /**
     * Read a value as range
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   default default NULL what to return in case the section or key does not exist
     * @return  array
     */
    public function readRange($section, $key, $default= array()) {
      self::_load();
      if (!isset($this->_data[$section][$key])) return $default;
      
      list($min, $max)= explode('..', $this->_data[$section][$key]);
      return range((int)$min, (int)$max);
    }
    
    /**
     * Read a value as integer
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   default default 0 what to return in case the section or key does not exist
     * @return  int
     */ 
    public function readInteger($section, $key, $default= 0) {
      self::_load();
      return isset($this->_data[$section][$key])
        ? intval($this->_data[$section][$key])
        : $default
      ;
    }

    /**
     * Read a value as float
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   float default default 0.0 what to return in case the section or key does not exist
     * @return  float
     */ 
    public function readFloat($section, $key, $default= 0.0) {
      self::_load();
      return isset($this->_data[$section][$key])
        ? doubleval($this->_data[$section][$key])
        : $default
      ;
    }

    /**
     * Read a value as boolean
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   int default default FALSE what to return in case the section or key does not exist
     * @return  bool TRUE, when key is 1, 'on', 'yes' or 'true', FALSE otherwise
     */ 
    public function readBool($section, $key, $default= FALSE) {
      self::_load();
      if (!isset($this->_data[$section][$key])) return $default;
      return ('1' === $this->_data[$section][$key]);
    }
    
    /**
     * Returns whether a section exists
     *
     * @access  public
     * @param   string name
     * @return  bool
     */
    public function hasSection($name) {
      self::_load();
      return isset($this->_data[$name]);
    }

    /**
     * Add a section
     *
     * @access  public
     * @param   string name
     * @param   bool overwrite default FALSE whether to overwrite existing sections
     * @return  string name
     */
    public function writeSection($name, $overwrite= FALSE) {
      self::_load();
      if ($overwrite || !self::hasSection($name)) $this->_data[$name]= array();
      return $name;
    }
    
    /**
     * Add a string (and the section, if necessary)
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   string value
     */
    public function writeString($section, $key, $value) {
      self::_load();
      if (!self::hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= (string)$value;
    }
    
    /**
     * Add a string (and the section, if necessary)
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   int value
     */
    public function writeInteger($section, $key, $value) {
      self::_load();
      if (!self::hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= (int)$value;
    }
    
    /**
     * Add a float (and the section, if necessary)
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   float value
     */
    public function writeFloat($section, $key, $value) {
      self::_load();
      if (!self::hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= (float)$value;
    }

    /**
     * Add a boolean (and the section, if necessary)
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   bool value
     */
    public function writeBool($section, $key, $value) {
      self::_load();
      if (!self::hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= $value ? 'yes' : 'no';
    }
    
    /**
     * Add an array string (and the section, if necessary)
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   array value
     */
    public function writeArray($section, $key, $value) {
      self::_load();
      if (!self::hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= $value;
    }

    /**
     * Add a hashmap (and the section, if necessary)
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   mixed value either a util.Hashmap or an array
     */
    public function writeHash($section, $key, $value) {
      self::_load();
      if (!self::hasSection($section)) $this->_data[$section]= array();
      if (is_a($value, 'Hashmap')) {
        $this->_data[$section][$key]= $value;
      } else {
        $this->_data[$section][$key]= new Hashmap($value);
      }
    }
    
    /**
     * Add a comment (and the section, if necessary)
     *
     * @access  public
     * @param   string section
     * @param   string key
     * @param   string value
     */
    public function writeComment($section, $comment) {
      self::_load();
      if (!self::hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][';'.sizeof($this->_data[$section])]= $comment;
    }
  }
?>
