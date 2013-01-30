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
      return 'sybase_ct';
    }

    /**
     * Return module version
     *
     * @return  string
     */
    protected function moduleVersion() {
      return '1.7.12RC1';
    }

    /**
     * Test
     *
     */
    #[@test]
    public function uses_has_executed() {
      $this->assertTrue($this->fixture->providesClass('rdbms.sybase_ct.Driver'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function initializer_has_run() {
      $class= $this->fixture->loadClass('rdbms.sybase_ct.Driver');
      $this->assertEquals(
        array($this->moduleName(), $this->moduleVersion()),
        $class->getField('registered')->get($class->getField('instance')->get(NULL))
      );
    }
  }
?>
