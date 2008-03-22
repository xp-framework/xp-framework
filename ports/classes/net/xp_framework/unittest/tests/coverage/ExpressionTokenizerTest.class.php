<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'unittest.coverage.PHPCodeFragmentizer'
  );

  /**
   * Tests expression parsing
   *
   * @see      xp://token_get_all
   * @purpose  Unit Test
   */
  class ExpressionTokenizerTest extends TestCase {
    
    /**
     * Retrieve fragments for a given piece of code
     *
     * @see     xp://net.xp_framework.unittest.tests.coverage.PHPCodeFragmentizer
     * @param   string code
     * @return  unittest.coverage.Fragment[] expressions
     */
    protected function fragmentsOf($code) {
      return PHPCodeFragmentizer::fragmentsOf('<?php '.trim($code).' ?>');
    }
    
    /**
     * Assert method
     *
     * @param   unittest.coverage.Fragment[] expected
     * @param   string code
     * @throws  unittest.AssertionFailedError
     */
    protected function assertExpressions($expected, $code) {
      $fragments= $this->fragmentsOf($code);

      // Compare sizes
      $s= sizeof($fragments);
      if (sizeof($expected) != $s) {
        return $this->fail('Different size', $expected, $fragments);
      }
      
      // Check every element
      for ($i= 0; $i < $s; $i++) {
        if ($fragments[$i]->equals($expected[$i])) continue;

        return $this->fail('At offset #'.$i.'/'.$s, $fragments[$i], $expected[$i]);
      }
    }

    /**
     * Tests empty input will result in an empty array of expressions.
     *
     */
    #[@test]
    public function emptyInput() {
      $this->assertExpressions(array(), '');
    }
    
    /**
     * Tests a single expression
     *
     */
    #[@test]
    public function singleExpression() {
      $this->assertExpressions(array(
        new Expression('$a= 1;', 1, 1),
      ), '$a= 1;');
    }

    /**
     * Tests expression still gets returned even if we have a missing 
     * trailing semicolon (;)
     *
     */
    #[@test]
    public function missingTrailingSemicolon() {
      $this->assertExpressions(array(
        new Expression('$a= 1;', 1, 1),
      ), '$a= 1');
    }

    /**
     * Tests multiple expressions on one line
     *
     */
    #[@test]
    public function multipleExpressionsPerLine() {
      $this->assertExpressions(array(
        new Expression('$a= 1;', 1, 1),
        new Expression('$b= 1;', 1, 1),
      ), '$a= 1; $b= 1;');
    }

    /**
     * Tests an expression spanning multiple lines
     *
     */
    #[@test]
    public function multilineLineExpression() {
      $this->assertExpressions(array(
        new Expression('$a= (5 == strlen("Hello")
          ? "good"
          : "bad"
        );', 1, 4),
      ), '
        $a= (5 == strlen("Hello")
          ? "good"
          : "bad"
        );
      ');
    }

    /**
     * Tests two expressions, each on a line by itself
     *
     */
    #[@test]
    public function twoExpressions() {
      $this->assertExpressions(array(
        new Expression('statement_on_line_one();', 1, 1),
        new Expression('statement_on_line_two();', 2, 2),
      ), '
        statement_on_line_one(); 
        statement_on_line_two();
      ');
    }

    /**
     * Tests a string containing an expression doesn't get torn apart 
     * into expressions.
     *
     */
    #[@test]
    public function stringsContainingExpressions() {
      $this->assertExpressions(array(
        new Expression('echo "A statement: statement_on_line_one();";', 1, 1),
      ), 'echo "A statement: statement_on_line_one();";');
    }
    
    /**
     * Tests a single block
     *
     */
    #[@test]
    public function singleBlock() {
      $this->assertExpressions(array(
        new Block(NULL, array(new Expression('$a= 1;', 1, 1)), 1, 1),
      ), '{ $a= 1; }');
    }

    /**
     * Tests a string offset ($string{SCALAR})
     *
     */
    #[@test]
    public function scalarStringOffset() {
      $this->assertExpressions(array(
        new Expression('echo $string{0};', 1, 1),
      ), 'echo $string{0};');
    }

    /**
     * Tests a string offset ($string{EXPRESSION})
     *
     */
    #[@test]
    public function dynamicStringOffset() {
      $this->assertExpressions(array(
        new Expression('echo $string{strlen($var[0]{0})};', 1, 1),
      ), 'echo $string{strlen($var[0]{0})};');
    }

    /**
     * Tests an if block
     *
     */
    #[@test]
    public function ifBlock() {
      $this->assertExpressions(array(
        new Block('if (TRUE)', array(new Expression('exit;', 1, 1)), 1, 1),
      ), 'if (TRUE) { exit; }');
    }

    /**
     * Tests an if / else block
     *
     */
    #[@test]
    public function ifElseBlock() {
      $this->assertExpressions(array(
        new Block('if (TRUE)', array(new Expression('$i++;', 2, 2)), 1, 3),
        new Block('else', array(new Expression('$i--;', 4, 4)), 3, 5),
      ), '
        if (TRUE) { 
          $i++;
        } else {
          $i--;
        }
      ');
    }

    /**
     * Tests nested blocks
     *
     */
    #[@test]
    public function nestedBlocks() {
      $this->assertExpressions(array(
        new Block(NULL, array(new Block(NULL, array(new Expression('$a= 1;', 1, 1)), 1, 1)), 1, 1),
      ), '{ { $a= 1; } }');
    }

    /**
     * Tests C++ style comments
     *
     */
    #[@test]
    public function cPlusPlusComment() {
      $this->assertExpressions(
        array(new Comment('/* Hello */', 1, 1)),
        '/* Hello */'
      );
    }

    /**
     * Tests apidoc style comments
     *
     */
    #[@test]
    public function apiDocComment() {
      $comment= "/**\n * APIDOC\n * @return  Should return TRUE\n */";
      $this->assertExpressions(
        array(new Comment($comment, 1, 4)),
        $comment
      );
    }

    /**
     * Tests apidoc style comments
     *
     */
    #[@test]
    public function methodWithApiDocComment() {
      $comment= "/**\n * APIDOC\n * @return  Should return TRUE\n */";
      $method= "function phrickeling() {\n  return TRUE;\n}";
      $this->assertExpressions(array(
        new Comment($comment, 1, 4),
        new Block(
          'function phrickeling()', 
          array(new Expression('return TRUE;', 6, 6)),
          5,
          7
        )
      ), $comment."\n".$method);
    }

    /**
     * Tests a class declaration
     *
     */
    #[@test]
    public function hereDoc() {
      $heredoc= "<<<__\nHereDOC\n__;";
      $this->assertExpressions(array(
        new Expression('$a= '.$heredoc, 1, 4),
      ), '$a= '.$heredoc);
    }

    /**
     * Tests a class declaration
     *
     */
    #[@test]
    public function classDeclaration() {
      $declaration= '
        /**
         * Class api doc here
         *
         * @see   xp://unittest.TestCase
         */
        class StringBuffer extends Object {
          var $buffer;

          /**
           * Constructor
           *
           * @param   string initial
           */
          function __construct($initial) {
            $this->buffer= $initial;
          }
          
          /**
           * Find the string offset of the given search string within this
           * string buffer.
           *
           * @param   string search
           * @return  int offset or -1 if the search string was not found
           */
          function indexOf($search) {
            return FALSE === $p= strpos($this->buffer, $search) ? -1 : $p;
          }
        }
      ';

      $this->assertExpressions(array(
        new Comment('/**
         * Class api doc here
         *
         * @see   xp://unittest.TestCase
         */', 1, 5),
        new Block('class StringBuffer extends Object', array(
          new Expression('var $buffer;', 7, 7),
          new Comment('/**
           * Constructor
           *
           * @param   string initial
           */', 9, 14),
          new Block('function __construct($initial)', array(
            new Expression('$this->buffer= $initial;', 16, 16),
          ), 15, 17),
          new Comment('/**
           * Find the string offset of the given search string within this
           * string buffer.
           *
           * @param   string search
           * @return  int offset or -1 if the search string was not found
           */', 19, 26),
          new Block('function indexOf($search)', array(
            new Expression('return FALSE === $p= strpos($this->buffer, $search) ? -1 : $p;', 28, 28)
          ), 27, 29)
        ), 6, 30)
      ), $declaration);
    }
  }
?>
