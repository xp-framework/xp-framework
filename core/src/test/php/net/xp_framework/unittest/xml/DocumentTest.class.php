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
     * Test getElementById()
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
     * Test getElementsByName()
     *
     */
    #[@test]
    public function elementsByName() {
      $dom= Document::fromString('<wizard>
        <step>
          <form name="package">
            <select>...</select>
          </form>
        </step>
      </wizard>');
      
      $this->assertEquals(
        array($dom->getDocumentElement()->children[0]->children[0]),
        $dom->getElementsByName('package')
      );
    }

    /**
     * Test getElementById()
     *
     */
    #[@test]
    public function nestedElementById() {
      $dom= Document::fromString('<html>
        <head>
          <title>Example page</title>
        </head>
        <body>
          <div id="header">
            <ul id="menu">
              <li id="home">Home</li>
            </ul>
          </div>
        </body>
      </html>');
      
      $this->assertEquals(
        $dom->getDocumentElement()->children[1]->children[0]->children[0]->children[0],
        $dom->getElementById('home')
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
