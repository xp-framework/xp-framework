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
     * @return  [:string[]] matches
     * @throws  unittest.AssertionFailedError
     */
    protected function parseComment($comment) {
      $details= XPClass::parseDetails('
        <?php
          class Test extends Object {
            '.$comment.'
            public function test() { }
          }
        ?>',
        $this->name
      );
      return $details[1]['test'];
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
         * @see   php://comment
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
         * @param   string param1
         */
      ');
      $this->assertEquals('string', $details[DETAIL_ARGUMENTS][0]);
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
         * @param   string[] param1
         */
      ');
      $this->assertEquals('string[]', $details[DETAIL_ARGUMENTS][0]);
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
         * @param   util.Date param1
         */
      ');
      $this->assertEquals('util.Date', $details[DETAIL_ARGUMENTS][0]);
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
         * @param   int param1 default 1
         */
      ');
      $this->assertEquals('int', $details[DETAIL_ARGUMENTS][0]);
    }
    
    /**
     * Tests parsing of the "param" tag with a map parameter
     *
     */
    #[@test]
    public function mapParameter() {
      $details= $this->parseComment('
        /**
         * Final protected method
         *
         * @param   [:string] map
         */
      ');
      $this->assertEquals('[:string]', $details[DETAIL_ARGUMENTS][0]);
    }

    /**
     * Tests parsing of the "param" tag with a generic parameter
     *
     */
    #[@test]
    public function genericParameterWithTwoComponents() {
      $details= $this->parseComment('
        /**
         * Final protected method
         *
         * @param   util.collection.HashTable<string, util.Traceable> map
         */
      ');
      $this->assertEquals('util.collection.HashTable<string, util.Traceable>', $details[DETAIL_ARGUMENTS][0]);
    }

    /**
     * Tests parsing of the "param" tag with a generic parameter
     *
     */
    #[@test]
    public function genericParameterWithOneComponent() {
      $details= $this->parseComment('
        /**
         * Abstract protected method
         *
         * @param   util.collections.Vector<lang.Object> param1
         */
      ');
      $this->assertEquals('util.collections.Vector<lang.Object>', $details[DETAIL_ARGUMENTS][0]);
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

    /**
     * Tests parsing of classes with closures inside
     *
     * @see   https://github.com/xp-framework/xp-framework/issues/230
     */
    #[@test]
    public function withClosure() {
      $details= XPClass::parseDetails('<?php
        class WithClosure_1 extends Object {

          /**
           * Creates a new answer
           *
           * @return  php.Closure
           */
          public function newAnswer() {
            return function() { return 42; };
          }
        }
      ?>');
      $this->assertEquals('Creates a new answer', $details[1]['newAnswer'][DETAIL_COMMENT]);
    }

    /**
     * Tests parsing of classes with closures inside
     *
     * @see   https://github.com/xp-framework/xp-framework/issues/230
     */
    #[@test]
    public function withClosures() {
      $details= XPClass::parseDetails('<?php
        class WithClosure_2 extends Object {

          /**
           * Creates a new answer
           *
           * @return  php.Closure
           */
          public function newAnswer() {
            return function() { return 42; };
          }

          /**
           * Creates a new question
           *
           * @return  php.Closure
           */
          public function newQuestion() {
            return function() { return NULL; };   /* TODO: Remember question */
          }
        }
      ?>');
      $this->assertEquals('Creates a new question', $details[1]['newQuestion'][DETAIL_COMMENT]);
    }

    /**
     * Returns dummy details
     *
     * @return var details
     */
    protected function dummyDetails() {
      return XPClass::parseDetails('<?php
        class DummyDetails extends Object {
          protected $test = TRUE;

          #[@test]
          public function test() { }
        }
      ?>');
    }

    /**
     * Tests detailsForClass() caching via xp::$meta
     */
    #[@test]
    public function canBeCached() {
      with (xp::$meta[$fixture= 'DummyDetails']= $details= $this->dummyDetails()); {
        $actual= XPClass::detailsForClass($fixture);
        unset(xp::$meta[$fixture]);
      }
      $this->assertEquals($details, $actual);
    }

    /**
     * Tests detailsForClass() caching via xp::registry
     *
     * @deprecated See https://github.com/xp-framework/xp-framework/issues/270
     */
    #[@test]
    public function canBeCachedViaXpRegistry() {
      with (xp::$registry['details.'.($fixture= 'DummyDetails')]= $details= $this->dummyDetails()); {
        $actual= XPClass::detailsForClass($fixture);
        unset(xp::$registry['details.'.$fixture]);
      }
      $this->assertEquals($details, $actual);
    }
  }
?>
