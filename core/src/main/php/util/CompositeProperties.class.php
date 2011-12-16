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
   * Description of CompositeProperties
   *
   * @purpose
   */
  class CompositeProperties extends Object implements PropertyAccess {
    protected $props  = array();
    private $sections = NULL;

    public function __construct(Properties $p, array $s= array()) {
      $this->props[]= $p;
      if (sizeof($s)) $this->props= array_merge($this->props, $s);
    }

    public function add(Properties $a) {
      foreach ($this->props as $p) {
        if ($p === $a) return;
        if ($p->equals($a)) return;
      }

      $this->props[]= $a;
      $this->sections= NULL;
    }

    public function length() {
      return sizeof($this->props);
    }

    private function _read($method, $section, $key, $default) {
      foreach ($this->props as $p) {
        $res= call_user_func_array(array($p, $method), array($section, $key, xp::null()));
        if (xp::null() !== $res) {
          return $res;
        }
      }

      return $default;
    }

    public function readString($section, $key, $default= NULL) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    public function readBool($section, $key, $default= FALSE) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    public function readArray($section, $key, $default= array()) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    public function readHash($section, $key, $default= NULL) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    public function readInteger($section, $key, $default= 0) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    public function readFloat($section, $key, $default= 0.0) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

    public function readRange($section, $key, $default= 0.0) {
      return $this->_read(__FUNCTION__, $section, $key, $default);
    }

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

    public function hasSection($section) {
      foreach ($this->props as $p) {
        if (TRUE === $p->hasSection($section)) return TRUE;
      }

      return FALSE;
    }

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

    public function getNextSection() {
      if (!is_array($this->sections)) return NULL;
      next($this->sections);
      return key($this->sections);
    }
  }
?>
