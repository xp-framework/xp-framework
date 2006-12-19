<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
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
  class UnmarshallingTest extends TestCase {

    /**
     * Compares XML after stripping all whitespace between tags of both 
     * expected and actual strings.
     *
     * @see     xp://unittest.TestCase#assertEquals
     * @access  public
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
     * @access  
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
     * @access  public
     */
    #[@test]
    public function loadCatalogue() {
      $catalogue= &Catalogue::create(new MemoryContainer(
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
     * @access  public
     */
    #[@test]
    public function saveCatalogue() {
      $c= new Catalogue();
      $e= new CatalogueEntry();
      $e->setId(1);
      $c->addEntry($e);
      
      $f= $e;
      $f->setId(2);
      $c->addEntry($f);

      $c->setStorage(new MemoryContainer());
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
     * @access  public
     */
    #[@test]
    public function loadPage() {
      $page= &Page::create(new MemoryContainer(
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
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    public function loadPicture() {
      $picture= &Picture::create(new MemoryContainer(
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
