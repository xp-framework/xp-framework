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
    const NAME = 0;
    const ATTR = 1;
    const CHLD = 2;
    
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
        protected
          $pointer  = array();
          
        public
          $tree     = NULL,
          $elements = array();
          
        public function onStartElement($parser, $name, $attrs) {
          $this->elements[]= $name;
          array_unshift($this->pointer, array($name, $attrs, array()));
        }

        public function onEndElement($parser, $name) {
          $e= array_shift($this->pointer);
          if (empty($this->pointer)) {
            $this->tree= $e;
          } else {
            $this->pointer[0][XMLParserTest::CHLD][]= $e;
          }
        }

        public function onCData($parser, $cdata) {
          $this->pointer[0][XMLParserTest::CHLD][]= trim($cdata);
        }

        public function onDefault($parser, $data) {
          $this->pointer[0][XMLParserTest::CHLD][]= trim($data);
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
     * Test tree parsing
     *
     */
    #[@test]
    public function tree() {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->xml('<book>
        <author><name>Timm</name></author>
        <chapter id="1">
          <title>Introduction</title>
          <paragraph>
            This is where it all started.
          </paragraph>
        </chapter>
      </book>'));
      $this->assertEquals('book', $callback->tree[self::NAME]);
      $this->assertEquals(array(), $callback->tree[self::ATTR]);
      
      with ($author= $callback->tree[self::CHLD][1]); {
        $this->assertEquals('author', $author[self::NAME]);
        $this->assertEquals(array(), $author[self::ATTR]);
      
        with ($name= $author[self::CHLD][0]); {
          $this->assertEquals('name', $name[self::NAME]);
          $this->assertEquals(array(), $name[self::ATTR]);
          $this->assertEquals(array('Timm'), $name[self::CHLD]);
        }
      }

      with ($chapter= $callback->tree[self::CHLD][3]); {
        $this->assertEquals('chapter', $chapter[self::NAME]);
        $this->assertEquals(array('id' => '1'), $chapter[self::ATTR]);

        with ($title= $chapter[self::CHLD][1]); {
          $this->assertEquals('title', $title[self::NAME]);
          $this->assertEquals(array(), $title[self::ATTR]);
          $this->assertEquals(array('Introduction'), $title[self::CHLD]);
        }

        with ($paragraph= $chapter[self::CHLD][3]); {
          $this->assertEquals('paragraph', $paragraph[self::NAME]);
          $this->assertEquals(array(), $paragraph[self::ATTR]);
          $this->assertEquals(array('This is where it all started.'), $paragraph[self::CHLD]);
        }
      }
    }

    /**
     * Test reusability, that is, a parser can be reused after calling parse()
     * on it.
     *
     */
    #[@test]
    public function reusable() {
      for ($i= 0; $i < 4; $i++) {
        $callback= $this->newCallback();
        $this->parser->setCallback($callback);
        $this->parser->parse($this->xml('<run id="'.$i.'"/>'));
        $this->assertEquals(
          array('run', array('id' => (string)$i), array()), 
          $callback->tree, 
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
        $this->assertEquals(NULL, $callback->tree, 'Tree only set if entire doc parsed');

        $this->assertEquals(4, sizeof($callback->elements));
        $this->assertEquals('doc', $callback->elements[0]);
        $this->assertEquals('h1', $callback->elements[1]);
        $this->assertEquals('p', $callback->elements[2]);
        $this->assertEquals('img', $callback->elements[3]);
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
        array('n', array('id' => "'1'", 't' => '"_new"', 'q' => '\'"'), array()),
        $callback->tree
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
        array('a', array('id' => '>'), array()),
        $callback->tree
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
      $this->assertEquals(array(
        'doc', array(), array(
          'CDATA [', '<&>', ']'
        )
      ), $callback->tree);
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

      $this->assertEquals(array(
        'doc', array(), array(
          '<?php echo "1"; ?>'
        )
      ), $callback->tree);
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

      $this->assertEquals(array(
        'doc', array(), array(
          '<!-- Comment -->'
        )
      ), $callback->tree);
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

      $this->assertEquals(array(
        'doc', array(), array(
          '"', '3', '<', '5', "'", '&', "'", '5', '>', '3', '"'
        )
      ), $callback->tree);
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

      $this->assertEquals(array(
        'doc', array(), array(
          '©', '2001-2009 the XP team'
        )
      ), $callback->tree);
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

      $this->assertEquals(array(
        'doc', array(), array(
          'The', 'übercoder returns'
        )
      ), $callback->tree);
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

      $this->assertEquals(array(
        'doc', array(), array(
          'The', 'Ã¼bercoder returns'
        )
      ), $callback->tree);
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

      $this->assertEquals(array(
        'doc', array(), array(
          'Copyright:', '&copy;'
        )
      ), $callback->tree);
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

      $this->assertEquals(array(
        'doc', array(), array(
          'Copyright:', '&copyright;'
        )
      ), $callback->tree);
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

      $this->assertEquals(array(
        'doc', array(), array(
          array('book', array('copyright' => 'Copyright 2009 The XP team'), array()),
        )
      ), $callback->tree);
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

      $this->assertEquals(array(
        'doc', array(), array(
          array('book', array('copyright' => 'Copyright 2009 The XP team'), array()),
        )
      ), $callback->tree);
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

      $this->assertEquals(array(
        'doc', array(), array('Copyright:'),
      ), $callback->tree);
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
