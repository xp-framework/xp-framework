<?php namespace net\xp_framework\unittest\io\collections;

use unittest\TestCase;
use io\TempFile;
use lang\archive\Archive;
use io\collections\ArchiveCollection;

/**
 * TestCase
 *
 * @see  xp://io.collections.ArchiveCollection
 */
class ArchiveCollectionTest extends TestCase {
  protected $file= null;
  protected $archive= null;

  /**
   * Sets up test case (creates temporary xar archive)
   */
  public function setUp() {
    $this->file= new TempFile();
    $this->archive= new Archive($this->file);
    $this->archive->open(ARCHIVE_CREATE);
    $this->archive->addBytes('lang/Object.xp', 'class Object { }');
    $this->archive->addBytes('lang/Type.xp', 'class Type extends Object { }');
    $this->archive->addBytes('lang/reflect/Method.xp', 'class Method extends Object { }');
    $this->archive->addBytes('lang/reflect/Ctor.xp', 'class Ctor extends Object { }');
    $this->archive->addBytes('lang/types/String.xp', 'class String extends Object { }');
    $this->archive->addBytes('lang/types/map/Uint.xp', 'class Uint extends Object { }');
    $this->archive->addBytes('lang/Runnable.xp', 'interface Runnable { }');
    $this->archive->create();
  }
  
  /**
   * Tears down test case (removes temporary xar archive)
   */
  public function tearDown() {
    try {
      $this->file->isOpen() && $this->file->close();
      $this->file->unlink();
    } catch (\io\IOException $ignored) {
      // Can't really do much about it..
    }
  }
  
  /**
   * Assertion helper
   *
   * @param   string name
   * @param   string uri
   * @throws  unittest.AssertionFailedError
   */
  protected function assertXarUri($name, $uri) {
    $this->assertEquals('xar://', substr($uri, 0, 6));
    $this->assertEquals($name, substr($uri, -strlen($name)), $uri);
  }
  
  #[@test]
  public function entriesInBase() {
    $c= new ArchiveCollection($this->archive);
    try {
      $c->open();
      $first= $c->next();
      $this->assertInstanceOf('io.collections.IOCollection', $first);
      $this->assertXarUri('lang/', $first->getURI());
      $this->assertEquals(0, $first->getSize());
      $this->assertEquals(null, $c->next());
    } catch (\lang\Throwable $e) {
    } ensure($e); {
      $c->close();
      if ($e) throw $e;
    }
  }
  
  #[@test]
  public function entriesInLang() {
    $c= new ArchiveCollection($this->archive, 'lang');
    
    try {
      $c->open();
      $expect= array(
        'lang/Object.xp'    => 'io.collections.IOElement', 
        'lang/Type.xp'      => 'io.collections.IOElement',
        'lang/reflect/'     => 'io.collections.IOCollection',
        'lang/types/'       => 'io.collections.IOCollection',
        'lang/Runnable.xp'  => 'io.collections.IOElement',
      );
      for (reset($expect); $element= $c->next(), $name= key($expect); next($expect)) {
        $this->assertSubclass($element, $expect[$name]);
        $this->assertXarUri($name, $element->getURI());
      }
      $this->assertEquals(null, $c->next());
    } catch (\lang\Throwable $e) {
    } ensure($e); {
      $c->close();
      if ($e) throw $e;
    }
  }

  /**
   * Returns first element in a given collection
   *
   * @param   io.IOCollection collection
   * @return  io.IOElement 
   * @throws  unittest.AssertionFailedError if no elements are available
   */
  protected function firstElement(\io\collections\IOCollection $collection) {
    $collection->open();
    $first= $collection->next();
    $collection->close();
    $this->assertNotEquals(null, $first, 'No first element in '.$collection->toString());
    return $first;
  }

  #[@test]
  public function readTwice() {
    $c= new ArchiveCollection($this->archive);
    $this->assertEquals($this->firstElement($c), $this->firstElement($c));
  }

  #[@test]
  public function readLangTwice() {
    $c= new ArchiveCollection($this->archive, 'lang');
    $this->assertEquals($this->firstElement($c), $this->firstElement($c));
  }

  #[@test]
  public function readObjectEntry() {
    with ($first= $this->firstElement(new ArchiveCollection($this->archive, 'lang'))); {
      $this->assertEquals(
        'class Object { }', 
        $first->getInputStream()->read($first->getSize())
      );
    }
  }

  #[@test, @expect('io.IOException')]
  public function writeObjectEntry() {
    $this->firstElement(new ArchiveCollection($this->archive, 'lang'))->getOutputStream();
  }

  #[@test, @expect('io.IOException')]
  public function readLangEntry() {
    $this->firstElement(new ArchiveCollection($this->archive))->getInputStream();
  }

  #[@test, @expect('io.IOException')]
  public function writeLangEntry() {
    $this->firstElement(new ArchiveCollection($this->archive))->getOutputStream();
  }

  #[@test]
  public function collections_origin() {
    $base= new ArchiveCollection($this->archive, 'lang');
    $this->assertEquals($base, $this->firstElement($base)->getOrigin());
  }

  #[@test]
  public function collections_name() {
    $base= new ArchiveCollection($this->archive, 'lang');
    $this->assertEquals('lang', $base->getName());
  }

  #[@test]
  public function collections_uri() {
    $base= new ArchiveCollection($this->archive, 'lang');
    $this->assertXarUri('lang/', $base->getUri());
  }

  #[@test]
  public function elements_name() {
    $element= $this->firstElement(new ArchiveCollection($this->archive, 'lang'));
    $this->assertEquals('Object.xp', $element->getName());
  }

  #[@test]
  public function elements_uri() {
    $element= $this->firstElement(new ArchiveCollection($this->archive, 'lang'));
    $this->assertXarUri('lang/Object.xp', $element->getUri());
  }
}
