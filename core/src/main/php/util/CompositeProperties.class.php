<?php
/* This class is part of the XP Framework
 *
 * $Id$
 */

  uses(
    'util.Properties',
    'util.PropertyProvider'
  );
  
  /**
   * Description of CompositeProperties
   *
   * @purpose
   */
  class CompositeProperties extends Object implements PropertyProvider {
    protected $props  = array();

    public function __construct(Properties $p, array $s= array()) {
      $this->props[]= $p;
      if (sizeof($s)) $this->props= array_merge($this->props, $s);
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
  }

?>
