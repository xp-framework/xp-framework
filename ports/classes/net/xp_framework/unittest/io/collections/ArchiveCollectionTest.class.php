<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.TempFile',
    'lang.archive.Archive',
    'io.collections.ArchiveCollection'
  );

  /**
   * TestCase
   *
   * @see      xp://io.collections.ArchiveCollection
   */
  class ArchiveCollectionTest extends TestCase {
    protected
      $file     = NULL,
      $archive  = NULL;
  
    /**
     * Sets up test case (creates temporary xar archive)
     *
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
     *
     */
    public function tearDown() {
      try {
        $this->file->isOpen() && $this->file->close();
        $this->file->unlink();
      } catch (IOException $ignored) {
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
    
    /**
     * Test
     *
     */
    #[@test]
    public function entriesInBase() {
      $c= new ArchiveCollection($this->archive);
      try {
        $c->open();
        $first= $c->next();
        $this->assertSubclass($first, 'io.collections.IOCollection');
        $this->assertXarUri('lang', $first->getURI());
        $this->assertEquals(0, $first->getSize());
        $this->assertEquals(NULL, $c->next());
      } catch (Throwable $e) {
      } finally(); {
        $c->close();
        if (isset($e)) throw $e;
      }
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function entriesInLang() {
      $c= new ArchiveCollection($this->archive, 'lang');
      
      try {
        $c->open();
        $expect= array(
          'lang/Object.xp'    => 'io.collections.IOElement', 
          'lang/Type.xp'      => 'io.collections.IOElement',
          'lang/reflect'      => 'io.collections.IOCollection',
          'lang/types'        => 'io.collections.IOCollection',
          'lang/Runnable.xp'  => 'io.collections.IOElement',
        );
        for (reset($expect); $element= $c->next(), $name= key($expect); next($expect)) {
          $this->assertSubclass($element, $expect[$name]);
          $this->assertXarUri($name, $element->getURI());
        }
        $this->assertEquals(NULL, $c->next());
      } catch (Throwable $e) {
      } finally(); {
        $c->close();
        if (isset($e)) throw $e;
      }
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
      $this->assertNotEquals(NULL, $first, 'No first element in '.$collection->toString());
      return $first;
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readTwice() {
      $c= new ArchiveCollection($this->archive);
      $this->assertEquals($this->firstElement($c), $this->firstElement($c));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readLangTwice() {
      $c= new ArchiveCollection($this->archive, 'lang');
      $this->assertEquals($this->firstElement($c), $this->firstElement($c));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readObjectEntry() {
      with ($first= $this->firstElement(new ArchiveCollection($this->archive, 'lang'))); {
        $this->assertEquals(
          'class Object { }', 
          $first->getInputStream()->read($first->getSize())
        );
      }
    }

    /**
     * Test
     *
     */
    #[@test, @expect('io.IOException')]
    public function writeObjectEntry() {
      $this->firstElement(new ArchiveCollection($this->archive, 'lang'))->getOutputStream();
    }

    /**
     * Test
     *
     */
    #[@test, @expect('io.IOException')]
    public function readLangEntry() {
      $this->firstElement(new ArchiveCollection($this->archive))->getInputStream();
    }

    /**
     * Test
     *
     */
    #[@test, @expect('io.IOException')]
    public function writeLangEntry() {
      $this->firstElement(new ArchiveCollection($this->archive))->getOutputStream();
    }
  }
?>
