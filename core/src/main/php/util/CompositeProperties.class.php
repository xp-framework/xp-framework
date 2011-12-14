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
  }

?>
