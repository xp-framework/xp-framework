<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.StringTokenizer', 'text.parser.generic.AbstractLexer');

  define('FQL_DELIMITERS', " =<>()/'\r\n");

  /**
   * FQL Lexer
   *
   * @see      xp://text.parser.generic.AbstractParser
   * @purpose  Abstract base class
   */
  class FQLLexer extends AbstractLexer {
    protected static
      $keywords= array(
        'select'  => TOKEN_T_SELECT,
        'from'    => TOKEN_T_FROM,
        'where'   => TOKEN_T_WHERE,
        'and'     => TOKEN_T_AND,
        'or'      => TOKEN_T_OR,
        'like'    => TOKEN_T_LIKE,
        'ilike'   => TOKEN_T_ILIKE,
        'matches' => TOKEN_T_MATCHES
     );
      
    /**
     * Constructor
     *
     * @param   string string
     * @param   string fileName
     */
    function __construct($string, $fileName) {
      $this->tokenizer= new StringTokenizer($string, FQL_DELIMITERS, TRUE);
      $this->fileName= $fileName;
      $this->line= 0;
    }
  
    /**
     * Advance to next token. Return TRUE and set token, value and
     * position members to indicate we have more tokens, or FALSE
     * to indicate we've arrived at the end of the tokens.
     *
     * @return  bool
     */
    public function advance() {
      do {
        $token= $this->tokenizer->nextToken(FQL_DELIMITERS);
        
        // Check for whitespace
        if (FALSE !== strpos(" \n\r\t", $token)) {
          $this->line+= substr_count($token, "\n");
          continue;
        }
        
        if ("'" == $token{0}) {
          $this->token= TOKEN_T_STRING;
          $this->value= $this->tokenizer->nextToken("'");
          $this->tokenizer->nextToken("'");
        } else if ('/' == $token{0}) {
          $this->token= TOKEN_T_REGEX;
          $this->value= $this->tokenizer->nextToken('/');
          $this->tokenizer->nextToken('/');
        } else if (isset(self::$keywords[$token])) {
          $this->token= self::$keywords[$token];
          $this->value= $token;
        } else if (FALSE !== strpos(FQL_DELIMITERS, $token) && 1 == strlen($token)) {
          $this->token= ord($token);
          $this->value= $token;
        } else if (preg_match('/^[0-9]+$/', $token)) {
          $this->token= TOKEN_T_NUMBER;
          $this->value= $token;
        } else {
          $this->token= TOKEN_T_WORD;
          $this->value= $token;
        }
        break;
      } while (1);
      
      return $this->tokenizer->hasMoreTokens();
    }
  }
?>
