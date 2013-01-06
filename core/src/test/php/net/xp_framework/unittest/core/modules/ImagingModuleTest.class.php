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
  class ImagingModuleTest extends AbstractModuleTest {

    /**
     * Return module name
     *
     * @return  string
     */
    protected function moduleName() {
      return 'imaging';
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
     * Test getComment()
     *
     */
    #[@test]
    public function imaging_modules_comment() {
      $this->assertEquals('An imaging module', $this->fixture->getComment());
    }

    /**
     * Test hasAnnotations()
     *
     */
    #[@test]
    public function does_not_have_any_annotations() {
      $this->assertFalse($this->fixture->hasAnnotations());
    }

    /**
     * Test getAnnotations()
     *
     */
    #[@test]
    public function annotations_are_empty() {
      $this->assertEquals(array(), $this->fixture->getAnnotations());
    }

    /**
     * Test hasAnnotation()
     *
     */
    #[@test]
    public function does_not_have_annotation() {
      $this->assertFalse($this->fixture->hasAnnotation('irrelevant'));
    }

    /**
     * Test hasAnnotation()
     *
     */
    #[@test]
    public function does_not_have_annotation_with_key() {
      $this->assertFalse($this->fixture->hasAnnotation('irrelevant', 'anything'));
    }

    /**
     * Test getAnnotation()
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function throws_exception_for_non_existant_annotation() {
      $this->fixture->getAnnotation('irrelevant');
    }

    /**
     * Test getAnnotation()
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function throws_exception_for_non_existant_annotation_with_key() {
      $this->fixture->getAnnotation('irrelevant', 'anything');
    }
  }
?>
