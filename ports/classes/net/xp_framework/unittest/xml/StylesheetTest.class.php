<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xml.Stylesheet',
    'util.collections.Vector'
  );

  /**
   * TestCase
   *
   * @see      xp://xml.Stylesheet
   * @purpose  Unittest
   */
  class StylesheetTest extends TestCase {
  
    /**
     * Helper method
     *
     * @param   xml.Node starting node
     * @param   string tagname
     * @return  util.collections.Vector<xml.Node>
     */
    protected function getElementsByTagName($node, $tagname) {
      $r= create('new util.collections.Vector<xml.Node>()');
      foreach (array_keys($node->children) as $key) {
        if ($tagname == $node->children[$key]->getName()) {
          $r[]= $node->children[$key];
        }
        if (!empty($node->children[$key]->children)) {
          $r->addAll($this->_getElementsByTagName(
            $node->children[$key], 
            $tagname
          ));
        }
      }
      return $r;
    }
  
    /**
     * Test
     *
     */
    #[@test]
    public function emptyStylesheet() {
      $this->assertEquals(
        '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"/>',
        trim(create(new Stylesheet())->getSource(INDENT_DEFAULT))
      );
    }

    /**
     * Test setOutputMethod()
     *
     */
    #[@test]
    public function withOutputMethod() {
      $s= new Stylesheet();
      $s->setOutputMethod('text', FALSE, 'utf-8');
      
      $this->assertEquals(
        '<xsl:output method="text" encoding="utf-8" indent="no"></xsl:output>',
        trim($this->getElementsByTagName($s->root, 'xsl:output')->get(0)->getSource(INDENT_NONE))
      );
    }

    /**
     * Test addImport()
     *
     */
    #[@test]
    public function withImport() {
      $s= new Stylesheet();
      $s->addImport('portlets/welcome.portlet.xsl');
      
      $this->assertEquals(
        '<xsl:import href="portlets/welcome.portlet.xsl"></xsl:import>',
        trim($this->getElementsByTagName($s->root, 'xsl:import')->get(0)->getSource(INDENT_NONE))
      );
    }
  }
?>
