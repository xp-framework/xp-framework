<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Collection',
    'net.xp_framework.unittest.tests.coverage.Expression',
    'net.xp_framework.unittest.tests.coverage.Comment',
    'net.xp_framework.unittest.tests.coverage.Block'
  );

  /**
   * Parses PHP sourcecode into code fragments
   *
   * @ext      tokenizer
   * @purpose  Helper class
   */
  class PHPCodeFragmentizer extends Object {

    /**
     * Retrieve expressions from a given piece of code
     *
     * @model   static
     * @access  public
     * @param   string code
     * @return  net.xp_framework.unittest.tests.coverage.Fragment[] expressions
     */
    function fragmentsOf($code) {
      $tokens= token_get_all(trim($code));
      $expressions= &Collection::forClass('Fragment');
      $expression= '';
      $line= $last= 1;
      $level= 0;
      $collections= array(&$expressions);
      
      // Iterate over tokens, starting from the T_OPEN_TAG and ending 
      // before the traling T_WHITESPACE and T_CLOSE_TAG tokens.
      for ($i= 1, $s= sizeof($tokens)- 2; $i < $s; $i++) {
        switch ($tokens[$i][0]) {
          case ';':           // EOE
            $collections[$level]->add(new Expression(trim($expression).';', $last, $line));
            $expression= '';
            $last= -1;
            break;
          
          case '{':           // SOB
            $block= &$collections[$level]->add(new Block(trim($expression), array(), $line, -1));
            $collections[++$level]= &$block->expressions;
            $expression= '';
            $last= -1;
            break;
          
          case '}':           // EOB
            unset($collections[$level]);
            $block->end= $line;
            $level--;
            $expression= '';
            $last= -1;
            break;
          
          case T_COMMENT:
            $last= substr_count($tokens[$i][1], "\n");
            $collections[$level]->add(new Comment($tokens[$i][1], $line, $last+ $line));
            $expression= '';
            $line+= $last;
            $last= -1;
            break;
            
          case T_CONSTANT_ENCAPSED_STRING:
          case T_WHITESPACE:
            $line+= substr_count($tokens[$i][1], "\n");
            $last == -1 && $last= $line;
            $expression.= $tokens[$i][1];
            break;
            
          default:
            $last == -1 && $last= $line;
            $expression.= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
        }
      }
      
      // Check for leftover tokens
      if ($expression) {
        $collections[$level]->add(new Expression(trim($expression).';', $last, $line));
      }
      
      return $expressions->values();
    }
  }
?>
