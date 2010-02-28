<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.regex.Matcher');

  /**
   * Scanner
   *
   * @see      php://sscanf
   * @see      http://www.kernel.org/doc/man-pages/online/pages/man3/scanf.3.html 
   */
  class Scanner extends Object implements Matcher {
    protected $pattern= array();
    
    /**
     * Creates a new character class instance
     *
     * @param   string pattern
     */
    public function __construct($pattern) {
      for ($i= 0, $s= strlen($pattern); $i < $s; $i++) {
        if ('%' === $pattern{$i}) {
          $i++;
          switch ($pattern{$i}) {
            case '%': $this->pattern[]= '1%'; break; 
            case 'd': $this->pattern[]= '1+-0123456789'; break;
            case 'x': $this->pattern[]= '1x0123456789abcdefABCDEF'; break;
            case 'f': $this->pattern[]= '1+-0123456789.'; break;
            case 's': $this->pattern[]= "0\1\2\3\4\5\6\7\10\11\12\13\14\15\16\17\20\21\22\23\24\25\26\27\30\31\32\33\34\35\36\37\40"; break;
            case '[': {   // [^a-z]: everything except a-z, [a-z]: only a-z, []01]: only "[", "0" and "1"
              if ('^' === $pattern{$i+ 1}) {
                $match= '0';
                $i++;
              } else {
                $match= '1';
              }
              $p= strpos($pattern, ']', $i + (']' === $pattern{$i+ 1} ? 2 : 0));
              $seq= substr($pattern, $i+ 1, $p- $i- 1);
              for ($j= 0, $t= strlen($seq); $j < $t; $j++) {
                if ($j < $t- 1 && '-' === $seq{$j+ 1}) {
                  $match.= implode('', range($seq{$j}, $seq{$j+ 2}));
                  $j+= 2;
                } else {
                  $match.= $seq{$j};
                }
              }
              $this->pattern[]= $match;
              $i+= $t+ 1;
              break;
            }
          }
        } else {
          $o= sizeof($pattern);
          if (!isset($this->pattern[$o])) {
            $this->pattern[$o]= '1';
          }
          $this->pattern[$o].= $pattern{$i};
        }
      }
    }
    
    /**
     * Checks whether a given string matches this character class
     *
     * @param   string input
     * @return  bool
     */
    public function matches($input) {
      $o= 0;
      $matches= 0;
      foreach ($this->pattern as $match) {
        $l= $match[0] ? strspn($input, substr($match, 1), $o) : strcspn($input, substr($match, 1), $o);
        if (0 === $l) break;
        $matches++;
        $o+= $l;
      }
      return $matches > 0;
    }

    /**
     * Returns match results
     *
     * @param   string input
     * @return  text.regex.MatchResult
     */
    public function match($input) {
      $matches= array();
      $o= 0;
      foreach ($this->pattern as $match) {
        $l= $match[0] ? strspn($input, substr($match, 1), $o) : strcspn($input, substr($match, 1), $o);
        if (0 === $l) break;
        $matches[]= substr($input, $o, $l);
        $o+= $l;
      }
      return new MatchResult(sizeof($matches), $matches ? array($matches) : array());
    }
  }
?>
