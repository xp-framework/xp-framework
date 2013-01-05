<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses(
    'io.IOException',
    'io.File',
    'util.PropertyAccess',
    'io.streams.InputStream',
    'io.streams.MemoryInputStream',
    'io.streams.MemoryOutputStream',
    'io.streams.FileInputStream',
    'io.streams.TextReader',
    'text.TextTokenizer',
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
   * @test    xp://net.xp_framework.unittest.util.PropertyWritingTest
   * @test    xp://net.xp_framework.unittest.util.StringBasedPropertiesTest
   * @test    xp://net.xp_framework.unittest.util.FileBasedPropertiesTest
   * @see     php://parse_ini_file
   */
  class Properties extends Object implements PropertyAccess {
    public
      $_file    = '',
      $_data    = NULL;

    /**
     * Constructor
     *
     * @param   string filename
     */
    public function __construct($filename) {
      $this->_file= $filename;
    }

    /**
     * Load from an input stream, e.g. a file
     *
     * @param   io.streams.InputStream in
     * @param   string charset the charset the stream is encoded in or NULL to trigger autodetection by BOM
     * @throws  io.IOException
     * @throws  lang.FormatException
     */
    public function load(InputStream $in, $charset= NULL) {
      $s= new TextTokenizer(new TextReader($in, $charset), "\r\n");
      $this->_data= array();
      $section= NULL;
      while ($s->hasMoreTokens()) {
        $t= $s->nextToken();
        $trimmedToken=trim($t);
        if ('' === $trimmedToken) continue;                // Empty lines
        $c= $trimmedToken{0};
        if (';' === $c || '#' === $c) {                            // One line comments
          continue;                    
        } else if ('[' === $c) {
          if (FALSE === ($p= strrpos($trimmedToken, ']'))) {
            throw new FormatException('Unclosed section "'.$trimmedToken.'"');
          }
          $section= substr($trimmedToken, 1, $p- 1);
          $this->_data[$section]= array();
        } else if (FALSE !== ($p= strpos($t, '='))) {
          $key= trim(substr($t, 0, $p));
          $value= ltrim(substr($t, $p+ 1));
          if (strlen($value) && ('"' === ($q= $value{0}))) {       // Quoted strings
            if (FALSE === ($p= strrpos($value, $q, 1))) {
              $value= substr($value, 1)."\n".$s->nextToken($q);
            } else {
              $value= substr($value, 1, $p- 1);
            }
          } else {        // unquoted string
            if (FALSE !== ($p= strpos($value, ';'))) {        // Comments at end of line
              $value= substr($value, 0, $p);
            }
            $value= rtrim($value);
          }
          

          // Arrays and maps: key[], key[0], key[assoc]
          if (']' === substr($key, -1)) {
            if (FALSE === ($p= strpos($key, '['))) {
              throw new FormatException('Invalid key "'.$key.'"');
            }
            $offset= substr($key, $p+ 1, -1);
            $key= substr($key, 0, $p);
            if (!isset($this->_data[$section][$key])) {
              $this->_data[$section][$key]= array();
            }
            if ('' === $offset) {
              $this->_data[$section][$key][]= $value;
            } else {
              $this->_data[$section][$key][$offset]= $value;
            }
          } else {
            $this->_data[$section][$key]= $value;
          }
        } else if ('' !== trim($t)) {
          throw new FormatException('Invalid line "'.$t.'"');
        }
      }
    }

    /**
     * Store to an output stream, e.g. a file
     *
     * @param   io.streams.OutputStream out
     * @throws  io.IOException
     */
    public function store(OutputStream $out) {
      foreach (array_keys($this->_data) as $section) {
        $out->write(sprintf("[%s]\n", $section));
        
        foreach ($this->_data[$section] as $key => $val) {
          if (';' == $key{0}) {
            $out->write(sprintf("\n; %s\n", $val)); 
          } else {
            if ($val instanceof Hashmap) {
              $str= '';
              foreach ($val->keys() as $k) {
                $str.= '|'.$k.':'.$val->get($k);
              }
              $val= (string)substr($str, 1);
            } 
            if (is_array($val)) $val= implode('|', $val);
            if (is_string($val)) $val= '"'.$val.'"';
            $out->write(sprintf(
              "%s=%s\n",
              $key,
              strval($val)
            ));
          }
        }
        $out->write("\n");
      }
    }
    
    /**
     * Create a property file from an io.File object
     *
     * @deprecated  Use load() method instead
     * @param   io.File file
     * @return  util.Properties
     * @throws  io.IOException in case the file given does not exist
     */
    public static function fromFile(File $file) {
      $self= new self($file->getURI());
      $self->load($file->getInputStream());
      return $self;
    }

    /**
     * Create a property file from a string
     *
     * @deprecated  Use load() method instead
     * @param   string str
     * @return  util.Properties
     */
    public static function fromString($str) {
      $self= new self(NULL);
      $self->load(new MemoryInputStream($str));
      return $self;
    }

    /**
     * Retrieves the file name containing the properties
     *
     * @return  string
     */
    public function getFilename() {
      return $this->_file;
    }
    
    /**
     * Create the property file
     *
     * @throws  io.IOException if the property file could not be created
     */
    public function create() {
      if (NULL !== $this->_file) {
        $fd= new File($this->_file);
        $fd->open(FILE_MODE_WRITE);
        $fd->close();
      }
      $this->_data= array();
    }
    
    /**
     * Returns whether the property file exists
     *
     * @return  bool
     */
    public function exists() {
      return file_exists($this->_file);
    }
    
    /**
     * Helper method that loads the data from the file if needed
     *
     * @param   bool force default FALSE
     * @throws  io.IOException
     */
    protected function _load($force= FALSE) {
      if (!$force && NULL !== $this->_data) return;
      $this->load(new FileInputStream($this->_file));
    }
    
    /**
     * Reload all data from the file
     *
     */
    public function reset() {
      return $this->_load(TRUE);
    }
    
    /**
     * Save properties to the file
     *
     * @deprecated  Use store() method instead
     * @throws  io.IOException if the property file could not be written
     */
    public function save() {
      $fd= new File($this->_file);
      $this->store($fd->getOutputStream());
      $fd->close();
    }

    /**
     * Get the first configuration section
     *
     * @see     xp://util.Properties#getNextSection
     * @return  string the first section's name
     */
    public function getFirstSection() {
      $this->_load();
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
     * @return  var string section or FALSE if this was the last section
     */
    public function getNextSection() {
      $this->_load();
      if (FALSE === next($this->_data)) return FALSE;

      return key($this->_data);
    }
    
    /**
     * Read an entire section into an array
     *
     * @param   string name
     * @param   var[] default default array() what to return in case the section does not exist
     * @return  array
     */
    public function readSection($name, $default= array()) {
      $this->_load();
      return isset($this->_data[$name]) 
        ? $this->_data[$name] 
        : $default
      ;
    }
    
    /**
     * Read a value as string
     *
     * @param   string section
     * @param   string key
     * @param   string default default '' what to return in case the section or key does not exist
     * @return  string
     */ 
    public function readString($section, $key, $default= '') {
      $this->_load();
      return isset($this->_data[$section][$key])
        ? $this->_data[$section][$key]
        : $default
      ;
    }
    
    /**
     * Read a value as array
     *
     * @param   string section
     * @param   string key
     * @param   var[] default default NULL what to return in case the section or key does not exist
     * @return  array
     */
    public function readArray($section, $key, $default= array()) {
      $this->_load();

      // New: key[]="a" or key[0]="a"
      // Old: key="" (an empty array) or key="a|b|c"
      if (!isset($this->_data[$section][$key])) {
        return $default;
      } else if (is_array($this->_data[$section][$key])) {
        return $this->_data[$section][$key];
      } else {
        return '' == $this->_data[$section][$key] ? array() : explode('|', $this->_data[$section][$key]);
      }
    }
    
    /**
     * Read a value as hash
     *
     * @param   string section
     * @param   string key
     * @param   util.Hashmap default default NULL what to return in case the section or key does not exist
     * @return  util.Hashmap
     */
    public function readHash($section, $key, $default= NULL) {
      $this->_load();

      // New: key[color]="green" and key[make]="model"
      // Old: key="color:green|make:model"
      if (!isset($this->_data[$section][$key])) {
        return $default;
      } else if (is_array($this->_data[$section][$key])) {
        return new Hashmap($this->_data[$section][$key]);
      } else {
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
    }

    /**
     * Read a value as range
     *
     * @param   string section
     * @param   string key
     * @param   int[] default default NULL what to return in case the section or key does not exist
     * @return  array
     */
    public function readRange($section, $key, $default= array()) {
      $this->_load();
      if (!isset($this->_data[$section][$key])) return $default;
      
      list($min, $max)= explode('..', $this->_data[$section][$key]);
      return range((int)$min, (int)$max);
    }
    
    /**
     * Read a value as integer
     *
     * @param   string section
     * @param   string key
     * @param   int default default 0 what to return in case the section or key does not exist
     * @return  int
     */ 
    public function readInteger($section, $key, $default= 0) {
      $this->_load();
      return isset($this->_data[$section][$key])
        ? intval($this->_data[$section][$key])
        : $default
      ;
    }

    /**
     * Read a value as float
     *
     * @param   string section
     * @param   string key
     * @param   float default default 0.0 what to return in case the section or key does not exist
     * @return  float
     */ 
    public function readFloat($section, $key, $default= 0.0) {
      $this->_load();
      return isset($this->_data[$section][$key])
        ? doubleval($this->_data[$section][$key])
        : $default
      ;
    }

    /**
     * Read a value as boolean
     *
     * @param   string section
     * @param   string key
     * @param   bool default default FALSE what to return in case the section or key does not exist
     * @return  bool TRUE, when key is 1, 'on', 'yes' or 'true', FALSE otherwise
     */ 
    public function readBool($section, $key, $default= FALSE) {
      $this->_load();
      if (!isset($this->_data[$section][$key])) return $default;
      return (
        '1' === $this->_data[$section][$key] ||
        0   === strncasecmp('yes', $this->_data[$section][$key], 3) ||
        0   === strncasecmp('true', $this->_data[$section][$key], 4) ||
        0   === strncasecmp('on', $this->_data[$section][$key], 2)
      );
    }
    
    /**
     * Returns whether a section exists
     *
     * @param   string name
     * @return  bool
     */
    public function hasSection($name) {
      $this->_load();
      return isset($this->_data[$name]);
    }

    /**
     * Add a section
     *
     * @param   string name
     * @param   bool overwrite default FALSE whether to overwrite existing sections
     * @return  string name
     */
    public function writeSection($name, $overwrite= FALSE) {
      $this->_load();
      if ($overwrite || !$this->hasSection($name)) $this->_data[$name]= array();
      return $name;
    }
    
    /**
     * Add a string (and the section, if necessary)
     *
     * @param   string section
     * @param   string key
     * @param   string value
     */
    public function writeString($section, $key, $value) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= (string)$value;
    }
    
    /**
     * Add a string (and the section, if necessary)
     *
     * @param   string section
     * @param   string key
     * @param   int value
     */
    public function writeInteger($section, $key, $value) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= (int)$value;
    }
    
    /**
     * Add a float (and the section, if necessary)
     *
     * @param   string section
     * @param   string key
     * @param   float value
     */
    public function writeFloat($section, $key, $value) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= (float)$value;
    }

    /**
     * Add a boolean (and the section, if necessary)
     *
     * @param   string section
     * @param   string key
     * @param   bool value
     */
    public function writeBool($section, $key, $value) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= $value ? 'yes' : 'no';
    }
    
    /**
     * Add an array string (and the section, if necessary)
     *
     * @param   string section
     * @param   string key
     * @param   array value
     */
    public function writeArray($section, $key, $value) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][$key]= $value;
    }

    /**
     * Add a hashmap (and the section, if necessary)
     *
     * @param   string section
     * @param   string key
     * @param   var value either a util.Hashmap or an array
     */
    public function writeHash($section, $key, $value) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      if ($value instanceof Hashmap) {
        $this->_data[$section][$key]= $value;
      } else {
        $this->_data[$section][$key]= new Hashmap($value);
      }
    }
    
    /**
     * Add a comment (and the section, if necessary)
     *
     * @param   string section
     * @param   string key
     * @param   string comment
     */
    public function writeComment($section, $comment) {
      $this->_load();
      if (!$this->hasSection($section)) $this->_data[$section]= array();
      $this->_data[$section][';'.sizeof($this->_data[$section])]= $comment;
    }
    
    /**
     * Remove section completely
     *
     * @param   string section
     * @throws  lang.IllegalStateException if given section does not exist
     */
    public function removeSection($section) {
      $this->_load();
      if (!isset($this->_data[$section])) throw new IllegalStateException('Cannot remove nonexistant section "'.$section.'"');
      unset($this->_data[$section]);
    }

    /**
     * Remove key
     *
     * @param   string section
     * @param   string key
     * @throws  lang.IllegalStateException if given key does not exist
     */
    public function removeKey($section, $key) {
      $this->_load();
      if (!isset($this->_data[$section][$key])) throw new IllegalStateException('Cannot remove nonexistant key "'.$key.'" in "'.$section.'"');
      unset($this->_data[$section][$key]);
    }

    /**
     * Check if is equal to other object
     *
     * @param   var $cmp
     * @return  bool
     */
    public function equals($cmp) {
      if (!$cmp instanceof self) return FALSE;

      if ($this->_data && $cmp->_data) {
        return $this->_data == $cmp->_data;
      }

      // If based on files, and both base on same file, then they're equal
      if ($this->_file && $cmp->_file) {
        return $this->_file === $cmp->_file;
      }

      // Bordercase
      if (
        $this->_file === NULL &&
        $cmp->_file === NULL &&
        $this->_data === NULL &&
        $cmp->_data === NULL
      ) {
        return TRUE;
      }

      return FALSE;
    }

    /**
     * Creates a string representation of this property file
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->_file.')@{'.xp::stringOf($this->_data).'}';
    }
  }
?>
