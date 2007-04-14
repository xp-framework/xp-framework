<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  uses('text.StringTokenizer', 'text.parser.generic.AbstractLexer');

  /**
   * Lexer for compact XML
   *
   * @see      xp://text.parser.generic.AbstractLexer
   * @purpose  Lexer
   */
  class CompactXmlLexer extends AbstractLexer {
    protected static
      $keywords  = array(
        'import'    => TOKEN_T_IMPORT,
      );

    const 
      DELIMITERS = " ;,=<>(){}#\"\r\n";
      
    /**
     * Constructor
     *
     * @param   string input
     * @param   string source
     */
    function __construct($input, $source) {
      $this->tokenizer= new StringTokenizer($input, self::DELIMITERS, TRUE);
      $this->fileName= $source;
      $this->line= 0;
    }
  
    /**
     * Advance this 
     *
     * @return  bool
     */
    public function advance() {
      do {
        $token= $this->tokenizer->nextToken(self::DELIMITERS);
        
        // Check for whitespace
        if (FALSE !== strpos(" \n\r\t", $token)) {
          $this->line+= substr_count($token, "\n");
          continue;
        }
        
        if ('"' == $token{0}) {
          $this->token= TOKEN_T_STRING;
          $this->value= $this->tokenizer->nextToken('"');
          $this->tokenizer->nextToken('"');
        } else if ('<' == $token{0}) {
          $this->token= TOKEN_T_TEXT;
          $this->value= $this->tokenizer->nextToken('>');
          $this->tokenizer->nextToken('>');
        } else if ('#' == $token{0}) {
          $this->token= TOKEN_T_COMMENT;
          $this->value= ltrim($this->tokenizer->nextToken("\r\n"), ' ');
        } else if (isset(self::$keywords[$token])) {
          $this->token= self::$keywords[$token];
          $this->value= $token;
        } else if (FALSE !== strpos(self::DELIMITERS, $token) && 1 == strlen($token)) {
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
