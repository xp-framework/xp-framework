<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xml.parser.XMLParser',
    'xml.parser.ParserCallback'
  );

  /**
   * TestCase
   *
   * @see      xp://xml.parser.ParserCallback
   * @see      xp://xml.parser.XMLParser
   */
  class XMLParserTest extends TestCase {
    protected
      $parser = NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->parser= new XMLParser();
    }
    
    /**
     * Tears down test case
     *
     */
    public function tearDown() {
      delete($this->parser);
    }
    
    /**
     * Creates a new callback
     *
     * @return  xml.parser.ParserCallback
     */
    protected function newCallback() {
      return newinstance('xml.parser.ParserCallback', array(), '{
        public 
          $elements = array(),
          $cdata    = array(),
          $default  = array();
          
        public function onStartElement($parser, $name, $attrs) {
          $this->elements[]= array($name, $attrs);
        }

        public function onEndElement($parser, $name) {
          // NOOP
        }

        public function onCData($parser, $cdata) {
          $this->cdata[]= $cdata;
        }

        public function onDefault($parser, $data) {
          $this->default[]= $data;
        }
      }');
    }
    
    /**
     * Returns an XML document as a string by prepending the XML 
     * declaration to the given string and returning it.
     *
     * @param   string str
     * @return  string XML
     */
    protected function xml($str) {
      return '<?xml version="1.0" encoding="utf-8"?>'.$str;
    }

    /**
     * Test
     *
     */
    #[@test]
    public function withoutDeclaration() {
      $this->assertTrue($this->parser->parse('<root/>'));
    }

    /**
     * Test parsing an empty string
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function emptyString() {
      $this->parser->parse('');
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function withDeclaration() {
      $this->assertTrue($this->parser->parse($this->xml('<root/>')));
    }

    /**
     * Test reusability, that is, a parser can be reused after calling parse()
     * on it.
     *
     */
    #[@test]
    public function reusable() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      for ($i= 0; $i < 4; $i++) {
        $this->parser->parse($this->xml('<run id="'.$i.'"/>'));
      }
      $this->assertEquals(4, sizeof($callback->elements));
      for ($i= 0; $i < 4; $i++) {
        $this->assertEquals(
          array('run', array('id' => (string)$i)), 
          $callback->elements[$i], 
          'Run #'.$i
        );
      }
    }

    /**
     * Test error is thrown late in the process
     *
     */
    #[@test]
    public function errorOccursLate() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      try {
        $this->parser->parse($this->xml('<doc><h1>Title</h1><p>Text</p><img></doc>'));
        $this->fail('Parsed without problems', NULL, 'xml.XMLFormatException');
      } catch (XMLFormatException $expected) {
        $this->assertEquals(4, sizeof($callback->elements));
        $this->assertEquals('doc', $callback->elements[0][0]);
        $this->assertEquals('h1', $callback->elements[1][0]);
        $this->assertEquals('p', $callback->elements[2][0]);
        $this->assertEquals('img', $callback->elements[3][0]);

        $this->assertEquals(2, sizeof($callback->cdata));
        $this->assertEquals('Title', $callback->cdata[0]);
        $this->assertEquals('Text', $callback->cdata[1]);
      }
    }

    /**
     * Test a document without a root node
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function withoutRoot() {
      $this->parser->parse($this->xml(''));
    }

    /**
     * Test an unclosed tag
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function unclosedTag() {
      $this->parser->parse($this->xml('<a>'));
    }

    /**
     * Test an unclosed attribute
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function unclosedAttribute() {
      $this->parser->parse($this->xml('<a href="http://>Click</a>'));
    }

    /**
     * Test an unclosed comment
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function unclosedComment() {
      $this->parser->parse($this->xml('<doc><!-- Comment</doc>'));
    }

    /**
     * Test an incorrectly closed comment
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function incorrectlyClosedComment() {
      $this->parser->parse($this->xml('<doc><!-- Comment ></doc>'));
    }

    /**
     * Test a malformed comment
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function malformedComment() {
      $this->parser->parse($this->xml('<doc><! Comment --></doc>'));
    }

    /**
     * Test an unclosed PI ("processing instruction")
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function unclosedProcessingInstruction() {
      $this->parser->parse($this->xml('<doc><?php echo "1"; </doc>'));
    }

    /**
     * Test attribute redefinition
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function attributeRedefinition() {
      $this->parser->parse($this->xml('<a id="1" id="2"/>'));
    }

    /**
     * Test quotes inside attributes
     *
     */
    #[@test]
    public function quotesInsideAttributes() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('<n id="\'1\'" t=\'"_new"\' q="&apos;&quot;"/>'));
      $this->assertEquals(
        array('n', array('id' => "'1'", 't' => '"_new"', 'q' => '\'"')),
        $callback->elements[0]
      );
    }

    /**
     * Test unquoted greater sign inside an attribute
     *
     */
    #[@test]
    public function greaterSignInAttribute() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('<a id=">"/>'));
      $this->assertEquals(
        array('a', array('id' => '>')),
        $callback->elements[0]
      );
    }

    /**
     * Test unquoted smaller sign inside an attribute
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function smallerSignInAttribute() {
      $this->parser->parse($this->xml('<a id="<"/>'));
    }

    /**
     * Test CDATA
     *
     */
    #[@test]
    public function cdataSection() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('
        <doc>CDATA [<![CDATA[ <&> ]]>]</doc>
      '));
      $this->assertEquals('CDATA [ <&> ]', implode('', $callback->cdata));
    }

    /**
     * Test processing instructions
     *
     */
    #[@test]
    public function processingInstruction() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('<doc><?php echo "1"; ?></doc>'));
      $this->assertEquals('<?php echo "1"; ?>', $callback->default[0]);
    }

    /**
     * Test comments
     *
     */
    #[@test]
    public function comment() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('<doc><!-- Comment --></doc>'));
      $this->assertEquals('<!-- Comment -->', $callback->default[0]);
    }

    /**
     * Test nested CDATA is not allowed
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function nestedCdata() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('
        <doc><![CDATA[ <![CDATA[ ]]> ]]></doc>
      '));
    }

    /**
     * Test predefined entities
     *
     */
    #[@test]
    public function predefinedEntities() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('
        <doc>&quot;3 &lt; 5 &apos;&amp;&apos; 5 &gt; 3&quot;</doc>
      '));
      $this->assertEquals('"3 < 5 \'&\' 5 > 3"', implode('', $callback->cdata));
    }

    /**
     * Test hex entity
     *
     */
    #[@test]
    public function hexEntity() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('
        <doc>&#169; 2001-2009 the XP team</doc>
      '));
      $this->assertEquals('© 2001-2009 the XP team', implode('', $callback->cdata));
    }

    /**
     * Test umlauts returned in ISO-8859-1
     *
     */
    #[@test]
    public function iso88591Conversion() {
      $this->parser->setEncoding('ISO-8859-1');
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('
        <doc>The Ã¼bercoder returns</doc>
      '));
      $this->assertEquals('The übercoder returns', implode('', $callback->cdata));
    }

    /**
     * Test umlauts returned in UTF-8
     *
     */
    #[@test]
    public function utf8Conversion() {
      $this->parser->setEncoding('UTF-8');
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('
        <doc>The Ã¼bercoder returns</doc>
      '));
      $this->assertEquals('The Ã¼bercoder returns', implode('', $callback->cdata));
    }

    /**
     * Test undeclared entity
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function undeclaredEntity() {
      $this->parser->parse($this->xml('<doc>&nbsp;</doc>'));
    }

    /**
     * Test undeclared entity inside an attribute
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function undeclaredEntityInAttribute() {
      $this->parser->parse($this->xml('<doc><a href="&nbsp;"/></doc>'));
    }

    /**
     * Test double root node
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function doubleRoot() {
      $this->parser->parse($this->xml('<doc/><doc/>'));
    }

    /**
     * Test DOCTYPE section without content
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function docTypeWithoutContent() {
      $this->parser->parse($this->xml('<!DOCTYPE doc ]>'));
    }

    /**
     * Test entity declared via ENTITY
     *
     */
    #[@test]
    public function characterEntity() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('
        <!DOCTYPE doc [ <!ENTITY copy "&#169;"> ]>
        <doc>Copyright: &copy;</doc>
      '));
      $this->assertEquals('Copyright: ', $callback->cdata[0]);
      $this->assertEquals('&copy;', $callback->default[0]);
    }

    /**
     * Test entity declared via ENTITY
     *
     */
    #[@test]
    public function entity() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('
        <!DOCTYPE doc [ <!ENTITY copyright "2009 The XP team"> ]>
        <doc>Copyright: &copyright;</doc>
      '));
      $this->assertEquals('Copyright: ', $callback->cdata[0]);
      $this->assertEquals('&copyright;', $callback->default[0]);
    }

    /**
     * Test entity declared via ENTITY and used inside an attribute
     *
     */
    #[@test]
    public function entityInAttribute() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('
        <!DOCTYPE doc [ <!ENTITY copyright "2009 The XP team"> ]>
        <doc><book copyright="Copyright &copyright;"/></doc>
      '));
      $this->assertEquals(
        array('book', array('copyright' => 'Copyright 2009 The XP team')),
        $callback->elements[1]
      );
    }

    /**
     * Test entity expansion
     *
     */
    #[@test]
    public function entityExpansion() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('
        <!DOCTYPE doc [ 
          <!ENTITY year "2009"> 
          <!ENTITY copyright "&year; The XP team"> 
        ]>
        <doc><book copyright="Copyright &copyright;"/></doc>
      '));
      $this->assertEquals(
        array('book', array('copyright' => 'Copyright 2009 The XP team')),
        $callback->elements[1]
      );
    }

    /**
     * Test external entity declared via ENTITY
     *
     */
    #[@test]
    public function externalEntity() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('
        <!DOCTYPE doc [ <!ENTITY copyright SYSTEM "http://xp-framework.net/copyright.txt" > ]>
        <doc>Copyright: &copyright;</doc>
      '));
      $this->assertEquals('Copyright: ', $callback->cdata[0]);
      $this->assertEquals(array(), $callback->default);
    }

    /**
     * Test external entity declared via ENTITY and used inside an 
     * attribute raises a XML_ERR_ENTITY_IS_EXTERNAL
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function externalEntityInAttribute() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('
        <!DOCTYPE doc [ <!ENTITY copyright SYSTEM "http://xp-framework.net/copyright.txt" > ]>
        <doc><book copyright="Copyright &copyright;"/></doc>
      '));
    }
  }
?>
