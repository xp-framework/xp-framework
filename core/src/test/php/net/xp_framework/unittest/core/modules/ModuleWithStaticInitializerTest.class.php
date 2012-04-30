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
     * Test
     *
     */
    #[@test]
    public function uses_has_executed() {
      $this->assertTrue($this->loader->providesClass('rdbms.sybase_ct.Driver'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function static_initializer_has_run() {
      $class= $this->loader->loadClass('rdbms.sybase_ct.Driver');
      $this->assertTrue($class->getField('registered')->get($class->getField('instance')->get(NULL)));
    }
  }
?>
