<?php
  /* This class is part of the XP Framework
   *
   * $Id$
   */

  /**
   * Description of CompositeProperties
   *
   * @purpose
   */
  class CompositeProperties extends Object {
    protected $props  = array();

    public function __construct(Properties $p, array $s= array()) {
      $this->props[]= $p;
      if (sizeof($s)) $this->props= array_merge($this->props, $s);
    }

    public function length() {
      return sizeof($this->props);
    }

    public function readString($section, $key, $default= NULL) {
      foreach ($this->props as $p) {
        if (xp::null() !== ($p->readString($section, $key, xp::null()))) {
          return $p->readString($section, $key);
        }
      }

      return $default;
    }
  }

?>
