<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.collections.Vector',
    'unittest.coverage.Expression',
    'unittest.coverage.Comment',
    'unittest.coverage.Block'
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
     * @param   string code
     * @return  unittest.coverage.Fragment[] expressions
     */
    public static function fragmentsOf($code) {
      static $delim= array(')', ';', ',', '=', '+', '-', '*', '/', '%');
      
      $tokens= token_get_all(trim($code));
      $expressions= create('new util.collections.Vector<unittest.coverage.Fragment>()');
      $expression= '';
      $line= 1 + substr_count($tokens[0][1], "\n");
      $last= 1;
      $level= 0;
      $collections= array($expressions);
      $blocks= array();
      
      // Iterate over tokens, starting from the T_OPEN_TAG and ending 
      // before the trailing T_WHITESPACE and T_CLOSE_TAG tokens.
      for ($i= 1, $s= sizeof($tokens)- 2; $i < $s; $i++) {
        switch ($tokens[$i][0]) {
          case ';':           // EOE
            $collections[$level]->add(new Expression(trim($expression).';', $last, $line));
            $expression= '';
            $last= -1;
            break;
          
          case T_VARIABLE:
            $last == -1 && $last= $line;
            
            // Look ahead for string offsets
            $var= '';
            $j= $i;
            $brackets= array(0, 0, 0);
            do {
              $var.= is_array($tokens[$j]) ? $tokens[$j][1] : $tokens[$j];
              switch ($tokens[$j][0]) {
                case '{': $brackets[0]++; break;
                case '[': $brackets[1]++; break;
                case '(': $brackets[2]++; break;
                case '}': $brackets[0]--; break;
                case ']': $brackets[1]--; break;
                case ')': $brackets[2]--; break;
              }
              // DEBUG Console::writeLine('L* ', $i, '->', $j, ': ', is_array($tokens[$j]) ? $tokens[$j][1] : $tokens[$j]);
              $j++;
              
              // Check for delimiter
              if (0 == array_sum($brackets) && in_array($tokens[$j][0], $delim)) break;
            } while ($j < $s);
            // DEBUG Console::writeLine('L* ', $i, '->', $j, ': === ', $var, ' ===');
            $expression.= $var;
            $line+= substr_count($var, "\n");
            $i= $j- 1;
            break;
          
          case '{':           // SOB
            $blocks[$level]= $collections[$level]->add(new Block(trim($expression), array(), $line, -1));
            $collections[++$level]= $blocks[$level- 1]->expressions;
            $expression= '';
            $last= -1;
            break;
          
          case '}':           // EOB
            unset($collections[$level]);
            $level--;
            $blocks[$level]->end= $line;
            $expression= '';
            $last= -1;
            break;
          
          case T_DOC_COMMENT:
            $last= substr_count($tokens[$i][1], "\n");
            $collections[$level]->add(new Comment($tokens[$i][1], $line, $last+ $line));
            $expression= '';
            $line+= $last;
            $last= -1;
            break;

          case T_COMMENT:
            if ('#' == $tokens[$i][1]{0} || '/' == $tokens[$i][1]{1}) {
              // Single-line comments
              $collections[$level]->add(new Comment($tokens[$i][1], $line, $line));
              $line++;
            } else {
              $last= substr_count($tokens[$i][1], "\n");
              $collections[$level]->add(new Comment($tokens[$i][1], $line, $last+ $line));
              $line+= $last;
            }
            $expression= '';
            $last= -1;
            break;

          case T_START_HEREDOC:
            $heredoc= '';
            do {
              $heredoc.= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
              $i++;
            } while ($i < $s && T_END_HEREDOC != $tokens[$i][0]);
            $heredoc.= $tokens[$i][1];
            $line+= substr_count($heredoc, "\n")+ 1;
            $last == -1 && $last= $line;
            $expression.= $heredoc;
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
      
      return $expressions->elements();
    }
  }
?>
