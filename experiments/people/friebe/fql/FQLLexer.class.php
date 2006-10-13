<?php
  uses('text.StringTokenizer');

  define('FQL_DELIMITERS', " =<>()/'\r\n");

  class FQLLexer extends Object {
    var
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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct($string, $fileName) {
      $this->tokenizer= &new StringTokenizer($string, FQL_DELIMITERS, TRUE);
      $this->fileName= $fileName;
      $this->line= 0;
    }
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function advance() {
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
        } else if (isset($this->keywords[$token])) {
          $this->token= $this->keywords[$token];
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
      // DEBUG Console::writeLine('FQLLexer::advance(), ', xp::stringOf($this->token), ': ', xp::stringOf($this->value)); 
      
      return $this->tokenizer->hasMoreTokens();
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function parseError(){
      return sprintf("Error at line %d in file %s", $this->line, $this->fileName);
    }
  }
?>
