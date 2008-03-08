<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

  define('APIDOC_TAG',        0x0001);
  define('APIDOC_VALUE',      0x0002);

  /**
   * Tests the class details gathering internals
   *
   * @see      xp://lang.XPClass#detailsForClass
   * @purpose  Unit test
   */
  class ClassDetailsTest extends TestCase {
  
    /**
     * Helper method that parses an apidoc comment and returns the matches
     *
     * @param   string comment
     * @return  array<string[]> matches
     * @throws  unittest.AssertionFailedError
     */
    protected function parseComment($comment) {
      $comment= trim($comment);
      if (!preg_match_all(
        '/@([a-z]+)\s*([^<\r\n]+<[^>]+>|[^\r\n ]+) ?([^\r\n ]+)? ?(default ([^\r\n ]+))?/', 
        $comment,
        $matches, 
        PREG_SET_ORDER
      )) {
        $this->fail('Could not parse comment', $actual= FALSE, $expect= TRUE);
        return;
      }

      // Set these to empty values
      $annotations= array();
      $name= NULL;  
      
      // Initialize details array
      $details= array(
        DETAIL_ARGUMENTS    => array(),
        DETAIL_RETURNS      => 'void',
        DETAIL_THROWS       => array(),
        DETAIL_COMMENT      => trim(preg_replace('/\n\s*\* ?/', "\n", "\n".substr(
          $comment, 
          4,                              // "/**\n"
          strpos($comment, '* @')- 2      // position of first details token
        ))),
        DETAIL_ANNOTATIONS  => $annotations,
        DETAIL_NAME         => $name
      );
      
      foreach ($matches as $match) {
        switch ($match[1]) {
          case 'param':
            $details[DETAIL_ARGUMENTS][]= $match[2];
            break;

          case 'return':
            $details[DETAIL_RETURNS]= $match[2];
            break;

          case 'throws': 
            $details[DETAIL_THROWS][]= $match[2];
            break;
        }
      }
      
      return $details;
    }
    
    /**
     * Tests the parseComment() helper
     *
     */
    #[@test, @expect('unittest.AssertionFailedError')]
    public function testParseComment() {
      $this->parseComment('NOT-A-COMMENT');
    }
    
    /**
     * Protected helper method
     *
     * @param   int modifiers
     * @param   string comment
     * @return  bool
     * @throws  unittest.AssertionFailedError
     */
    public function assertAccessFlags($modifiers, $comment) {
      if (!($details= $this->parseComment($comment))) return;
      return $this->assertEquals($modifiers, $details[DETAIL_MODIFIERS]);
    }
    
    /**
     * Tests separation of the comment from the "tags part".
     *
     */
    #[@test]
    public function commentString() {
      $details= $this->parseComment('
        /**
         * A protected method
         *
         * Note: Not compatible with PHP 4.1.2!
         *
         * @access  protected
         * @param   string param1
         */
      ');
      $this->assertEquals(
        "A protected method\n\nNote: Not compatible with PHP 4.1.2!",
        $details[DETAIL_COMMENT]
      );
    }

    /**
     * Tests comment is empty when no comment is available in apidoc
     *
     */
    #[@test]
    public function noCommentString() {
      $details= $this->parseComment('
        /**
         * @access  protected
         */
      ');
      $this->assertEquals(
        '',
        $details[DETAIL_COMMENT]
      );
    }
    
    /**
     * Tests parsing of the "param" tag with a scalar parameter
     *
     */
    #[@test]
    public function scalarParameter() {
      $details= $this->parseComment('
        /**
         * A protected method
         *
         * @access  protected
         * @param   string param1
         */
      ');
      if ($this->assertClass($details[DETAIL_ARGUMENTS][0], 'lang.reflect.Argument')) {
        $this->assertEquals('param1', $details[DETAIL_ARGUMENTS][0]->getName());
        $this->assertEquals('string', $details[DETAIL_ARGUMENTS][0]->getType());
        $this->assertFalse($details[DETAIL_ARGUMENTS][0]->isOptional());
        $this->assertFalse($details[DETAIL_ARGUMENTS][0]->isPassedByReference());
      }
    }

    /**
     * Tests parsing of the "param" tag with an array parameter
     *
     */
    #[@test]
    public function arrayParameter() {
      $details= $this->parseComment('
        /**
         * Another protected method
         *
         * @access  protected
         * @param   string[] param1
         */
      ');
      if ($this->assertClass($details[DETAIL_ARGUMENTS][0], 'lang.reflect.Argument')) {
        $this->assertEquals('param1', $details[DETAIL_ARGUMENTS][0]->getName());
        $this->assertEquals('string[]', $details[DETAIL_ARGUMENTS][0]->getType());
        $this->assertFalse($details[DETAIL_ARGUMENTS][0]->isOptional());
        $this->assertFalse($details[DETAIL_ARGUMENTS][0]->isPassedByReference());
      }
    }

    /**
     * Tests parsing of the "param" tag with an object parameter
     *
     */
    #[@test]
    public function objectParameter() {
      $details= $this->parseComment('
        /**
         * Yet another protected method
         *
         * @access  protected
         * @param   &util.Date param1
         */
      ');
      if ($this->assertClass($details[DETAIL_ARGUMENTS][0], 'lang.reflect.Argument')) {
        $this->assertEquals('param1', $details[DETAIL_ARGUMENTS][0]->getName());
        $this->assertEquals('util.Date', $details[DETAIL_ARGUMENTS][0]->getType());
        $this->assertFalse($details[DETAIL_ARGUMENTS][0]->isOptional());
        $this->assertTrue($details[DETAIL_ARGUMENTS][0]->isPassedByReference());
      }
    }

    /**
     * Tests parsing of the "param" tag with a parameter with default value
     *
     */
    #[@test]
    public function defaultParameter() {
      $details= $this->parseComment('
        /**
         * A private method
         *
         * @access  private
         * @param   int param1 default 1
         */
      ');
      if ($this->assertClass($details[DETAIL_ARGUMENTS][0], 'lang.reflect.Argument')) {
        $this->assertEquals('param1', $details[DETAIL_ARGUMENTS][0]->getName());
        $this->assertEquals('int', $details[DETAIL_ARGUMENTS][0]->getType());
        $this->assertTrue($details[DETAIL_ARGUMENTS][0]->isOptional());
        $this->assertFalse($details[DETAIL_ARGUMENTS][0]->isPassedByReference());
        $this->assertEquals('1', $details[DETAIL_ARGUMENTS][0]->getDefault());
      }
    }
    
    /**
     * Tests parsing of the "param" tag with an generic parameter
     *
     */
    #[@test]
    public function genericArrayParameter() {
      $details= $this->parseComment('
        /**
         * Final protected method
         *
         * @model   final
         * @access  protected
         * @param   array<string, string> map
         */
      ');
      if ($this->assertClass($details[DETAIL_ARGUMENTS][0], 'lang.reflect.Argument')) {
        $this->assertEquals('map', $details[DETAIL_ARGUMENTS][0]->getName());
        $this->assertEquals('array<string, string>', $details[DETAIL_ARGUMENTS][0]->getType());
        $this->assertFalse($details[DETAIL_ARGUMENTS][0]->isOptional());
        $this->assertFalse($details[DETAIL_ARGUMENTS][0]->isPassedByReference());
      }
    }

    /**
     * Tests parsing of the "param" tag with an generic parameter
     *
     */
    #[@test]
    public function genericObjectParameter() {
      $details= $this->parseComment('
        /**
         * Abstract protected method
         *
         * @model   abstract
         * @access  protected
         * @param   &lang.Collection<&lang.Object> param1
         */
      ');
      if ($this->assertClass($details[DETAIL_ARGUMENTS][0], 'lang.reflect.Argument')) {
        $this->assertEquals('param1', $details[DETAIL_ARGUMENTS][0]->getName());
        $this->assertEquals('lang.Collection<&lang.Object>', $details[DETAIL_ARGUMENTS][0]->getType());
        $this->assertFalse($details[DETAIL_ARGUMENTS][0]->isOptional());
        $this->assertTrue($details[DETAIL_ARGUMENTS][0]->isPassedByReference());
      }
    }
    
    /**
     * Tests parsing of the "throws" tag
     *
     */
    #[@test]
    public function throwsList() {
      $details= $this->parseComment('
        /**
         * Test method
         *
         * @throws  lang.IllegalArgumentException
         * @throws  lang.IllegalAccessException
         */
      ');
      $this->assertEquals('lang.IllegalArgumentException', $details[DETAIL_THROWS][0]);
      $this->assertEquals('lang.IllegalAccessException', $details[DETAIL_THROWS][1]);
    }
 
     /**
     * Tests parsing of the "return" tag
     *
     */
    #[@test]
    public function returnType() {
      $details= $this->parseComment('
        /**
         * Test method
         *
         * @return  int
         */
      ');
      $this->assertEquals('int', $details[DETAIL_RETURNS]);
    }
 }
?>
