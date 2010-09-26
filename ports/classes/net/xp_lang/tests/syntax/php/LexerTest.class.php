<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.syntax.php';

  uses(
    'unittest.TestCase',
    'xp.compiler.syntax.php.Parser',
    'xp.compiler.syntax.php.Lexer'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.syntax.php.Lexer
   */
  abstract class net·xp_lang·tests·syntax·php·LexerTest extends TestCase {

    /**
     * Creates a lexer instance
     *
     * @param   string in
     * @return  xp.compiler.syntax.php.Lexer
     */
    protected abstract function newLexer($in);
  
    /**
     * Returns an array of tokens for a given input string
     *
     * @param   string in
     * @return  array<int, string>[] tokens
     */
    protected function tokensOf($in) {
      $l= $this->newLexer('<?php '.$in.'?>');
      $tokens= array();
      do {
        try {
          if ($r= $l->advance()) {
            $tokens[]= array($l->token, $l->value, $l->position);
          }
        } catch (Throwable $e) {
          $tokens[]= array($e->getClassName(), $e->getMessage());
          $r= FALSE;
        }
      } while ($r);
      return $tokens;
    }
  
    /**
     * Test parsing a class net·xp_lang·tests·syntax·php·declaration
     *
     */
    #[@test]
    public function classDeclaration() {
      $t= $this->tokensOf('class Point { }');
     $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_CLASS, 'class', array(1, 6)), $t[0]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_WORD, 'Point', array(1, 12)), $t[1]);
      $this->assertEquals(array(123, '{', array(1, 18)), $t[2]);
      $this->assertEquals(array(125, '}', array(1, 20)), $t[3]);
    }

    /**
     * Test parsing a one-line comment at the end of a line
     *
     */
    #[@test]
    public function commentAtEnd() {
      $t= $this->tokensOf('$a++; // HACK');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 'a', array(1, 6)), $t[0]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_INC, '++', array(1, 8)), $t[1]);
      $this->assertEquals(array(59, ';', array(1, 10)), $t[2]);
    }

    /**
     * Test parsing a doc-comment
     *
     */
    #[@test]
    public function docComment() {
      $t= $this->tokensOf('
        /**
         * Doc-Comment
         *
         * @see http://example.com
         */  
        public void init() { }
      ');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_PUBLIC, 'public', array(7, 9)), $t[0]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_WORD, 'void', array(7, 16)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_WORD, 'init', array(7, 21)), $t[2]);
      $this->assertEquals(array(40, '(', array(7, 25)), $t[3]);
      $this->assertEquals(array(41, ')', array(7, 26)), $t[4]);
      $this->assertEquals(array(123, '{', array(7, 28)), $t[5]);
      $this->assertEquals(array(125, '}', array(7, 30)), $t[6]);
    }

    /**
     * Test parsing a double-quoted string
     *
     */
    #[@test]
    public function dqString() {
      $t= $this->tokensOf('$s= "Hello World";');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_STRING, 'Hello World', array(1, 10)), $t[2]);
      $this->assertEquals(array(59, ';', array(1, 23)), $t[3]);
    }

    /**
     * Test parsing an empty double-quoted string
     *
     */
    #[@test]
    public function emptyDqString() {
      $t= $this->tokensOf('$s= "";');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_STRING, '', array(1, 10)), $t[2]);
      $this->assertEquals(array(59, ';', array(1, 12)), $t[3]);
    }

    /**
     * Test parsing an multi-line double-quoted string
     *
     */
    #[@test]
    public function multiLineDqString() {
      $t= $this->tokensOf('$s= "'."\n\n".'";');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_STRING, "\n\n", array(1, 10)), $t[2]);
      $this->assertEquals(array(59, ';', array(3, 2)), $t[3]);
    }

    /**
     * Test parsing a double-quoted string with escapes
     *
     */
    #[@test]
    public function dqStringWithEscapes() {
      $t= $this->tokensOf('$s= "\"Hello\", he said";');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_STRING, '"Hello", he said', array(1, 10)), $t[2]);
      $this->assertEquals(array(59, ';', array(1, 30)), $t[3]);
    }

    /**
     * Test escape sequences
     *
     */
    #[@test]
    public function escapeSequences() {
      foreach (array('r' => "\r", 'n' => "\n", 't' => "\t", '\\' => "\\") as $escape => $expanded) {
        $t= $this->tokensOf('$s= "{\\'.$escape.'}";');
        $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
        $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
        $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_STRING, '{'.$expanded.'}', array(1, 10)), $t[2], $escape);
        $this->assertEquals(array(59, ';', array(1, 16)), $t[3]);
      }
    }

    /**
     * Test illegal escape sequence
     *
     */
    #[@test]
    public function illegalEscapeSequence() {
      $t= $this->tokensOf('$s= "Hell\ü";');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array('lang.FormatException', 'Illegal escape sequence \\ü in Hell\\ü starting at line 1, offset 10'), $t[2]);
    }

    /**
     * Test parsing a single-quoted string
     *
     */
    #[@test]
    public function sqString() {
      $t= $this->tokensOf('$s= \'Hello World\';');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_STRING, 'Hello World', array(1, 10)), $t[2]);
      $this->assertEquals(array(59, ';', array(1, 23)), $t[3]);
    }

    /**
     * Test parsing an empty single-quoted string
     *
     */
    #[@test]
    public function emptySqString() {
      $t= $this->tokensOf('$s= \'\';');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_STRING, '', array(1, 10)), $t[2]);
      $this->assertEquals(array(59, ';', array(1, 12)), $t[3]);
    }

    /**
     * Test parsing an multi-line single-quoted string
     *
     */
    #[@test]
    public function multiLineSqString() {
      $t= $this->tokensOf('$s= \''."\n\n".'\';');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_STRING, "\n\n", array(1, 10)), $t[2]);
      $this->assertEquals(array(59, ';', array(3, 2)), $t[3]);
    }

    /**
     * Test parsing a single-quoted string with escapes
     *
     */
    #[@test]
    public function sqStringWithEscapes() {
      $t= $this->tokensOf('$s= \'\\\'Hello\\\', he said\';');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_STRING, '\'Hello\', he said', array(1, 10)), $t[2]);
      $this->assertEquals(array(59, ';', array(1, 30)), $t[3]);
    }

    /**
     * Test string at end
     *
     */
    #[@test]
    public function stringAsLastToken() {
      $t= $this->tokensOf('"Hello World"');
      $this->assertEquals(1, sizeof($t));
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_STRING, 'Hello World', array(1, 6)), $t[0]);
    }

    /**
     * Test parsing an unterminated string
     *
     */
    #[@test]
    public function unterminatedString() {
      $t= $this->tokensOf('$s= "The end');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array('lang.IllegalStateException', 'Unterminated string literal starting at line 1, offset 10'), $t[2]);
    }

    /**
     * Test a backslash inside a double quoted string ("\\\\")
     *
     */
    #[@test]
    public function dqBackslash() {
      $t= $this->tokensOf('$s= "\\\\";');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_STRING, '\\', array(1, 10)), $t[2]);
    }

    /**
     * Test a backslash inside a single quoted string ('\\')
     *
     */
    #[@test]
    public function sqBackslash() {
      $t= $this->tokensOf('$s= \'\\\\\';');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 's', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_STRING, '\\', array(1, 10)), $t[2]);
    }

    /**
     * Test decimal number
     *
     */
    #[@test]
    public function decimalNumber() {
      $t= $this->tokensOf('$i= 1.0;');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 'i', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_DECIMAL, '1.0', array(1, 10)), $t[2]);
      $this->assertEquals(array(59, ';', array(1, 13)), $t[3]);
    }

    /**
     * Test illegal decimal number
     *
     */
    #[@test]
    public function illegalDecimalNumber() {
      $t= $this->tokensOf('$i= 1.a;');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 'i', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array('lang.FormatException', 'Illegal decimal number <1.a> starting at line 1, offset 10'), $t[2]);
    }

    /**
     * Test hex number
     *
     */
    #[@test]
    public function hexNumber() {
      $t= $this->tokensOf('$i= 0xFF;');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 'i', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_HEX, '0xFF', array(1, 10)), $t[2]);
      $this->assertEquals(array(59, ';', array(1, 14)), $t[3]);
    }

    /**
     * Test illegal decimal number
     *
     */
    #[@test]
    public function illegalHexNumber() {
      $t= $this->tokensOf('$i= 0xZ;');
      $this->assertEquals(array(xp·compiler·syntax·php·Parser::T_VARIABLE, 'i', array(1, 6)), $t[0]);
      $this->assertEquals(array(61, '=', array(1, 8)), $t[1]);
      $this->assertEquals(array('lang.FormatException', 'Illegal hex number <0xZ> starting at line 1, offset 10'), $t[2]);
    }
  }
?>
