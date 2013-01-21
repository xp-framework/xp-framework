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
  class NamespacedClassUsingQualifiedUnloaded extends \lang\Object {
    
    /**
     * Returns a namespaced class
     *
     * @return  net.xp_framework.unittest.core.UnloadedNamespacedClass
     */
    public function getNamespacedClass() {
      return new UnloadedNamespacedClass();
    }
  }
?>
