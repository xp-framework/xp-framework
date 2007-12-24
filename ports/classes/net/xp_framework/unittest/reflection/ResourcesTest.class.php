<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'lang.archive.Archive', 'io.File');

  /**
   * TestCase for resource loading
   *
   * @see      xp://lang.ClassLoader
   * @purpose  Unittest
   */
  class ResourcesTest extends TestCase {

    static function __static() {
      ClassLoader::registerLoader(new ArchiveClassLoader(
        new Archive(new File(dirname(__FILE__).'/lib/three-and-four.xar'))
      ));
    }

    /**
     * Helper method for getResource() and getResourceAsStream()
     *
     * @param   string contents
     */
    protected function assertManifestFile($contents) {
      $this->assertEquals(
        "[runnable]\nmain-class=\"remote.server.impl.ApplicationServer\"",
        trim($contents)
      );
    }
    
    /**
     * Tests findResource() method
     *
     */
    #[@test]
    public function findResource() {
      $this->assertClass(
        ClassLoader::getDefault()->findResource('META-INF/manifest.ini'),
        'lang.archive.ArchiveClassLoader'
      );
    }

    /**
     * Tests getResource() method
     *
     */
    #[@test]
    public function getResource() {
      $this->assertManifestFile(ClassLoader::getDefault()->getResource('META-INF/manifest.ini'));
    }

    /**
     * Tests getResourceAsStream() method
     *
     */
    #[@test]
    public function getResourceAsStream() {
      $stream= ClassLoader::getDefault()->getResourceAsStream('META-INF/manifest.ini');
      $this->assertSubClass($stream, 'io.Stream');
      $stream->open(STREAM_MODE_READ);
      $this->assertManifestFile($stream->read($stream->size()));
      $stream->close();
    }

    /**
     * Tests getResource() method
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function nonExistantResource() {
      ClassLoader::getDefault()->getResource('::DOES-NOT-EXIST::');
    }

    /**
     * Tests getResourceAsStream() method
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function nonExistantResourceStream() {
      ClassLoader::getDefault()->getResourceAsStream('::DOES-NOT-EXIST::');
    }
  }
?>
