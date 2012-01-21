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
     * Register imaging module path. This will actually trigger loading it.
     *
     */
    public function setUp() {
      $name= $this->moduleName();
      $this->loader= ClassLoader::getDefault()->registerPath(dirname(__FILE__).'/'.$name);
      $this->fixture= Module::forName($name);
    }

    /**
     * Sets up test case
     *
     */
    public function tearDown() {
      ClassLoader::getDefault()->removeLoader($this->loader);
    }
    
    /**
     * Test getName()
     *
     */
    #[@test]
    public function modules_name() {
      $this->assertEquals($this->moduleName(), $this->fixture->getName());
    }
  }
?>
