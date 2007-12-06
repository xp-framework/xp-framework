<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xml.XSLCallback',
    'xml.DomXSLProcessor'
  );

  /**
   * TestCase for XSL callbacks
   *
   * @see      xp://xml.XSLCallback
   * @purpose  Unittest
   */
  class XslCallbackTest extends TestCase {

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      XSLCallback::getInstance()->registerInstance('this', $this);
    }

    /**
     * Tears down case
     *
     */
    public function tearDown() {
      XSLCallback::getInstance()->clearInstances();
    }

    /**
     * Runs a transformation
     *
     * @param   string xml
     * @param   string callback
     * @param   string[] arguments
     * @return  string
     */
    protected function runTransformation($xml, $callback, $arguments) {
      sscanf($callback, '%[^:]::%s', $name, $method);
      $p= new DomXSLProcessor();
      $p->setXMLBuf($xml);
      $p->setXSLBuf(sprintf('
        <xsl:stylesheet 
         version="1.0" 
         xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
         xmlns:php="http://php.net/xsl"
        >
          <xsl:output method="text"/>
          
          <xsl:template match="/">
            <xsl:value-of select="php:function(\'XSLCallback::invoke\', \'%s\', \'%s\'%s)"/>
          </xsl:template>
        </xsl:stylesheet>
        ',
        $name,
        $method,
        $arguments ? ', '.implode(', ', $arguments) : ''
      ));
      $p->run();
      return $p->output();
    }
    
    /**
     * Simple XSL callback method
     *
     * @param   string name default 'World'
     * @return  string
     */
    #[@xslmethod]
    public function sayHello($name= 'World') {
      return 'Hello '.$name;
    }
    
    /**
     * Test simple XSL callback method
     *
     */
    #[@test]
    public function callSayHello() {
      $this->assertEquals('Hello Test', $this->runTransformation(
        '<document/>', 
        'this::sayHello',
        array("'Test'")
      ));
    }

    /**
     * Test simple XSL callback method and omitting sayHello()'s optional
     * name argument
     *
     */
    #[@test]
    public function callSayHelloOmittingOptionalParameter() {
      $this->assertEquals('Hello World', $this->runTransformation(
        '<document/>', 
        'this::sayHello',
        array()
      ));
    }

    /**
     * Test calling function of not-registered callback
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function callOnNotRegisteredCallback() {
      $this->runTransformation('<irrelevant/>', 'not-registered::irrelevant', array());
    }

    /**
     * Test calling a method without xslmethod annotation
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function callNonXslMethod() {
      $this->runTransformation('<irrelevant/>', 'this::setUp', array());
    }

    /**
     * Test calling a non-existant method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function callNonExistantMethod() {
      $this->runTransformation('<irrelevant/>', 'this::nonExistantMethod', array());
    }
  }
?>
