<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.Properties',
    'util.Hashmap'
  );

  /**
   * Testcase for util.Properties class.
   *
   * @see      xp://util.Properties
   */
  class PropertyWritingTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Creates a new, empty properties file as fixture
     *
     */
    public function setUp() {
      $this->fixture= new Properties(NULL);
      $this->fixture->create();
    }
    
    /**
     * Verifies the saved property file equals a given expected source string
     *
     * @param   string expected
     * @throws  unittest.AssertionFailedError
     */
    protected function assertSavedFixtureEquals($expected) {
      $out= new MemoryOutputStream();
      $this->fixture->store($out);
      $this->assertEquals(preg_replace('/^ +/m', '', trim($expected)), trim($out->getBytes())); 
    }
  
    /**
     * Test writing a string
     *
     */
    #[@test]
    public function string() {
      $this->fixture->writeString('section', 'key', 'value');
      $this->assertSavedFixtureEquals('
        [section]
        key="value"
      ');
    }

    /**
     * Test writing a string
     *
     */
    #[@test]
    public function emptyString() {
      $this->fixture->writeString('section', 'key', '');
      $this->assertSavedFixtureEquals('
        [section]
        key=""
      ');
    }

    /**
     * Test writing an integer
     *
     */
    #[@test]
    public function integer() {
      $this->fixture->writeInteger('section', 'key', 1);
      $this->assertSavedFixtureEquals('
        [section]
        key=1
      ');
    }

    /**
     * Test writing a float
     *
     */
    #[@test]
    public function float() {
      $this->fixture->writeFloat('section', 'key', 1.5);
      $this->assertSavedFixtureEquals('
        [section]
        key=1.5
      ');
    }

    /**
     * Test writing a bool
     *
     */
    #[@test]
    public function boolTrue() {
      $this->fixture->writeFloat('section', 'key', TRUE);
      $this->assertSavedFixtureEquals('
        [section]
        key=1
      ');
    }

    /**
     * Test writing a bool
     *
     */
    #[@test]
    public function boolFalse() {
      $this->fixture->writeFloat('section', 'key', FALSE);
      $this->assertSavedFixtureEquals('
        [section]
        key=0
      ');
    }

    /**
     * Test writing an array
     *
     */
    #[@test]
    public function intArray() {
      $this->fixture->writeArray('section', 'key', array(1, 2, 3));
      $this->assertSavedFixtureEquals('
        [section]
        key="1|2|3"
      ');
    }

    /**
     * Test writing an array
     *
     */
    #[@test]
    public function emptyArray() {
      $this->fixture->writeArray('section', 'key', array());
      $this->assertSavedFixtureEquals('
        [section]
        key=""
      ');
    }

    /**
     * Test writing a hashmap
     *
     */
    #[@test]
    public function hashmapOneElement() {
      $h= new HashMap();
      $h->put('color', 'green');
      $this->fixture->writeHash('section', 'key', $h);
      $this->assertSavedFixtureEquals('
        [section]
        key="color:green"
      ');
    }

    /**
     * Test writing a hashmap
     *
     */
    #[@test]
    public function hashmapTwoElements() {
      $h= new HashMap();
      $h->put('color', 'green');
      $h->put('size', 'L');
      $this->fixture->writeHash('section', 'key', $h);
      $this->assertSavedFixtureEquals('
        [section]
        key="color:green|size:L"
      ');
    }

    /**
     * Test writing a hashmap
     *
     */
    #[@test]
    public function emptyHashmap() {
      $this->fixture->writeHash('section', 'key', new HashMap());
      $this->assertSavedFixtureEquals('
        [section]
        key=""
      ');
    }

    /**
     * Test writing a comment
     *
     */
    #[@test]
    public function comment() {
      $this->fixture->writeComment('section', 'Hello');
      $this->assertSavedFixtureEquals('
        [section]

        ; Hello
      ');
    }

    /**
     * Test writing a comment
     *
     */
    #[@test]
    public function comments() {
      $this->fixture->writeComment('section', 'Hello');
      $this->fixture->writeComment('section', 'World');
      $this->assertSavedFixtureEquals('
        [section]

        ; Hello

        ; World
      ');
    }
  }
?>
