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
  class ImagingModuleTest extends TestCase {
    protected static $loader= NULL;
    protected $fixture= NULL;
  
    /**
     * Register imaging module path. This will actually trigger loading it.
     *
     */
    #[@beforeClass]
    public static function registerPath() {
      self::$loader= ClassLoader::getDefault()->registerPath(dirname(__FILE__).'/imaging');
    }

    /**
     * Remove imaging module path.
     *
     */
    #[@afterClass]
    public static function removePath() {
      ClassLoader::getDefault()->removeLoader(self::$loader);
    }
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= Module::forName('imaging');
    }
    
    /**
     * Test getName()
     *
     */
    #[@test]
    public function imaging_modules_name() {
      $this->assertEquals('imaging', $this->fixture->getName());
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
