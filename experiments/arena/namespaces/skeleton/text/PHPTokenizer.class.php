<?php
/* This class is part of the XP framework
 *
 * $Id: PHPTokenizer.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace text;
 
  define('T_NONE',            0x0000);    // None of the known tokens, CDATA only
  define('T_ANY',             0xFFFF);    // Any token
 
  /**
   * PHP Tokenizer
   * Class wrapper around PHPs tokenizer extension
   * 
   * @ext   tokenize
   * @see   php://tokenizer
   * @see   xp://text.apidoc.parser.GenericParser
   */ 
  class PHPTokenizer extends lang::Object {
    public 
      $tokens= array(),
      $rules=  array();
      
    public
      $_offset,
      $_size;
      
    /**
     * Set tokens
     *
     * @param   string str a string containing all of the tokens
     */
    public function setTokenString($str) {
      $this->tokens= token_get_all($str);
      $this->_offset= 0;
      $this->_size= sizeof($this->tokens);
    }
    
    /**
     * Set tokens
     *
     * @param   array tokens
     */
    public function setTokens($tokens) {
      $this->tokens= $tokens;
      $this->_offset= 0;
      $this->_size= sizeof($tokens);
    }
    
    /**
     * Gets a token's name
     *
     * @param   int tok Token constant, e.g. T_WHITESPACE
     * @return  string name
     */
    public function getTokenName($tok) {
      switch ($tok) {
        case T_NONE:  return 'T_NONE';
        case T_ANY:   return 'T_ANY';
      }
      return token_name($tok);
    }
    
    /**
     * Adds a rule which can be applied with applyRules()
     *
     * When a list of matches has succeeded in applying, the defined callback
     * function is called with according parameters. Parameters may be any of
     * PHPs known datatypes in their notation or the special $X syntax:
     * 
     * Consider the following parameters:
     * <code>
     *   $name=     'function_with_comment';
     *   $match=    array('T_COMMENT', 'T_WHITESPACE', 'T_FUNCTION', 'T_WHITESPACE', 'T_STRING');
     *   $callback= 'setFunctionComment';
     *   $params=   array('$5', '$1', FALSE);
     * </code>
     *
     * In this example, setFunctionComment() is called with the functions name (the 
     * 5th token, counting from 1) as its first parameter, the comment as its second
     * and boolean FALSE as its third parameter
     *
     * @see     xp://text.PHPTokenizer#applyRules
     * @param   string name rule name
     * @param   array match list of tokens to match
     * @param   mixed callback either a string or array(&$obj, 'function') syntax
     * @param   array params parameters for callback
     */
    public function addRule($name, $match, $callback, $params) {
      $this->rules[$name]= array(
        'expect'        => 0,
        'match'         => $match, 
        'callback'      => $callback, 
        'params'        => $params
      );
    }

    /**
     * Return a token by position
     *
     * @param   int i offset
     * @return  array tokendata (type, cdata)
     */
    public function getToken($i) {
      return (is_array($this->tokens[$i])
        ? $this->tokens[$i]
        : array(T_NONE, $this->tokens[$i])
      );
    }
    
    /**
     * Get first token
     *
     * @return  array first token
     */
    public function getFirstToken() {
      return $this->getToken($this->_offset= 0);
    }
    
    /**
     * Get next token
     *
     * @return  array next token from current offset or FALSE when no more tokens exist
     */
    public function getNextToken() {
      if (++$this->_offset >= $this->_size) return FALSE;
      return $this->getToken($this->_offset);
    }
    
    /**
     * Apply rules on all tokens
     *
     * @param   util.log.LogCategory CAT default NULL a log category to print debug to
     * @return  bool success
     */
    public function applyRules($CAT= ) {
      $data= array();
      
      // Loop throught tokens
      $tok= $this->getFirstToken();
      $i= 0;
      do {
        list($token, $cdata)= $tok;

        // Go through all rules and see if one matches
        reset($this->rules);
        $name= key($this->rules);
        do {
          $CAT && $CAT->debugf('[%04x:%-18s] >>> %s "%s"', $i, $name, $this->getTokenName($token), $cdata);
          
          $rule= $this->rules[$name];
          $expect= $rule['match'][$rule['expect']];
          $s= sizeof($rule['match']);
          $f= FALSE;

          $CAT && $CAT->infof('[%04x:%-18s] Executing %s, expecting %s', $i, $name, $name, $expect);
          if (
            ('{' === $expect{0}) 
          ) {
            
            // Matches list of tokens
            $tokens= explode(',', substr($expect, 1, -1));
            if (in_array(token_name($token), $tokens)) {
              $CAT && $CAT->debugf('[%04x:%-18s] %s in %s', $i, $name, token_name($token), $expect);
              $f= TRUE;
            }
            
          } else if (
            ("'" === $expect{0}) &&
            (T_NONE === $token) && 
            (preg_match('/'.substr($expect, 1, -1).'/', $cdata))
          ) {

            // Matches text
            $CAT && $CAT->debugf('[%04x:%-18s] %s =~ %s', $i, $name, $expect, $cdata);
            $f= TRUE;
          } else if (
            ('(' === $expect{0} && $p= strpos($expect, ')')) &&
            (token_name($token) === substr($expect, 1, $p- 1)) &&
            (preg_match('/^'.substr($expect, $p+ 2, -1).'$/', $cdata))
          ) {
          
            // Matches token and cdata
            $CAT && $CAT->debugf('[%04x:%-18s] %s =~ %s, token %s', $i, $name, $expect, $cdata, token_name($token));
            $f= TRUE;
          } else if (
            ('!' === $expect{0}) &&
            (
              (T_NONE === $token) ||
              (token_name($token) !== substr($expect, 1))
            )
          ) {

            // Does not match a token
            $CAT && $CAT->debugf('[%04x:%-18s] %s !== %s', $i, $name, token_name($token), $expect);
            $f= TRUE;
          } else if (
            (token_name($token) === $expect)
          ) {   

            // Matches a token
            $CAT && $CAT->debugf('[%04x:%-18s] %s === %s', $i, $name, token_name($token), $expect);
            $f= TRUE;
          }

          if (!$f) {
          
            // No action taken before
            if ($rule['expect'] == 0) {
              if (FALSE === next($this->rules)) break;
              $name= key($this->rules);
              continue;
            }

            // One or more tokens found before (but this one doesn't match the expectation)
            $CAT && $CAT->warnf(
              '[%04x:%-18s] --- have %s "%s" for rule %s, but was expecting %s', 
              $i, 
              $name,
              token_name($token),  
              chop($cdata),
              $name,
              $expect
            );
            if ($rule['expect'] < $s) $rule['expect']= 0;

            continue;
          }

          // Found n'th token in list...
          $CAT && $CAT->infof('[%04x:%-18s] +++ found %s for rule %s [%d/%d]', $i, $name, token_name($token), $name, $rule['expect'], $s);
          $rule['expect']++;
          $data[$name][$rule['expect']]= $cdata;

          // ...but not last one
          if ($rule['expect'] < $s) {
            if (FALSE === next($this->rules)) break;
            $name= key($this->rules);
            continue;
          }

          // Completed
          $CAT && $CAT->infof('[%04x:%-18s] *** List completed::%s', $i, $name, var_export($data[$name], 1));
          
          // Call user function
          $params= array();
          for ($p= 0; $p < sizeof($rule['params']); $p++) {
            if ('$' == $rule['params'][$p]{0}) {
              $params[]= $data[$name][substr($rule['params'][$p], 1)];
            } else {
              eval('$params[]= '.$rule['params'][$p].';');
            }
          }
          
          // Interrupt on error
          if (FALSE === call_user_func_array($rule['callback'], $params)) {
            return FALSE;
          }

          // Reinit for next "round"
          $rule['expect']= 0;
          $data[$name]= array();
          if (FALSE === next($this->rules)) break;
          $name= key($this->rules);
        } while (1);
      } while (++$i && $tok= $this->getNextToken());
      
      return TRUE;
    }
  }
?>
