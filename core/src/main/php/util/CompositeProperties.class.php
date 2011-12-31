<?php
/* This class is part of the XP Framework
 *
 * $Id$
 */

  uses(
    'util.Properties',
    'util.PropertyAccess'
  );
  
  /**
   * Composite class for util.Properties; can be used to group together
   * arbitrary many Properties objects
   *
   * @test    xp://net.xp_framework.unittest.util.CompositePropertiesTest
   */
  class CompositeProperties extends Object implements PropertyAccess {
    protected $props  = array();
    private $sections = NULL;

    /**
     * Constructor
     *
     * @param   util.Properties p
     * @param   util.Properties[] s default array()
     */
    public function __construct(array $properties) {
      if (empty($properties)) throw new IllegalArgumentException(__CLASS__.' requires at least 1 util.Properties child.');
      foreach ($properties as $p) $this->add($p);
    }

    /**
     * Add another Properties
     *
     * @param   util.Properties a
     */
    public function add(Properties $a) {
      foreach ($this->props as $p) {
        if ($p === $a) return;
        if ($p->equals($a)) return;
      }

      $this->props[]= $a;
      $this->sections= NULL;
    }

    /**
     * Retrieve number of grouped properties
     *
     * @return  int
     */
    public function length() {
      return sizeof($this->props);
    }

    /**
     * Helper method to delegate call
     *
     * @param   string method
     * @param   string section
     * @param   string key
     * @param   mixed default
     * @return  mixed
     */
    private function _read($method, $section, $key, $default) {
      foreach ($this->props as $p) {
        $res= call_user_func_array(array($p, $method), array($section, $key, xp::null()));
        if (xp::null() !== $res) {
          return $res;
        }
      }

      return $default;
    }

    /**
     * Read string value
     *
     * @param   string section
     * @param   string key
     * @param   mixed default default NULL
     * @return  string
     */
    public function readString($section, $key, $default= NULL) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    /**
     * Read bool value
     *
     * @param   string section
     * @param   string key
     * @param   bool default default FALSE
     * @return  bool
     */
    public function readBool($section, $key, $default= FALSE) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    /**
     * Read array value
     *
     * @param   string section
     * @param   string key
     * @param   mixed default default array()
     * @return  string[]
     */
    public function readArray($section, $key, $default= array()) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    /**
     * Read hash value
     *
     * @param   string section
     * @param   string key
     * @param   mixed default default NULL
     * @return  util.Hashmap
     */
    public function readHash($section, $key, $default= NULL) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    /**
     * Read integer value
     *
     * @param   string section
     * @param   string key
     * @param   mixed default default 0
     * @return  int
     */
    public function readInteger($section, $key, $default= 0) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    /**
     * Read float value
     *
     * @param   string section
     * @param   string key
     * @param   mixed default default array()
     * @return  double
     */
    public function readFloat($section, $key, $default= array()) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    /**
     * Read range value
     *
     * @param   string section
     * @param   string key
     * @param   mixed default default 0.0
     * @return  int[]
     */
    public function readRange($section, $key, $default= 0.0) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    /**
     * Read section
     *
     * @param   string section
     * @param   mixed default default array()
     * @return  [:string]
     */
    public function readSection($section, $default= array()) {
      $result= array(); $sectionFound= FALSE;
      foreach (array_reverse($this->props) as $p) {
        if (!$p->hasSection($section)) continue;
        $sectionFound= TRUE;
        $result= array_merge($result, $p->readSection($section));
      }

      if (!$sectionFound) return $default;
      return $result;
    }

    /**
     * Test whether a given section exists
     *
     * @param   string section
     * @return  bool
     */
    public function hasSection($section) {
      foreach ($this->props as $p) {
        if (TRUE === $p->hasSection($section)) return TRUE;
      }

      return FALSE;
    }

    /**
     * Retrieve first section name, set internal pointer
     *
     * @return  string
     */
    public function getFirstSection() {

      // Lazy initialize - for subsequent loops
      if (NULL === $this->sections) {
        $this->sections= array();
        foreach ($this->props as $p) {
          $section= $p->getFirstSection();
          while ($section) {
            $this->sections[$section]= TRUE;
            $section= $p->getNextSection();
          }
        }
      }

      reset($this->sections);
      return key($this->sections);
    }

    /**
     * Retrieve next section name, NULL if no more sections exist
     *
     * @return  string
     */
    public function getNextSection() {
      if (!is_array($this->sections)) return NULL;
      next($this->sections);
      return key($this->sections);
    }
  }
?>
