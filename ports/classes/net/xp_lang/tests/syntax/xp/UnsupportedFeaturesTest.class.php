<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase showing what is not supported in XP language in comparison
   * to PHP.
   *
   */
  class UnsupportedFeaturesTest extends ParserTestCase {
  
    /**
     * Test variable variables are not supported
     *
     * @see   php://language.variables.variable
     */
    #[@test, @expect('lang.FormatException')]
    public function variableVariables() {
      $this->parse('$$i= 0;');
    }

    /**
     * Test dynamic variables are not supported
     *
     * @see   php://language.variables.variable
     */
    #[@test, @expect('lang.FormatException')]
    public function dynamicVariables() {
      $this->parse('${$i}= 0;');
    }

    /**
     * Test goto is not supported
     *
     * @see   php://goto
     */
    #[@test, @expect('lang.FormatException')]
    public function gotoStatement() {
      $this->parse('goto error;');
    }

    /**
     * Test declare is not supported
     *
     * @see   php://declare
     */
    #[@test, @expect('lang.FormatException')]
    public function declareStatement() {
      $this->parse('declare(ticks=1) { }');
    }

    /**
     * Test functions are not supported
     *
     * @see   php://language.functions
     */
    #[@test, @expect('lang.FormatException')]
    public function functions() {
      $this->parse('function a() { }');
    }

    /**
     * Test new statement without braces
     *
     * @see   php://new
     */
    #[@test, @expect('lang.FormatException')]
    public function newWithoutBraces() {
      $this->parse('new A;');
    }

    /**
     * Test references are not supported
     *
     * @see   php://language.references
     */
    #[@test, @expect('lang.FormatException')]
    public function references() {
      $this->parse('$a= &$b;');
    }

    /**
     * Test "elseif" keyword is not supported
     *
     * @see   php://elseif
     */
    #[@test, @expect('lang.FormatException')]
    public function elseifKeyword() {
      $this->parse('if ($a) { $b++; } elseif ($c) { $d++; }');
    }

    /**
     * Test "include" keyword is not supported without braces
     *
     * @see   php://include
     */
    #[@test, @expect('lang.FormatException')]
    public function includeKeywordWithoutBraces() {
      $this->parse('include "functions.inc";');
    }

    /**
     * Test "require" keyword is not supported without braces
     *
     * @see   php://require
     */
    #[@test, @expect('lang.FormatException')]
    public function requireKeywordWithoutBraces() {
      $this->parse('require "functions.inc";');
    }

    /**
     * Test "echo" keyword is not supported without braces
     *
     * @see   php://echo
     */
    #[@test, @expect('lang.FormatException')]
    public function echoKeywordWithoutBraces() {
      $this->parse('echo "Hello";');
    }

    /**
     * Test alternative syntax for control structures are not supported
     *
     * @see   php://control-structures.alternative-syntax
     */
    #[@test, @expect('lang.FormatException')]
    public function alternativeIf() {
      $this->parse('if ($a): $b++; endif;');
    }

    /**
     * Test alternative syntax for control structures are not supported
     *
     * @see   php://control-structures.alternative-syntax
     */
    #[@test, @expect('lang.FormatException')]
    public function alternativeWhile() {
      $this->parse('while ($a > 0): $a--; endwhile;');
    }

    /**
     * Test alternative syntax for control structures are not supported
     *
     * @see   php://control-structures.alternative-syntax
     */
    #[@test, @expect('lang.FormatException')]
    public function alternativeFor() {
      $this->parse('for ($i= 0; $i < 4; $i++): $b--; endfor;');
    }

    /**
     * Test alternative syntax for control structures are not supported
     *
     * @see   php://control-structures.alternative-syntax
     */
    #[@test, @expect('lang.FormatException')]
    public function alternativeForeach() {
      $this->parse('foreach ($a in $list): $b--; endforeach;');
    }

    /**
     * Test silence operator is not supported
     *
     * @see   php://language.operators.errorcontrol
     */
    #[@test, @expect('lang.FormatException')]
    public function silenceOperator() {
      $this->parse('$a= @$b;');
    }

    /**
     * Test execution operator is not supported
     *
     * @see   php://language.operators.execution
     */
    #[@test, @expect('lang.FormatException')]
    public function executionOperator() {
      $this->parse('$a= `ls -al`;');
    }

    /**
     * Test inline HTML is not supported
     *
     * @see   php://language.basic-syntax.phpmode
     */
    #[@test, @expect('lang.FormatException')]
    public function inlineHTML() {
      $this->parse('?>HTML<?php');
    }

    /**
     * Test hash (#) comment is not supported
     *
     * @see   php://language.basic-syntax.comments
     */
    #[@test, @expect('lang.FormatException')]
    public function hashComment() {
      $this->parse('# $a= 1;');
    }

    /**
     * Test heredoc is not supported
     *
     * @see   php://heredoc
     */
    #[@test, @expect('lang.FormatException')]
    public function hereDoc() {
      $this->parse("\$s= <<<EOS\nHello\nEOS;");
    }

    /**
     * Test nowdoc is not supported
     *
     * @see   php://nowdoc
     */
    #[@test, @expect('lang.FormatException')]
    public function nowDoc() {
      $this->parse("\$s= <<<'EOS'\nHello\nEOS;");
    }
  }
?>
