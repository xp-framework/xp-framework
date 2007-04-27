<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

  /**
   * TestCase for resource loading
   *
   * @see      xp://lang.ClassLoader
   * @purpose  Unittest
   */
  class ResourcesTest extends TestCase {

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
      $this->assertManifestFile($stream->read($stream->size()));
    }

    /**
     * Tests getResource() method
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function nonExistantResource() {
      ClassLoader::getDefault()->getResource('::DOES-NOT-EXIST::');
    }
  }
?>
