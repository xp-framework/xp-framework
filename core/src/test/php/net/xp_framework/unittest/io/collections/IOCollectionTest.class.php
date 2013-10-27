<?php namespace net\xp_framework\unittest\io\collections;

use io\collections\IOCollection;

/**
 * Unit tests for IOCollection class (basic functionality)
 *
 * @see   xp://io.collections.IOCollection
 */
class IOCollectionTest extends AbstractCollectionTest {

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
    $this->assertNotEquals(null, $first);
    return $first;
  }

  #[@test, @values(['.', './sub', './sub/sec'])]
  public function getUri_adds_trailing_slash_to_collections($dir) {
    $this->assertEquals($dir.'/', create(new MockCollection($dir))->getURI());
  }

  #[@test, @values(['./first.txt', './sub/sec/lang.base.php'])]
  public function getUri_retuns_absolute_name_of_elements($file) {
    $this->assertEquals($file, create(new MockElement($file))->getURI());
  }

  #[@test, @values(['.', './sub', './sub/sec'])]
  public function getName_returns_relative_name_of_collections($dir) {
    $this->assertEquals(basename($dir), create(new MockCollection($dir))->getName());
  }

  #[@test, @values(['./first.txt', './sub/sec/lang.base.php'])]
  public function getUri_retuns_relatvie_name_of_elements($file) {
    $this->assertEquals(basename($file), create(new MockElement($file))->getName());
  }

  #[@test]
  public function next_returns_null_for_empty_collection() {
    $empty= new MockCollection('empty-dir');
    $empty->open();
    $this->assertNull($empty->next());
    $empty->close();
  }

  #[@test, @expect('lang.IllegalStateException')]
  public function nextBeforeOpen() {
    $c= new MockCollection('~');
    $c->next();
  }

  #[@test, @expect('lang.IllegalStateException')]
  public function nextAfterClose() {
    $c= new MockCollection('~');
    $c->open();
    $c->close();
    $c->next();
  }

  #[@test]
  public function nextReturnsIOElements() {
    $this->fixture->open();
    for ($i= 0; $e= $this->fixture->next(); $i++) {
      $this->assertInstanceOf('io.collections.IOElement', $e);
    }
    $this->assertEquals($this->sizes[$this->fixture->getURI()], $i);
    $this->fixture->close();
  }

  #[@test]
  public function nextReturnsNullAfterIteration() {
    $this->fixture->open();
    while ($this->fixture->next()) { 
      // Intentionally empty
    }
    $this->assertNull($this->fixture->next());
    $this->fixture->close();
  }

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
  
  #[@test]
  public function inputStream() {
    with ($stream= $this->firstElement($this->fixture)->getInputStream()); {
      $this->assertSubclass($stream, 'io.streams.InputStream');
      $this->assertNotEquals(0, $stream->available());
      $this->assertEquals('File contents', $stream->read(13));
    }
  }

  #[@test, @expect('io.IOException')]
  public function collectionInputStream() {
    $this->firstElement($this->newCollection('/', array($this->newCollection('/root'))))->getInputStream();
  }

  #[@test]
  public function outputStream() {
    with ($stream= $this->firstElement($this->fixture)->getOutputStream()); {
      $this->assertSubclass($stream, 'io.streams.OutputStream');
      $stream->write('File contents');
    }
  }

  #[@test, @expect('io.IOException')]
  public function collectionOutputStream() {
    $this->firstElement($this->newCollection('/', array($this->newCollection('/root'))))->getOutputStream();
  }
 
  #[@test]
  public function findExistingElement() {
    $this->assertEquals(new MockElement('./first.txt'), $this->fixture->findElement('first.txt'));
  }

  #[@test]
  public function findNonExistantElement() {
    $this->assertEquals(null, $this->fixture->findElement('doesnotexist.txt'));
  }

  #[@test]
  public function newNonExistantElement() {
    $created= $this->fixture->newElement('new.txt');
    $this->assertEquals(new MockElement('./new.txt'), $created);
    $this->assertEquals($created, $this->fixture->getElement('new.txt'));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function newExistingElement() {
    $this->fixture->newElement('first.txt');
  }

  #[@test]
  public function getExistingElement() {
    $this->assertEquals(new MockElement('./first.txt'), $this->fixture->getElement('first.txt'));
  }

  #[@test, @expect('util.NoSuchElementException')]
  public function getNonExistantElement() {
    $this->fixture->getElement('doesnotexist.txt');
  }

  #[@test]
  public function findExistingCollection() {
    $this->assertEquals(new MockCollection('./sub'), $this->fixture->findCollection('sub'));
  }

  #[@test]
  public function findNonExistantCollection() {
    $this->assertEquals(null, $this->fixture->findCollection('doesnotexist'));
  }
 
  #[@test]
  public function getExistingCollection() {
    $this->assertEquals(new MockCollection('./sub'), $this->fixture->getCollection('sub'));
  }

  #[@test, @expect('util.NoSuchElementException')]
  public function getNonExistantCollection() {
    $this->fixture->getCollection('doesnotexist');
  }

  #[@test]
  public function newNonExistantCollection() {
    $created= $this->fixture->newCollection('newdir');
    $this->assertEquals(new MockCollection('./newdir'), $created);
    $this->assertEquals($created, $this->fixture->getCollection('newdir'));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function newExistingCollection() {
    $this->fixture->newCollection('sub');
  }
}
