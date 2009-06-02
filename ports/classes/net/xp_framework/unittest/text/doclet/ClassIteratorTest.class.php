<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.doclet.RootDoc',
    'text.doclet.ClassIterator'
  );

  /**
   * TestCase
   *
   * @see      xp://text.doclet.ClassIterator
   */
  class ClassIteratorTest extends TestCase {
    protected $rootDoc= NULL;
  
    /**
     * Sets up testcase
     *
     */
    public function setUp() {
      $this->rootDoc= new RootDoc();
    }
  
    /**
     * Test hasNext() method returns FALSE when invoked with an empty list
     *
     */
    #[@test]
    public function hasNextOnEmptyClassList() {
      $this->assertFalse(create(new ClassIterator(array(), $this->rootDoc))->hasNext());
    }

    /**
     * Test next() method throws an util.NoSuchElementException when
     * invoked with an empty list
     *
     */
    #[@test, @expect('util.NoSuchElementException')]
    public function nextOnEmptyClassList() {
      create(new ClassIterator(array(), $this->rootDoc))->next();
    }

    /**
     * Test hasNext()
     *
     */
    #[@test]
    public function hasNext() {
      $it= new ClassIterator(array('lang.Object'), $this->rootDoc);
      $this->assertTrue($it->hasNext());
    }

    /**
     * Test calling hasNext() multiple times
     *
     */
    #[@test]
    public function hasNextMultiple() {
      $it= new ClassIterator(array('lang.Object'), $this->rootDoc);
      $this->assertTrue($it->hasNext());
      $this->assertTrue($it->hasNext());
    }

    /**
     * Test next()
     *
     */
    #[@test]
    public function next() {
      $it= new ClassIterator(array('lang.Object'), $this->rootDoc);
      $this->assertEquals($this->rootDoc->classNamed('lang.Object'), $it->next());
    }

    /**
     * Test next()
     *
     */
    #[@test, @expect('util.NoSuchElementException')]
    public function nextThrowsExceptionAfterFirstElement() {
      $it= new ClassIterator(array('lang.Object'), $this->rootDoc);
      $it->next();
      $it->next();
    }

    /**
     * Test hasNext() and next() in combination
     *
     */
    #[@test]
    public function hasNextAfterNext() {
      $it= new ClassIterator(array('lang.Type'), $this->rootDoc);
      $it->next();
      $this->assertFalse($it->hasNext());
    }

    /**
     * Test hasNext() and next() in combination
     *
     */
    #[@test]
    public function hasNextAfterNextThrowsException() {
      $it= new ClassIterator(array(), $this->rootDoc);
      try {
        $it->next();
        $this->fail('next() did not throw an exception', NULL, 'lang.NoSuchElementException');
      } catch (NoSuchElementException $expected) {
        // OK   
      }
      $this->assertFalse($it->hasNext());
    }

    /**
     * Test hasNext() and next() in combination
     *
     */
    #[@test]
    public function hasNextAndNext() {
      $it= new ClassIterator(array('lang.Object', 'lang.Type'), $this->rootDoc);
      $this->assertTrue($it->hasNext());
      $this->assertEquals($this->rootDoc->classNamed('lang.Object'), $it->next());
      $this->assertTrue($it->hasNext());
      $this->assertEquals($this->rootDoc->classNamed('lang.Type'), $it->next());
      $this->assertFalse($it->hasNext());
    }
  }
?>
