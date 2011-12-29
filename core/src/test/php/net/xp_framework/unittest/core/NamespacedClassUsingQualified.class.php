<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace net\xp_framework\unittest\core;
  
  /**
   * Fixture for namespaces tests
   *
   * @see   xp://net.xp_framework.unittest.core.NamespacedClassesTest
   */
  class NamespacedClassUsingQualified extends \lang\Object {
    
    /**
     * Returns a namespaced class
     *
     * @return  net.xp_framework.unittest.core.NamespacedClass
     */
    public function getNamespacedClass() {
      return new NamespacedClass();
    }
  }
?>
