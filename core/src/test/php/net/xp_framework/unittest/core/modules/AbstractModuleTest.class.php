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
     * Return packages provided by module. Nulls 
     *
     * @return  string[]
     */
    protected function moduleProvides() {
      return array('**');
    }
  
    /**
     * Register module path. This will actually trigger loading it.
     *
     */
    public function setUp() {
      $this->fixture= ClassLoader::getDefault()->registerPath(
        __DIR__.'/../../../../../modules/'.$this->moduleName()
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
    public function modules_delegates() {
      $this->assertInstanceOf('lang.IClassLoader[]', $this->fixture->getDelegates());
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation() {
      $v= $this->moduleVersion();
      $this->assertEquals(
        'Module<'.$this->moduleName().(NULL === $v ? '' : ':'.$v).">@{\n".
        '  '.implode(', ', $this->moduleProvides()).' @'.$this->fixture->getDelegate(NULL)->toString()."\n".
        '}',
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
  }
?>
