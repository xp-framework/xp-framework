<?php
/* This class is part of the XP framework
 *
 * $Id: ReturnTagletTest.class.php 10561 2007-06-07 18:37:13Z friebe $ 
 */

  namespace net::xp_framework::unittest::doclet;

  ::uses(
    'unittest.TestCase',
    'text.doclet.TagletManager',
    'text.doclet.RootDoc'
  );

  /**
   * TestCase for return taglet
   *
   * @see      xp://text.doclet.ReturnTaglet
   * @purpose  Unittest for text.doclet API
   */
  class ReturnTagletTest extends unittest::TestCase {
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->holder= new text::doclet::RootDoc();
    }
    
    /**
     * Create a return tag for a given text
     *
     * @param   string text
     * @return  text.doclet.ReturnTag
     */
    protected function makeReturn($text) {
      return text::doclet::TagletManager::getInstance()->make($this->holder, 'return', $text);
    }
    
    /**
     * Test class return
     *
     */
    #[@test]
    public function classReturn() {
      $t= $this->makeReturn('io.File');
      $this->assertEquals('io.File', $t->type);
      $this->assertEquals('', $t->text);
    }

    /**
     * Test class return
     *
     */
    #[@test]
    public function classReturnWithText() {
      $t= $this->makeReturn('lang.Generic the chosen victim');
      $this->assertEquals('lang.Generic', $t->type);
      $this->assertEquals('the chosen victim', $t->text);
    }

    /**
     * Test primitive return
     *
     */
    #[@test]
    public function primitiveReturn() {
      $t= $this->makeReturn('string');
      $this->assertEquals('string', $t->type);
      $this->assertEquals('', $t->text);
    }

    /**
     * Test array return
     *
     */
    #[@test]
    public function arrayReturn() {
      $t= $this->makeReturn('string[]');
      $this->assertEquals('string[]', $t->type);
      $this->assertEquals('', $t->text);
    }

    /**
     * Test generic return
     *
     */
    #[@test]
    public function genericReturn() {
      $t= $this->makeReturn('array<string, string>');
      $this->assertEquals('array<string, string>', $t->type);
      $this->assertEquals('', $t->text);
    }

    /**
     * Test generic return with fully qualified class names
     *
     */
    #[@test]
    public function qualifiedGenericReturn() {
      $t= $this->makeReturn('util.collection.HashTable<lang.types.Number, lang.types.String>');
      $this->assertEquals('util.collection.HashTable<lang.types.Number, lang.types.String>', $t->type);
      $this->assertEquals('', $t->text);
    }
    
    /**
     * Test generic return with fully qualified class names
     *
     */
    #[@test]
    public function bracketsDoNotConfuseGenerics() {
      $t= $this->makeReturn('int equal: 0, date before $this: < 0, date after $this: >');
      $this->assertEquals('int', $t->type);
      $this->assertEquals('equal: 0, date before $this: < 0, date after $this: >', $t->text);
    }
    
  }
?>
