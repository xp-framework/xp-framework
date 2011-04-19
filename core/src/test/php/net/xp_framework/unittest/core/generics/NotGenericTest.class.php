<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.types.String'
  );

  /**
   * TestCase for reflection on a non-generic
   *
   */
  class NotGenericTest extends TestCase {
    
    /**
     * Test isGeneric()
     *
     */
    #[@test]
    public function thisIsNotAGeneric() {
      $this->assertFalse($this->getClass()->isGeneric());
    }

    /**
     * Test isGenericDefinition()
     *
     */
    #[@test]
    public function thisIsNotAGenericDefinition() {
      $this->assertFalse($this->getClass()->isGenericDefinition());
    }

    /**
     * Test newGenericType()
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function cannotCreateGenericTypeFromThis() {
      $this->getClass()->newGenericType(array());
    }

    /**
     * Test genericArguments()
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function cannotGetGenericArgumentsForThis() {
      $this->getClass()->genericArguments();
    }

    /**
     * Test genericComponents()
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function cannotGetGenericComponentsForThis() {
      $this->getClass()->genericComponents();
    }
  }
?>
