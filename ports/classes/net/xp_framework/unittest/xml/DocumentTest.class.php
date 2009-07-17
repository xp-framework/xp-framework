<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xml.dom.Document'
  );

  /**
   * TestCase
   *
   * @see      xp://xml.dom.Document
   */
  class DocumentTest extends TestCase {
  
    /**
     * Test getElementsByTagName()
     *
     */
    #[@test]
    public function elementsByTagName() {
      $dom= Document::fromString('<list>
        <person id="1549">Timm</person>
        <person id="1552">Alex</person>
      </list>');
      
      $this->assertEquals(
        $dom->getDocumentElement()->children,
        $dom->getElementsByTagName('person')
      );
    }

    /**
     * Test getElementsById()
     *
     */
    #[@test]
    public function elementById() {
      $dom= Document::fromString('<list>
        <person id="1549">Timm</person>
        <person id="1552">Alex</person>
      </list>');
      
      $this->assertEquals(
        $dom->getDocumentElement()->children[0],
        $dom->getElementById('1549')
      );
    }

    /**
     * Test getElementsById()
     *
     */
    #[@test]
    public function noSuchElementById() {
      $this->assertEquals(
        NULL,
        Document::fromString('<list/>')->getElementById('1777')
      );
    }
  }
?>
