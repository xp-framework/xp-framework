<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.doclet.TagletManager',
    'text.doclet.RootDoc'
  );

  /**
   * TestCase for see taglet
   *
   * @see      xp://text.doclet.SeeTaglet
   * @purpose  Unittest for text.doclet API
   */
  class SeeTagletTest extends TestCase {
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->holder= new RootDoc();
    }
    
    /**
     * Create a see tag for a given text
     *
     * @param   string text
     * @return  text.doclet.SeeTag
     */
    protected function makeReference($text) {
      return TagletManager::getInstance()->make($this->holder, 'see', $text);
    }
    
    /**
     * Test reference to an XP class
     *
     */
    #[@test]
    public function classReference() {
      $t= $this->makeReference('xp://lang.XPClass');
      $this->assertEquals('xp', $t->scheme);
      $this->assertEquals('lang.XPClass', $t->urn);
      $this->assertEquals('', $t->text);
    }

    /**
     * Test reference to an XP class
     *
     */
    #[@test]
    public function classReferenceWithText() {
      $t= $this->makeReference('xp://lang.XPClass Entry point class for reflection');
      $this->assertEquals('xp', $t->scheme);
      $this->assertEquals('lang.XPClass', $t->urn);
      $this->assertEquals('Entry point class for reflection', $t->text);
    }

    /**
     * Test reference to an XP class' method
     *
     */
    #[@test]
    public function classMethodReference() {
      $t= $this->makeReference('xp://lang.XPClass#forName');
      $this->assertEquals('xp', $t->scheme);
      $this->assertEquals('', $t->text);
      $this->assertEquals('lang.XPClass#forName', $t->urn);
    }

    /**
     * Test reference to a PHP function
     *
     */
    #[@test]
    public function phpFunctionReference() {
      $t= $this->makeReference('php://urlencode');
      $this->assertEquals('php', $t->scheme);
      $this->assertEquals('urlencode', $t->urn);
      $this->assertEquals('', $t->text);
    }

    /**
     * Test reference to a HTTP url
     *
     */
    #[@test]
    public function httpUrlReference() {
      $t= $this->makeReference('http://example.com/');
      $this->assertEquals('http', $t->scheme);
      $this->assertEquals('example.com/', $t->urn);
      $this->assertEquals('', $t->text);
    }
  }
?>
