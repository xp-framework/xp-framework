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
  class ImagingTestModuleTest extends AbstractModuleTest {

    /**
     * Return module name
     *
     * @return  string
     */
    protected function moduleName() {
      return 'imaging.test';
    }

    /**
     * Return module version
     *
     * @return  string
     */
    protected function moduleVersion() {
      return '3.4.1';
    }

    /**
     * Injects a class loader into the fixture
     *
     * @param  lang.ClassLoader cl
     */
    protected function useClassLoader($cl) {
      $this->fixture->getClass()->getField('loader')->setAccessible(TRUE)->set($this->fixture, $cl);
    }

    /**
     * Test providesClass()
     *
     */
    #[@test]
    public function does_not_ask_for_resource_outside_provides() {
      $this->useClassLoader(newinstance('lang.IClassLoader', array(), '{
        public function providesResource($name) { throw new IllegalStateException("Loader asked for resource ".$name); }
        public function providesClass($name) { /* Not reached */ }
        public function providesPackage($name) { /* Not reached */ }
        public function packageContents($name) { /* Not reached */ }
        public function loadClass($name) { /* Not reached */ }
        public function loadClass0($name) { /* Not reached */ }
        public function getResource($name) { /* Not reached */ }
        public function getResourceAsStream($name) { /* Not reached */ }
        public function instanceId() { return "(test-fixture)"; }
      }'));
      $this->assertFalse($this->fixture->providesResource('imaging/api/resources/mime.ini'));
    }

    /**
     * Test providesClass()
     *
     */
    #[@test]
    public function does_not_ask_for_class_outside_provides() {
      $this->useClassLoader(newinstance('lang.IClassLoader', array(), '{
        public function providesResource($name) { /* Not reached */ }
        public function providesClass($name) { throw new IllegalStateException("Loader asked for class ".$name); }
        public function providesPackage($name) { /* Not reached */ }
        public function packageContents($name) { /* Not reached */ }
        public function loadClass($name) { /* Not reached */ }
        public function loadClass0($name) { /* Not reached */ }
        public function getResource($name) { /* Not reached */ }
        public function getResourceAsStream($name) { /* Not reached */ }
        public function instanceId() { return "(test-fixture)"; }
      }'));
      $this->assertFalse($this->fixture->providesClass('imaging.api.AnyClass'));
    }

    /**
     * Test providesClass()
     *
     */
    #[@test]
    public function does_not_ask_for_package_outside_provides() {
      $this->useClassLoader(newinstance('lang.IClassLoader', array(), '{
        public function providesResource($name) { /* Not reached */ }
        public function providesClass($name) { /* Not reached */ }
        public function providesPackage($name) { throw new IllegalStateException("Loader asked for package ".$name); }
        public function packageContents($name) { /* Not reached */ }
        public function loadClass($name) { /* Not reached */ }
        public function loadClass0($name) { /* Not reached */ }
        public function getResource($name) { /* Not reached */ }
        public function getResourceAsStream($name) { /* Not reached */ }
        public function instanceId() { return "(test-fixture)"; }
      }'));
      $this->assertFalse($this->fixture->providesPackage('imaging.api'));
    }
  }
?>
