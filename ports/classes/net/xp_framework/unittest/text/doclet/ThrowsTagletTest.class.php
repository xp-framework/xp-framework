<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.doclet.TagletManager',
    'text.doclet.RootDoc',
    'text.doclet.Doc'
  );

  /**
   * TestCase for return taglet
   *
   * @see      xp://text.doclet.ThrowsTaglet
   * @purpose  Unittest for text.doclet API
   */
  class ThrowsTagletTest extends TestCase {
    protected
      $holder  = NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->holder= new Doc();
      $this->holder->setRoot(new RootDoc());
    }
    
    /**
     * Create a return tag for a given text
     *
     * @param   string text
     * @return  text.doclet.ReturnTag
     */
    protected function makeThrows($text) {
      return TagletManager::getInstance()->make($this->holder, 'throws', $text);
    }

    /**
     * Test lang.IllegalArgumentException
     *
     */
    #[@test]
    public function illegalArgumentException() {
      $t= $this->makeThrows('lang.IllegalArgumentException');
      $this->assertClass($t->exception, 'text.doclet.ClassDoc');
      $this->assertEquals('lang.IllegalArgumentException', $t->exception->qualifiedName());
      $this->assertEquals('', $t->text);
    }

    /**
     * Test lang.IllegalArgumentException
     *
     */
    #[@test]
    public function illegalArgumentExceptionWithText() {
      $t= $this->makeThrows('lang.IllegalArgumentException In case the argument is less than zero');
      $this->assertClass($t->exception, 'text.doclet.ClassDoc');
      $this->assertEquals('lang.IllegalArgumentException', $t->exception->qualifiedName());
      $this->assertEquals('In case the argument is less than zero', $t->text);
    }

    /**
     * Test with an exception class that does not exist
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nonExistantException() {
      $this->makeThrows('@@DOES-NOT-EXIST@@');
    }
  }
?>
