<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace net\xp_framework\unittest\core;
  
  use lang\types\String;

  /**
   * Fixture for namespaces tests
   *
   * @see   xp://net.xp_framework.unittest.core.NamespacedClassesTest
   */
  class NamespacedClassUsingUnqualified extends \lang\Object {
    
    /**
     * Returns an empty string
     *
     * @return  lang.types.String
     */
    public function getEmptyString() {
      return String::$EMPTY;
    }
  }
?>
