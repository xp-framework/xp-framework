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
  public function calling_next_before_opening_collection_raises_exception() {
    $c= new MockCollection('~');
    $c->next();
  }

  #[@test, @expect('lang.IllegalStateException')]
  public function calling_next_after_closing_collection_raises_exception() {
    $c= new MockCollection('~');
    $c->open();
    $c->close();
    $c->next();
  }

  #[@test]
  public function next_returns_IOElement_instances() {
    $this->fixture->open();
    for ($i= 0; $e= $this->fixture->next(); $i++) {
      $this->assertInstanceOf('io.collections.IOElement', $e);
    }
    $this->assertEquals($this->sizes[$this->fixture->getURI()], $i);
    $this->fixture->close();
  }

  #[@test]
  public function next_returns_null_after_iteration_completed() {
    $this->fixture->open();
    while ($this->fixture->next()) { 
      // Intentionally empty
    }
    $this->assertNull($this->fixture->next());
    $this->fixture->close();
  }

  #[@test]
  public function consecutive_iteration_calls_with_open_and_close() {
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
  public function consecutive_iteration_calls_with_rewind() {
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
  public function get_elements_input_stream() {
    with ($stream= $this->firstElement($this->fixture)->getInputStream()); {
      $this->assertInstanceOf('io.streams.InputStream', $stream);
      $this->assertNotEquals(0, $stream->available());
      $this->assertEquals('File contents', $stream->read(13));
    }
  }

  #[@test, @expect('io.IOException')]
  public function get_collections_input_stream() {
    $this->firstElement($this->newCollection('/', array($this->newCollection('/root'))))->getInputStream();
  }

  #[@test]
  public function get_elements_output_stream() {
    with ($stream= $this->firstElement($this->fixture)->getOutputStream()); {
      $this->assertInstanceOf('io.streams.OutputStream', $stream);
      $stream->write('File contents');
    }
  }

  #[@test, @expect('io.IOException')]
  public function get_collections_output_stream() {
    $this->firstElement($this->newCollection('/', array($this->newCollection('/root'))))->getOutputStream();
  }
 
  #[@test]
  public function find_existing_element() {
    $this->assertEquals(new MockElement('./first.txt'), $this->fixture->findElement('first.txt'));
  }

  #[@test]
  public function find_nonexistant_element() {
    $this->assertEquals(null, $this->fixture->findElement('doesnotexist.txt'));
  }

  #[@test]
  public function create_non_existant_element() {
    $created= $this->fixture->newElement('new.txt');
    $this->assertEquals(new MockElement('./new.txt'), $created);
    $this->assertEquals($created, $this->fixture->getElement('new.txt'));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function create_existing_element() {
    $this->fixture->newElement('first.txt');
  }

  #[@test]
  public function get_existing_element() {
    $this->assertEquals(new MockElement('./first.txt'), $this->fixture->getElement('first.txt'));
  }

  #[@test, @expect('util.NoSuchElementException')]
  public function get_nonexistant_element() {
    $this->fixture->getElement('doesnotexist.txt');
  }

  #[@test]
  public function find_existing_collection() {
    $this->assertEquals(new MockCollection('./sub'), $this->fixture->findCollection('sub'));
  }

  #[@test]
  public function find_nonexistant_collection() {
    $this->assertEquals(null, $this->fixture->findCollection('doesnotexist'));
  }
 
  #[@test]
  public function get_existing_collection() {
    $this->assertEquals(new MockCollection('./sub'), $this->fixture->getCollection('sub'));
  }

  #[@test, @expect('util.NoSuchElementException')]
  public function get_nonexistant_collection() {
    $this->fixture->getCollection('doesnotexist');
  }

  #[@test]
  public function new_nonexistant_collection() {
    $created= $this->fixture->newCollection('newdir');
    $this->assertEquals(new MockCollection('./newdir'), $created);
    $this->assertEquals($created, $this->fixture->getCollection('newdir'));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function new_existing_collection() {
    $this->fixture->newCollection('sub');
  }
}
