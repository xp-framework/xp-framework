<?php namespace net\xp_framework\unittest\util;

use io\File;
use io\streams\Streams;
use io\streams\MemoryInputStream;


/**
 * Testcase for util.Properties class.
 *
 * @see   xp://net.xp_framework.unittest.util.AbstractPropertiesTest
 * @see   xp://util.Properties#fromFile
 * @test  xp://net.xp_framework.unittest.util.FilesystemPropertySourceTest
 */
class FileBasedPropertiesTest extends AbstractPropertiesTest {
  protected static $fileStreamAdapter;
  
  static function __static() {
    self::$fileStreamAdapter= \lang\ClassLoader::defineClass('FileStreamAdapter', 'io.File', array(), '{
      protected $stream= NULL;
      public function __construct($stream) { $this->stream= $stream; }
      public function exists() { return NULL !== $this->stream; }
      public function getURI() { return Streams::readableUri($this->stream); }
      public function getInputStream() { return $this->stream; }
    }');
  }

  /**
   * Create a new properties object from a string source
   *
   * @param   string source
   * @return  util.Properties
   */
  protected function newPropertiesFrom($source) {
    return \util\Properties::fromFile(self::$fileStreamAdapter->newInstance(new MemoryInputStream($source)));
  }

  /**
   * Test construction via fromFile() method for a non-existant file
   *
   */
  #[@test, @expect('io.IOException')]
  public function fromNonExistantFile() {
    \util\Properties::fromFile(new File('@@does-not-exist.ini@@'));
  }

  /**
   * Test construction via fromFile() method for an existant file.
   * Relies on a file "example.ini" existing parallel to this class.
   *
   */
  #[@test]
  public function fromFile() {
    $p= \util\Properties::fromFile($this->getClass()->getPackage()->getResourceAsStream('example.ini'));
    $this->assertEquals('value', $p->readString('section', 'key'));
  }

  /**
   * Test exceptions are not thrown until first read
   *
   */
  #[@test]
  public function lazyRead() {
    $p= new \util\Properties('@@does-not-exist.ini@@');
    
    // This cannot be done via @expect because it would also catch if an
    // exception was thrown from util.Properties' constructor. We explicitely
    // want the exception to be thrown later on
    try {
      $p->readString('section', 'key');
      $this->fail('Expected exception not thrown', null, 'io.IOException');
    } catch (\io\IOException $expected) {
      \xp::gc();
    }
  }

  /**
   * Test
   *
   */
  #[@test]
  public function propertiesFromSameFileAreEqual() {
    $one= \util\Properties::fromFile($this->getClass()->getPackage()->getResourceAsStream('example.ini'));
    $two= \util\Properties::fromFile($this->getClass()->getPackage()->getResourceAsStream('example.ini'));

    $this->assertFalse($one === $two);
    $this->assertTrue($one->equals($two));
  }
}
