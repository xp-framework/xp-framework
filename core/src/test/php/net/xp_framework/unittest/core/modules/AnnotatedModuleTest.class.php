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
  class AnnotatedModuleTest extends TestCase {
    protected static $loader= NULL;
    protected $fixture= NULL;
  
    /**
     * Register imaging module path. This will actually trigger loading it.
     *
     */
    #[@beforeClass]
    public static function registerPath() {
      self::$loader= ClassLoader::getDefault()->registerPath(dirname(__FILE__).'/forkqueue');
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
      $this->fixture= Module::forName('forkqueue');
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
  }
?>
