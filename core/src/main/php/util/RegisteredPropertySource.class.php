<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.PropertySource');

  /**
   * Memory-based property source
   *
   * @test     xp://net.xp_framework.unittest.RegisteredPropertySourceTest
   */
  class RegisteredPropertySource extends Object implements PropertySource {
    protected
      $name = NULL,
      $prop = NULL;

    /**
     * Constructor
     *
     * @param   string name
     * @param   util.PropertyAccess prop
     */
    public function __construct($name, PropertyAccess $prop) {
      $this->name= $name;
      $this->prop= $prop;
    }

    /**
     * Check for properties
     *
     * @param   string name
     * @return  bool
     */
    public function provides($name) {
      return $name == $this->name;
    }

    /**
     * Retrieve properties
     *
     * @param   string name
     * @return  util.PropertyAccess
     */
    public function fetch($name) {
      if (!$name == $this->name)
        throw new IllegalArgumentException('Access to property source under wrong name "'.$name.'"');

      return $this->prop;
    }

    /**
     * Returns hashcode for this source
     *
     * @return  string
     */
    public function hashCode() {
      return md5($this->name.serialize($this->prop));
    }

    /**
     * Compare against other object
     *
     * @param   util.RegisteredPropertySource cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self &&
        $cmp->name == $this->name &&
        $this->prop->equals($cmp->prop)
      ;
    }
  }
?>
