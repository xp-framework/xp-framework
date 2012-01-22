<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.reflect.Module'
  );

  /**
   * TestCase
   *
   */
  class CoreModuleTest extends TestCase {
    protected $fixture= NULL;

  
    /**
     * Retrieve "core" module
     *
     */
    public function setUp() {
      $this->fixture= Module::forName('core');
    }

    /**
     * Test getName()
     *
     */
    #[@test]
    public function modules_name() {
      $this->assertEquals('core', $this->fixture->getName());
    }

    /**
     * Test getClassLoader()
     *
     */
    #[@test]
    public function modules_loader() {
      $this->assertInstanceOf('lang.IClassLoader', $this->fixture->getClassLoader());
    }
  }
?>
