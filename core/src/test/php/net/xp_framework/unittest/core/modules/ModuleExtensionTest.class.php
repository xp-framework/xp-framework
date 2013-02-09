<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

  /**
   * TestCase
   *
   */
  class ModuleExtensionTest extends TestCase {
    protected $parent= NULL;

    /**
     * Helper method to get module path
     *
     * @param  string name the module's name
     * @return string
     */
    protected function modulePath($name) {
      return __DIR__.'/../../../../../modules/'.$name;
    }

    /**
     * Register module path. This will actually trigger loading it.
     *
     */
    public function setUp() {
      $this->parent= ClassLoader::registerPath($this->modulePath('rdbms'));
      ClassLoader::registerPath($this->modulePath('sybase_ct'));
    }

    /**
     * Sets up test case
     *
     */
    public function tearDown() {
      ClassLoader::removeLoader($this->parent);
    }

    /**
     * Test getDelegate()
     *
     */
    #[@test]
    public function default_delegate() {
      $this->assertInstanceOf('lang.IClassLoader', $this->parent->getDelegate(NULL));
    }

    /**
     * Test getDelegate()
     *
     */
    #[@test]
    public function delegate_named_sybase_ct() {
      $this->assertInstanceOf('lang.IClassLoader', $this->parent->getDelegate('sybase_ct'));
    }

    /**
     * Test getDelegate()
     *
     */
    #[@test]
    public function default_and_sybase_ct_delegates_are_different_delegates() {
      $this->assertNotEquals($this->parent->getDelegate(NULL), $this->parent->getDelegate('sybase_ct'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function initializer_has_run() {
      $class= $this->parent->loadClass('rdbms2.Drivers');
      $this->assertEquals(
        'SybaseCtConnection<sybase+ct://user:password@host, 2.7.12RC1>',
        $class->getMethod('newConnection')->invoke(NULL, array('sybase+ct://user:password@host'))
      );
    }
  }
?>
