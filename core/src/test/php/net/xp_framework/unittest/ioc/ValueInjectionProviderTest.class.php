<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'ioc.ValueInjectionProvider');

  /**
   * Unittest
   */
  class ValueInjectionProviderTest extends TestCase {
    /**
     * value injection provider should provide given value
     */
    #[@test]
    public function shouldProvideGivenValue() {
      $valueInjectorProvider = new ValueInjectionProvider('value');
      $this->assertEquals('value', $valueInjectorProvider->get());
    }
  }
?>