<?php
  class Lexer extends Object {
    var 
      $tokens,
      $N = 0,
      $line,
      $pos = -1,
      $token,
      $value,
      $fileName,
      $hereDoc,
      $string = false,
      $openTagWithEcho = false,
      $tokenMap = array(
        TOKEN_T_PUBLIC      => 'public',
        TOKEN_T_PRIVATE     => 'private',
        TOKEN_T_PROTECTED   => 'protected',
        TOKEN_T_ABSTRACT    => 'abstract',
        TOKEN_T_FINAL       => 'final',
        TOKEN_T_IMPLEMENTS  => 'implements',
        TOKEN_T_INTERFACE   => 'interface',
        TOKEN_T_TRY         => 'try',
        TOKEN_T_THROW       => 'throw',
        TOKEN_T_THROWS      => 'throws',
        TOKEN_T_CATCH       => 'catch',
        TOKEN_T_IMPORT      => 'import',
        TOKEN_T_PACKAGE     => 'package',
        TOKEN_T_ENUM        => 'enum',  
        TOKEN_T_OPERATOR    => 'operator',   
        TOKEN_T_VOID        => 'void',   
        TOKEN_T_CONSTRUCT   => '__construct',
        TOKEN_T_DESTRUCT    => '__destruct',
        TOKEN_T_FINALLY     => 'finally',
        TOKEN_T_INSTANCEOF  => 'instanceof',
      );

    /**
     * Constructor
     *
     * @access  public
     * @param   string[] tokens
     * @param   string fileName
     */
    function __construct($tokens, $fileName){
      $this->tokens= $this->tokenGetAll($tokens);
      $this->N= count($this->tokens);
      $this->pos= -1;
      $this->line= 1;
      $this->fileName= $fileName;
    }

    function setLine($line){
      $this->line= $line;
    }

    function advance(){
      $this->pos++;
      while ($this->pos < $this->N){
        if ($this->hereDoc === TRUE && $this->tokens[$this->pos][0] != T_END_HEREDOC){
          return $this->update();
        }
        if ($this->string === TRUE && $this->tokens[$this->pos][0] != 34){
          return $this->update();   
        }
        if ($this->openTagWithEcho === TRUE && $this->tokens[$this->pos][0] == T_CLOSE_TAG){
          return $this->update();
        }
        switch($this->tokens[$this->pos][0]){
          case 34:
            $this->string?$this->string = false:$this->string = true;
            return $this->update();
          case TOKEN_T_START_HEREDOC:
            $this->hereDoc = true;
            return $this->update();
          case TOKEN_T_END_HEREDOC:
            $this->hereDoc = false;
            return $this->update();
          case TOKEN_T_OPEN_TAG_WITH_ECHO:
            $this->openTagWithEcho = true;
            return $this->update();
          case TOKEN_T_OPEN_TAG:
          case TOKEN_T_CLOSE_TAG:
          case TOKEN_T_WHITESPACE:
          case TOKEN_T_ENCAPSED_AND_WHITESPACE:
          case TOKEN_T_COMMENT:
            $this->setLine($this->line + substr_count($this->tokens[$this->pos][1], "\n"));
            $this->pos++;
            continue;
          default: 
            return $this->update();
        }
      }

      return FALSE;
    }

    function update(){
      $this->setLine($this->line + substr_count($this->tokens[$this->pos][1], "\n"));
      $this->token = $this->tokens[$this->pos][0];
      $this->value = $this->tokens[$this->pos][1];
      return true;
    }

    function parseError(){
      return sprintf("Error at line %d in file %s", $this->line, $this->fileName);
    }

    function tokenGetAll($source){
      $tokens= token_get_all('<?php '.$source.' ?>');
      $return= array();
      $offset= 0;
      for ($id= 0, $s= sizeof($tokens); $id < $s; $id++) {
        $token= $tokens[$id];

        // Map
        if(!is_array($token)) {
          $return[$offset] = array(ord($token), $token);
          $token = $return[$offset];
        } else {
          $return[$offset]= array(
            array_search(token_name($token[0]), $GLOBALS['_TOKEN_DEBUG']),
            $token[1]
          );
          $token = $return[$offset];
        }


        if($token[0] == TOKEN_T_START_HEREDOC && !$this->hereDoc){
          $this->hereDoc = true;
        } elseif ($token[0] == TOKEN_T_END_HEREDOC && $this->hereDoc){
          $this->hereDoc = false;
        } elseif ($token[0] == 34){
          $this->string?$this->string = false:$this->string = true;
        } elseif (
          $token[0] == TOKEN_T_STRING && 
          !$this->string && 
          !$this->hereDoc &&
          isset($tokens[$id-1]) && 
          $tokens[$id-1][0] != TOKEN_T_OBJECT_OPERATOR && 
          $tokens[$id-1][0] != TOKEN_T_DOUBLE_COLON
        ) {
          if ('~' == $tokens[$i= $id+ 1][0]) {
            $classname= '';
            while ('~' == $tokens[$i][0]) {
              $classname.= $tokens[$i- 1][1].'~';
              $i+= 2;
            }
            $return[$offset]= array(TOKEN_T_CLASSNAME, $classname.$tokens[$i- 1][1]);
            $id= $i- 1;  // Skip tokens
          } else if ($key = array_search(strtolower($token[1]), $this->tokenMap)) {
            $return[$offset]= array($key, $token[1]);
          }
        }

        // *** echo $offset, '] ', $return[$offset][0], ' ', $GLOBALS['_TOKEN_DEBUG'][$return[$offset][0]], ' (', addcslashes($return[$offset][1], "\0..\17"), ')', "\n";

        $offset++;
      }
      $this->hereDoc= FALSE;
      $this->string= FALSE;
      return $return;
    }

    function tokenName($token){
      return $token < 374 ? $GLOBALS['_TOKEN_DEBUG'][$token] : $this->tokenMap[$token];
    }
  }
?> 
