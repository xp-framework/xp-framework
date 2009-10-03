<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xml.Tree',
    'xml.XPath',
    'lang.types.String'
  );

  /**
   * TestCase for XPath class
   *
   * @see      xp://xml.XPath
   * @purpose  Unittest
   */
  class XPathTest extends TestCase {

    /**
     * Test constructor accepts string as argument
     *
     */
    #[@test]
    public function constructWithString() {
      new XPath('<document/>');
    }

    /**
     * Test constructor accepts a php.DOMDocument as argument
     *
     */
    #[@test]
    public function constructWithDomDocument() {
      $d= new DOMDocument();
      $d->appendChild($d->createElement('document'));
      new XPath($d);
    }

    /**
     * Test constructor accepts an xml.Tree as argument
     *
     */
    #[@test]
    public function constructWithTree() {
      new XPath(new Tree('document'));
    }

    /**
     * Test constructor does not accept NULL as argument
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function constructWithNull() {
      new XPath(NULL);
    }

    /**
     * Test query for "/" returns the document
     *
     */
    #[@test]
    public function queryReturnsNodeList() {
      $this->assertEquals(
        'php.DOMNodeList', 
        xp::typeOf(create(new XPath('<document/>'))->query('/'))
      );
    }
  
    /**
     * Test query for "/" returns the document
     *
     */
    #[@test]
    public function slashQueryReturnsDocument() {
      $this->assertEquals(
        'php.DOMDocument', 
        xp::typeOf(create(new XPath('<document/>'))->query('/')->item(0))
      );
    }
    
    /**
     * Returns an XML tree for use in further test cases
     *
     * @return  xml.Tree
     */
    protected function personTree() {
      $t= new Tree('person');
      $t->root->setAttribute('id', '1549');
      $t->addChild(new Node('firstName', 'Timm'));
      $t->addChild(new Node('lastName', 'Friebe'));
      $t->addChild(new Node('location', 'Karlsruhe'));
      $t->addChild(new Node('location', 'Germany'));
      return $t;
    }
    
    /**
     * Test an attribute query
     *
     */
    #[@test]
    public function attributeQuery() {
      $this->assertEquals('1549', create(new XPath($this->personTree()))
        ->query('/person/@id')
        ->item(0)
        ->nodeValue
      );
    }

    /**
     * Test an attribute query
     *
     */
    #[@test]
    public function attributeName() {
      $this->assertEquals('id', create(new XPath($this->personTree()))
        ->query('name(/person/@id)')
      );
    }
  
    /**
     * Test a query with [expr]/text()
     *
     */
    #[@test]
    public function textQuery() {
      $this->assertEquals('Timm', create(new XPath($this->personTree()))
        ->query('/person/firstName/text()')
        ->item(0)
        ->nodeValue
      );
    }

    /**
     * Test a query with name([expr])
     *
     */
    #[@test]
    public function nameQuery() {
      $this->assertEquals('firstName', create(new XPath($this->personTree()))
        ->query('name(/person/firstName)')
      );
    }

    /**
     * Test a query with string([expr])
     *
     */
    #[@test]
    public function stringQuery() {
      $this->assertEquals('Timm', create(new XPath($this->personTree()))
        ->query('string(/person/firstName)')
      );
    }

    /**
     * Test a query for a node that exists twice
     *
     */
    #[@test]
    public function multipleQuery() {
      $locations= create(new XPath($this->personTree()))->query('/person/location');
      
      $this->assertEquals('Karlsruhe', $locations->item(0)->nodeValue);
      $this->assertEquals('Germany', $locations->item(1)->nodeValue);
    }

    /**
     * Test a query for first node of set that exists twice
     *
     */
    #[@test]
    public function offsetQuery() {
      $this->assertEquals('Karlsruhe', create(new XPath($this->personTree()))
        ->query('string(/person/location[1])')
      );
    }

    /**
     * Test an invalid XPath expression throws an exception
     *
     */
    #[@test, @expect('xml.XPathException')]
    public function invalidQuery() {
      create(new XPath('<document/>'))->query(',INVALID,');
    }
    
    /**
     * Query XML document from xml.Tree
     *
     */
    #[@test]
    public function queryTree() {
      $xpath= new XPath(Tree::fromString('<document><node>value</node></document>'));
      
      $this->assertEquals('value', $xpath->query('string(/document/node)'));
    }
    
    /**
     * Query XML document from xml.Tree with encoding given
     *
     */
    #[@test]
    public function queryTreeWithEncoding() {
      $value= new String('value öäü', 'ISO-8859-1');
      $xpath= new XPath(Tree::fromString(sprintf(
        '<?xml version="1.0" encoding="iso-8859-1"?>'.
        '<document><node>%s</node></document>',
        $value
      )));

      $this->assertTrue($value->getBytes('UTF-8') == $xpath->query('string(/document/node)'));
    }
    
    /**
     * Query XML document from xml.Tree with default encoding
     *
     */
    #[@test]
    public function queryTreeWithDefaultEncoding() {
      $value= new String('value Ã¶Ã¤Ã¼', 'UTF-8');
      $xpath= new XPath($s= sprintf(
        '<document><node>%s</node></document>',
        $value->getBytes('UTF-8')
      ));

      $this->assertTrue($value->getBytes('UTF-8') == $xpath->query('string(/document/node)'));
    }
  }
?>
