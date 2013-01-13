<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.io.collections.AbstractCollectionTest');

  /**
   * Unit tests for IOCollection class (basic functionality)
   *
   * @see      xp://io.collections.IOCollection
   * @purpose  Unit test
   */
  class IOCollectionTest extends AbstractCollectionTest {
    
    /**
     * Test next() returns NULL when no elements are left
     *
     */
    #[@test]
    public function nextReturnsNull() {
      $empty= new MockCollection('empty-dir');
      $empty->open();
      $this->assertNull($empty->next());
      $empty->close();
    }

    /**
     * Test next() throws an exception if collection is not open
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function nextBeforeOpen() {
      $c= new MockCollection('~');
      $c->next();
    }

    /**
     * Test next() throws an exception if collection is not open
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function nextAfterClose() {
      $c= new MockCollection('~');
      $c->open();
      $c->close();
      $c->next();
    }
  
    /**
     * Test next() returns IOElements
     *
     */
    #[@test]
    public function nextReturnsIOElements() {
      $this->fixture->open();
      for ($i= 0; $e= $this->fixture->next(); $i++) {
        $this->assertSubclass($e, 'io.collections.IOElement');
      }
      $this->assertEquals($this->sizes[$this->fixture->getURI()], $i);
      $this->fixture->close();
    }

    /**
     * Test next() returns NULL after iterating over all elements
     *
     */
    #[@test]
    public function nextReturnsNullAfterIteration() {
      $this->fixture->open();
      while ($this->fixture->next()) { 
        // Intentionally empty
      }
      $this->assertNull($this->fixture->next());
      $this->fixture->close();
    }

    /**
     * Test consecutive iteration works
     *
     */
    #[@test]
    public function consecutiveIteration() {
      for ($i= 0; $i < 2; $i++) {
        $elements= 0;
        $this->fixture->open();
        while ($this->fixture->next()) { 
          $elements++;
        }
        $this->assertNull($this->fixture->next());
        $this->assertEquals($this->sizes[$this->fixture->getURI()], $elements, 'Iteration #'.$i);
        $this->fixture->close();
      }
    }

    /**
     * Test consecutive iteration works
     *
     */
    #[@test]
    public function consecutiveIterationWithRewind() {
      $this->fixture->open();
      for ($i= 0; $i < 2; $i++) {
        $elements= 0;
        $this->fixture->rewind();
        while ($this->fixture->next()) { 
          $elements++;
        }
        $this->assertNull($this->fixture->next());
        $this->assertEquals($this->sizes[$this->fixture->getURI()], $elements, 'Iteration #'.$i);
      }
      $this->fixture->close();
    }
    
    /**
     * Returns first element in a given collection
     *
     * @param   io.IOCollection collection
     * @return  io.IOElement 
     * @throws  unittest.AssertionFailedError if no elements are available
     */
    protected function firstElement(IOCollection $collection) {
      $collection->open();
      $first= $collection->next();
      $collection->close();
      $this->assertNotEquals(NULL, $first);
      return $first;
    }

    /**
     * Test getInputStream()
     *
     */
    #[@test]
    public function inputStream() {
      with ($stream= $this->firstElement($this->fixture)->getInputStream()); {
        $this->assertSubclass($stream, 'io.streams.InputStream');
        $this->assertNotEquals(0, $stream->available());
        $this->assertEquals('File contents', $stream->read(13));
      }
    }

    /**
     * Test getInputStream()
     *
     */
    #[@test, @expect('io.IOException')]
    public function collectionInputStream() {
      $this->firstElement($this->newCollection('/', array($this->newCollection('/root'))))->getInputStream();
    }

    /**
     * Test getOutputStream()
     *
     */
    #[@test]
    public function outputStream() {
      with ($stream= $this->firstElement($this->fixture)->getOutputStream()); {
        $this->assertSubclass($stream, 'io.streams.OutputStream');
        $stream->write('File contents');
      }
    }

    /**
     * Test getOutputStream()
     *
     */
    #[@test, @expect('io.IOException')]
    public function collectionOutputStream() {
      $this->firstElement($this->newCollection('/', array($this->newCollection('/root'))))->getOutputStream();
    }
 
    /**
     * Test findElement()
     *
     */
    #[@test]
    public function findExistingElement() {
      $this->assertEquals(new MockElement('./first.txt'), $this->fixture->findElement('first.txt'));
    }

    /**
     * Test findElement()
     *
     */
    #[@test]
    public function findNonExistantElement() {
      $this->assertEquals(NULL, $this->fixture->findElement('doesnotexist.txt'));
    }

    /**
     * Test newElement()
     *
     */
    #[@test]
    public function newNonExistantElement() {
      $created= $this->fixture->newElement('new.txt');
      $this->assertEquals(new MockElement('./new.txt'), $created);
      $this->assertEquals($created, $this->fixture->getElement('new.txt'));
    }

    /**
     * Test newElement()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function newExistingElement() {
      $this->fixture->newElement('first.txt');
    }

    /**
     * Test getElement()
     *
     */
    #[@test]
    public function getExistingElement() {
      $this->assertEquals(new MockElement('./first.txt'), $this->fixture->getElement('first.txt'));
    }

    /**
     * Test getElement()
     *
     */
    #[@test, @expect('util.NoSuchElementException')]
    public function getNonExistantElement() {
      $this->fixture->getElement('doesnotexist.txt');
    }

    /**
     * Test findCollection()
     *
     */
    #[@test]
    public function findExistingCollection() {
      $this->assertEquals(new MockCollection('./sub'), $this->fixture->findCollection('sub'));
    }

    /**
     * Test findCollection()
     *
     */
    #[@test]
    public function findNonExistantCollection() {
      $this->assertEquals(NULL, $this->fixture->findCollection('doesnotexist'));
    }
 
    /**
     * Test getCollection()
     *
     */
    #[@test]
    public function getExistingCollection() {
      $this->assertEquals(new MockCollection('./sub'), $this->fixture->getCollection('sub'));
    }

    /**
     * Test getCollection()
     *
     */
    #[@test, @expect('util.NoSuchElementException')]
    public function getNonExistantCollection() {
      $this->fixture->getCollection('doesnotexist');
    }

    /**
     * Test newCollection()
     *
     */
    #[@test]
    public function newNonExistantCollection() {
      $created= $this->fixture->newCollection('newdir');
      $this->assertEquals(new MockCollection('./newdir'), $created);
      $this->assertEquals($created, $this->fixture->getCollection('newdir'));
    }

    /**
     * Test newCollection()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function newExistingCollection() {
      $this->fixture->newCollection('sub');
    }
  }
?>
