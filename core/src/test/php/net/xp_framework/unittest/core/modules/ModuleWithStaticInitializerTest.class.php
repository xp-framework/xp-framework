<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.core.modules.AbstractModuleTest');

  /**
   * TestCase
   *
   */
  class ModuleWithStaticInitializerTest extends AbstractModuleTest {

    /**
     * Return module name
     *
     * @return  string
     */
    protected function moduleName() {
      return 'rdbms';
    }

    /**
     * Return module version
     *
     * @return  string
     */
    protected function moduleVersion() {
      return '2.0';
    }

    /**
     * Test
     *
     */
    #[@test]
    public function uses_has_executed() {
      $this->assertTrue($this->fixture->providesClass('rdbms2.Driver'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function initializer_has_run() {
      $class= $this->fixture->loadClass('rdbms2.Drivers');
      $this->assertEquals(
        'DefaultConnection<default://user:password@host>',
        $class->getMethod('newConnection')->invoke(NULL, array('default://user:password@host'))
      );
    }
  }
?>
