<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'lang.Module');

  /**
   * TestCase
   *
   * @see xp://lang.Module
   */
  abstract class AbstractModuleTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Return module name
     *
     * @return  string
     */
    protected abstract function moduleName();

    /**
     * Return module version
     *
     * @return  string
     */
    protected abstract function moduleVersion();
  
    /**
     * Register module path. This will actually trigger loading it.
     *
     */
    public function setUp() {
      $this->fixture= ClassLoader::getDefault()->registerPath(
        dirname(__FILE__).'/../../../../../modules/'.$this->moduleName()
      );
    }

    /**
     * Sets up test case
     *
     */
    public function tearDown() {
      ClassLoader::removeLoader($this->fixture);
    }
    
    /**
     * Test getName()
     *
     */
    #[@test]
    public function modules_name() {
      $this->assertEquals($this->moduleName(), $this->fixture->getName());
    }

    /**
     * Test getVersion()
     *
     */
    #[@test]
    public function modules_version() {
      $this->assertEquals($this->moduleVersion(), $this->fixture->getVersion());
    }

    /**
     * Test getClassLoader()
     *
     */
    #[@test]
    public function modules_loader() {
      $this->assertInstanceOf('lang.IClassLoader', $this->fixture->getClassLoader());
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation() {
      $this->assertEquals(
        'Module<'.$this->moduleName().':'.$this->moduleVersion().', '.$this->fixture->getClassLoader()->toString().'>',
        $this->fixture->toString()
      );
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function hashcode_value() {
      $this->assertEquals(
        'module@'.$this->moduleName().$this->moduleVersion(),
        $this->fixture->hashCode()
      );
    }


    /**
     * Test equals()
     *
     */
    #[@test]
    public function differing_modules_not_equal() {
      $this->assertNotEquals(Module::forName('core'), $this->fixture);
    }
  }
?>
