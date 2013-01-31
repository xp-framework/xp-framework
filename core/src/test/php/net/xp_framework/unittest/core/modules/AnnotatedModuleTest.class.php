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
  class AnnotatedModuleTest extends AbstractModuleTest {

    /**
     * Return module name
     *
     * @return  string
     */
    protected function moduleName() {
      return 'forkqueue';
    }

    /**
     * Return module version
     *
     * @return  string
     */
    protected function moduleVersion() {
      return NULL;      // Has no version
    }
    
    /**
     * Test hasAnnotations()
     *
     */
    #[@test]
    public function has_annotations() {
      $this->assertTrue($this->fixture->hasAnnotations());
    }

    /**
     * Test getAnnotations()
     *
     */
    #[@test]
    public function annotations() {
      $this->assertEquals(
        array('author' => array('id' => 1, 'name' => 'timm')),
        $this->fixture->getAnnotations()
      );
    }

    /**
     * Test hasAnnotation()
     *
     */
    #[@test]
    public function has_author_annotation() {
      $this->assertTrue($this->fixture->hasAnnotation('author'));
    }

    /**
     * Test hasAnnotation()
     *
     */
    #[@test]
    public function has_author_name_annotation() {
      $this->assertTrue($this->fixture->hasAnnotation('author', 'name'));
    }

    /**
     * Test hasAnnotation()
     *
     */
    #[@test]
    public function has_author_id_annotation() {
      $this->assertTrue($this->fixture->hasAnnotation('author', 'id'));
    }

    /**
     * Test hasAnnotation()
     *
     */
    #[@test]
    public function does_not_have_author_other_key_annotation() {
      $this->assertFalse($this->fixture->hasAnnotation('author', 'non-existant'));
    }

    /**
     * Test getAnnotation()
     *
     */
    #[@test]
    public function author_annotation() {
      $this->assertEquals(
        array('id' => 1, 'name' => 'timm'), 
        $this->fixture->getAnnotation('author')
      );
    }

    /**
     * Test getAnnotation()
     *
     */
    #[@test]
    public function author_name_annotation() {
      $this->assertEquals('timm', $this->fixture->getAnnotation('author', 'name'));
    }

    /**
     * Test getAnnotation()
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function throws_exception_for_non_existant_annotation() {
      $this->fixture->getAnnotation('non-existant');
    }

    /**
     * Test getAnnotation()
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function throws_exception_for_non_existant_annotation_with_key() {
      $this->fixture->getAnnotation('non-existant', 'non-existant');
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation() {
      $this->assertEquals(
        'Module<'.$this->moduleName().', '.$this->fixture->getClassLoader()->toString().'>',     // No version
        $this->fixture->toString()
      );
    }
  }
?>
