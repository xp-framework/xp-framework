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
  abstract class AbstractModuleTest extends TestCase {
    protected $loader= NULL;
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
      $name= $this->moduleName();
      $this->loader= ClassLoader::getDefault()->registerPath(
        dirname(__FILE__).'/../../../../../modules/'.$name
      );
      $this->fixture= Module::forName($name);
    }

    /**
     * Sets up test case
     *
     */
    public function tearDown() {
      ClassLoader::removeModule($this->fixture);
      ClassLoader::removeLoader($this->loader);
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
      $this->assertEquals($this->loader, $this->fixture->getClassLoader());
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation() {
      $this->assertEquals(
        'lang.reflect.Module<'.$this->moduleName().':'.$this->moduleVersion().'>',
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
        'module'.$this->moduleName().$this->moduleVersion(),
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