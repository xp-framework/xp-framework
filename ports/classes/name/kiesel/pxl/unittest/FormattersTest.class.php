<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'name.kiesel.pxl.format.NewlineFormatter',
    'name.kiesel.pxl.format.URLFormatter',
    'name.kiesel.pxl.format.SimpleMarkupFormatter'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class FormattersTest extends TestCase {
  
    /**
     * Test
     *
     */
    #[@test]
    public function testNewlineFormatter() {
      $f= new NewlineFormatter();
      
      $this->assertEquals(
        'This is a <br/> newline.',
        $f->apply("This is a \n newline.")
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testURLFormatter() {
      $f= new URLFormatter();
      
      $this->assertEquals(
        '<a href="http://localhost/">http://localhost/</a>',
        $f->apply('http://localhost/')
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testBold() {
      $f= new SimpleMarkupFormatter();
      
      $this->assertEquals('<b>bold</b>', $f->apply('*bold*'));
      $this->assertEquals('This is <b>SPARTA</b>!', $f->apply('This is *SPARTA*!'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testItalic() {
      $f= new SimpleMarkupFormatter();
      
      $this->assertEquals('<i>italic</i>', $f->apply('/italic/'));
      $this->assertEquals('This is <i>damn italic</i>!', $f->apply('This is /damn italic/!'));
    }    

    /**
     * Test
     *
     */
    #[@test]
    public function testUnderline() {
      $f= new SimpleMarkupFormatter();
      
      $this->assertEquals('<u>underlined</u>', $f->apply('_underlined_'));
      $this->assertEquals('This is <u>emphasized</u>.', $f->apply('This is _emphasized_.'));
    }    

    
    /**
     * Test
     *
     */
    #[@test]
    public function testCombinedSimple() {
      $f= new SimpleMarkupFormatter();
      $this->assertEquals('This is still <b>/bold/</b>', $f->apply('This is still */bold/*'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function nonMatchingEnddelimiter() {
      $f= new SimpleMarkupFormatter();
      $this->assertEquals(
        'This should *not/ be <u>formatted</u> in <i>any</i> way.',
        $f->apply('This should *not/ be _formatted_ in /any/ way.')
      );
    }
    
    /**
     * Test
     *
     */
    #[@test, @ignore]
    public function testLists() {
      $f= new SimpleMarkupFormatter();
      $this->assertEquals(
        "This should be \n* a\n* simple\n* <b>bold<b> list\n",
        $f->format("This should be \n* a\n* simple\n* *bold* list\n")
      );
    }
    
    /**
     * Test
     *
     */
    #[@test, @ignore]
    public function multiline() {
      $f= new SimpleMarkupFormatter();
      $this->assertEquals(
        "This\nshould\nbe\n<b>bold</b>\n",
        $f->format("This\nshould\nbe\n*bold*\n")
      );
    }
  }
?>
