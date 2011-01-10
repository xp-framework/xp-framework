<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.util.AbstractPropertiesTest',
    'io.File',
    'io.streams.Streams',
    'io.streams.MemoryInputStream'
  );

  /**
   * Testcase for util.Properties class.
   *
   * @see   xp://net.xp_framework.unittest.util.AbstractPropertiesTest
   * @see   xp://util.Properties#fromFile
   */
  class FileBasedPropertiesTest extends AbstractPropertiesTest {
    protected static $fileStreamAdapter;
    
    static function __static() {
      self::$fileStreamAdapter= ClassLoader::defineClass('FileStreamAdapter', 'io.File', array(), '{
        protected $stream= NULL;
        public function __construct($stream) { $this->stream= $stream; }
        public function exists() { return NULL !== $this->stream; }
        public function getURI() { return Streams::readableUri($this->stream); }
      }');
    }
  
    /**
     * Create a new properties object from a string source
     *
     * @param   string source
     * @return  util.Properties
     */
    protected function newPropertiesFrom($source) {
      return Properties::fromFile(self::$fileStreamAdapter->newInstance(new MemoryInputStream($source)));
    }

    /**
     * Test construction via fromFile() method for a non-existant file
     *
     */
    #[@test, @expect('io.IOException')]
    public function fromNonExistantFile() {
      Properties::fromFile(new File('@@does-not-exist.ini@@'));
    }

    /**
     * Test construction via fromFile() method for an existant file.
     * Relies on a file "example.ini" existing parallel to this class.
     *
     */
    #[@test]
    public function fromFile() {
      $p= Properties::fromFile($this->getClass()->getPackage()->getResourceAsStream('example.ini'));
      $this->assertEquals('value', $p->readString('section', 'key'));
    }

    /**
     * Test exceptions are not thrown until first read
     *
     */
    #[@test]
    public function lazyRead() {
      $p= new Properties('@@does-not-exist.ini@@');
      
      // This cannot be done via @expect because it would also catch if an
      // exception was thrown from util.Properties' constructor. We explicitely
      // want the exception to be thrown later on
      try {
        $p->readString('section', 'key');
        $this->fail('Expected exception not thrown', NULL, 'io.IOException');
      } catch (IOException $expected) {
        xp::gc();
      }
    }
  }
?>
