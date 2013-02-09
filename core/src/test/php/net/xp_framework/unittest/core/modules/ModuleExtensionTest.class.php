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
      $this->parent= ClassLoader::getDefault()->registerPath($this->modulePath('rdbms'));
      ClassLoader::getDefault()->registerPath($this->modulePath('sybase_ct'));
    }

    /**
     * Sets up test case
     *
     */
    public function tearDown() {
      ClassLoader::removeLoader($this->parent);
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function initializer_has_run() {
      $class= $this->parent->loadClass('rdbms2.Drivers');
      $this->assertEquals(
        'SybaseCtConnection<sybase+ct://user:password@host, 2.7.11RC2>',
        $class->getMethod('newConnection')->invoke(NULL, array('sybase+ct://user:password@host'))
      );
    }
  }
?>
