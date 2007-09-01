<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace name::kiesel::pxl::unittest;

  ::uses(
    'unittest.TestCase',
    'name.kiesel.pxl.Catalogue',
    'name.kiesel.pxl.Page',
    'name.kiesel.pxl.Picture',
    'name.kiesel.pxl.storage.MemoryContainer'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class UnmarshallingTest extends unittest::TestCase {

    /**
     * Compares XML after stripping all whitespace between tags of both 
     * expected and actual strings.
     *
     * @see     xp://unittest.TestCase#assertEquals
     * @param   string expect
     * @param   string actual
     * @return  bool
     */
    public function assertXmlEquals($expect, $actual) {
      return $this->assertEquals(
        preg_replace('#>[\s\r\n]+<#', '><', trim($expect)),
        preg_replace('#>[\s\r\n]+<#', '><', trim($actual))
      );
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function tearDown() {
      // TODO: Fill code that gets executed after every test method
      //       or remove this method
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function loadCatalogue() {
      $catalogue= name::kiesel::pxl::Catalogue::create(new name::kiesel::pxl::storage::MemoryContainer(
        '<?xml version="1.0"?>
        <catalogue>
          <entry id="1" name="foo" path="bar"/>
          <entry id="2" name="foo" path="bar"/>
        </catalogue>
      '));

      $this->assertEquals(2, $catalogue->entries->size());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function saveCatalogue() {
      $c= new name::kiesel::pxl::Catalogue();
      $e= new ();
      $e->setId(1);
      $c->addEntry($e);
      
      $f= $e;
      $f->setId(2);
      $c->addEntry($f);

      $c->setStorage(new name::kiesel::pxl::storage::MemoryContainer());
      $c->hibernate();

      return;
      $this->assertXmlEquals('
        <catalogue>
          <entry
           id="1"
           name=""
           path=""
          />
          <entry 
           id="2" 
           name="" 
           path=""
          />
        </catalogue>',
        $c->storage->data
      );
    }
    
    
    /**
     * Test
     *
     */
    #[@test]
    public function loadPage() {
      $page= name::kiesel::pxl::Page::create(new name::kiesel::pxl::storage::MemoryContainer(
        '<?xml version="1.0"?>
        <page>
          <description>
            Description...
          </description>
          <comments/>
          <trackbacks/>
          <picture>
            <name>First picture</name>
            <date>2006-12-12</date>
            <author>Alex Kiesel</author>
            <filename>pict3020.jpg</filename>
          </picture>
        </page>
      '));
      
      $this->assertEquals('Description...', $page->description);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@test]
    public function loadPicture() {
      $picture= name::kiesel::pxl::Picture::create(new name::kiesel::pxl::storage::MemoryContainer(
        '<?xml version="1.0"?>
        <picture>
          <name>Foobar</name>
          <date>2006-01-01</date>
          <author>Alex Kiesel</author>
          <filename>foo.jpg</filename>
        </picture>
      '));
      
      $this->assertEquals('Alex Kiesel', $picture->getAuthor());
      $this->assertEquals('foo.jpg', $picture->getFilename());
    }
  }
?>
