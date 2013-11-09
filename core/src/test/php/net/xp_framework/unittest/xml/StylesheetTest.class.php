<?php namespace net\xp_framework\unittest\xml;

use xml\Stylesheet;
use util\collections\Vector;

/**
 * TestCase for Stylesheet class
 *
 * @see  xp://xml.Stylesheet
 */
class StylesheetTest extends \unittest\TestCase {

  /**
   * Helper method
   *
   * @param   xml.Node starting node
   * @param   string tagname
   * @return  util.collections.Vector<xml.Node>
   */
  protected function getElementsByTagName($node, $tagname) {
    $r= create('new util.collections.Vector<xml.Node>()');
    foreach (array_keys($node->getChildren()) as $key) {
      if ($tagname == $node->nodeAt($key)->getName()) {
        $r[]= $node->nodeAt($key);
      }
      if ($node->nodeAt($key)->hasChildren()) {
        $r->addAll($this->_getElementsByTagName(
          $node->nodeAt($key),
          $tagname
        ));
      }
    }
    return $r;
  }

  #[@test]
  public function emptyStylesheet() {
    $this->assertEquals(
      '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"/>',
      trim(create(new Stylesheet())->getSource(INDENT_DEFAULT))
    );
  }

  #[@test]
  public function setOutputMethod() {
    $s= new Stylesheet();
    $s->setOutputMethod('text', false, 'utf-8');
    
    $this->assertEquals(
      '<xsl:output method="text" encoding="utf-8" indent="no"></xsl:output>',
      trim($this->getElementsByTagName($s->root, 'xsl:output')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function withOutputMethod() {
    $s= create(new Stylesheet())->withOutputMethod('text', false, 'utf-8');
    
    $this->assertEquals(
      '<xsl:output method="text" encoding="utf-8" indent="no"></xsl:output>',
      trim($this->getElementsByTagName($s->root, 'xsl:output')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function addImport() {
    $s= new Stylesheet();
    $s->addImport('portlets/welcome.portlet.xsl');
    
    $this->assertEquals(
      '<xsl:import href="portlets/welcome.portlet.xsl"></xsl:import>',
      trim($this->getElementsByTagName($s->root, 'xsl:import')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function withImport() {
    $s= create(new Stylesheet())->withImport('portlets/welcome.portlet.xsl');
    
    $this->assertEquals(
      '<xsl:import href="portlets/welcome.portlet.xsl"></xsl:import>',
      trim($this->getElementsByTagName($s->root, 'xsl:import')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function addInclude() {
    $s= new Stylesheet();
    $s->addInclude('portlets/welcome.portlet.xsl');
    
    $this->assertEquals(
      '<xsl:include href="portlets/welcome.portlet.xsl"></xsl:include>',
      trim($this->getElementsByTagName($s->root, 'xsl:include')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function withInclude() {
    $s= create(new Stylesheet())->withInclude('portlets/welcome.portlet.xsl');
    
    $this->assertEquals(
      '<xsl:include href="portlets/welcome.portlet.xsl"></xsl:include>',
      trim($this->getElementsByTagName($s->root, 'xsl:include')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function addParam() {
    $s= new Stylesheet();
    $s->addParam('session');
    
    $this->assertEquals(
      '<xsl:param name="session"></xsl:param>',
      trim($this->getElementsByTagName($s->root, 'xsl:param')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function withParam() {
    $s= create(new Stylesheet())->withParam('session');
    
    $this->assertEquals(
      '<xsl:param name="session"></xsl:param>',
      trim($this->getElementsByTagName($s->root, 'xsl:param')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function addVariable() {
    $s= new Stylesheet();
    $s->addVariable('session');
    
    $this->assertEquals(
      '<xsl:variable name="session"></xsl:variable>',
      trim($this->getElementsByTagName($s->root, 'xsl:variable')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function addVariables() {
    $s= new Stylesheet();
    $s->addVariable('session');
    $s->addVariable('language');
    
    $variables= $this->getElementsByTagName($s->root, 'xsl:variable');
    $this->assertEquals(
      '<xsl:variable name="session"></xsl:variable>',
      trim($variables->get(0)->getSource(INDENT_NONE))
    );
    $this->assertEquals(
      '<xsl:variable name="language"></xsl:variable>',
      trim($variables->get(1)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function withVariable() {
    $s= create(new Stylesheet())->withVariable('session');
    
    $this->assertEquals(
      '<xsl:variable name="session"></xsl:variable>',
      trim($this->getElementsByTagName($s->root, 'xsl:variable')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function addMatchTemplate() {
    $s= new Stylesheet();
    $s->addTemplate(create(new \xml\XslTemplate())->matching('/'));
    
    $this->assertEquals(
      '<xsl:template match="/"></xsl:template>',
      trim($this->getElementsByTagName($s->root, 'xsl:template')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function withMatchTemplate() {
    $s= create(new Stylesheet())->withTemplate(create(new \xml\XslTemplate())->matching('/'));
    
    $this->assertEquals(
      '<xsl:template match="/"></xsl:template>',
      trim($this->getElementsByTagName($s->root, 'xsl:template')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function addNamedTemplate() {
    $s= new Stylesheet();
    $s->addTemplate(create(new \xml\XslTemplate())->named('sitemap'));
    
    $this->assertEquals(
      '<xsl:template name="sitemap"></xsl:template>',
      trim($this->getElementsByTagName($s->root, 'xsl:template')->get(0)->getSource(INDENT_NONE))
    );
  }

  #[@test]
  public function withNamedTemplate() {
    $s= create(new Stylesheet())->withTemplate(create(new \xml\XslTemplate())->named('sitemap'));
    
    $this->assertEquals(
      '<xsl:template name="sitemap"></xsl:template>',
      trim($this->getElementsByTagName($s->root, 'xsl:template')->get(0)->getSource(INDENT_NONE))
    );
  }
}
