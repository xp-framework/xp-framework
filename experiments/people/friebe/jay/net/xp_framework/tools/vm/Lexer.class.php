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
      $string = false,
      $tokenMap = array(
        TOKEN_T_PUBLIC      => 'public',
        TOKEN_T_PRIVATE     => 'private',
        TOKEN_T_PROTECTED   => 'protected',
        TOKEN_T_PROPERTY    => 'property',
        TOKEN_T_ABSTRACT    => 'abstract',
        TOKEN_T_FINAL       => 'final',
        TOKEN_T_NATIVE      => 'native',
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

        // Casts (T_STRING) or (T_CLASSNAME)
        if (
          '(' == $this->tokens[$this->pos][1] && 
          (($this->tokens[$this->pos+ 1][0] == TOKEN_T_STRING) || ($this->tokens[$this->pos+ 1][0] == TOKEN_T_CLASSNAME))  && 
          ')' == $this->tokens[$this->pos+ 2][1]
        ) {
          // echo 'CAST: '; var_dump($this->tokens[$this->pos], $this->tokens[$this->pos+ 1], $this->tokens[$this->pos+ 2]);
          // echo "\n";
          $this->tokens[$this->pos]= array(TOKEN_T_CAST, $this->tokens[$this->pos+ 1][1]);
          $this->update();
          $this->pos+= 2;
          return TRUE;
        }
        

        if ($this->string === TRUE && $this->tokens[$this->pos][0] != 34){
          return $this->update();   
        }
        switch($this->tokens[$this->pos][0]){
          case 34:
            $this->string?$this->string = false:$this->string = true;
            return $this->update();
          case TOKEN_T_WHITESPACE:
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
      return TRUE;
    }

    function tokenGetAll($source) {
      $tokens= token_get_all('<?php '.$source.' ?>');
      $return= array();
      $offset= 0;
      for ($id= 1, $s= sizeof($tokens); $id < $s- 1; $id++) {
        $token= $tokens[$id];

        // Map
        if(!is_array($token)) {
          $return[$offset] = array(ord($token), $token);
          $token = $return[$offset];
        } else {
          if (in_array($token[0], array(T_INT_CAST, T_DOUBLE_CAST, T_STRING_CAST, T_ARRAY_CAST, T_OBJECT_CAST, T_BOOL_CAST, T_UNSET_CAST))) {
            $return[$offset]= array(TOKEN_T_CAST, trim($token[1], '()'));
          } else {
            $c= 'TOKEN_'.token_name($token[0]);
            $return[$offset]= array(
              defined($c) ? constant($c) : TOKEN_T_STRING, // Map PHP tokens to ours
              $token[1]
            );
            $token= $return[$offset];
          }
        }

        if ($token[0] == 34){
          $this->string= !$this->string;
        } elseif (
          $token[0] == TOKEN_T_STRING && 
          !$this->string && 
          isset($tokens[$id- 1]) && 
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
            $token = $return[$offset];
            $id= $i- 1;  // Skip tokens
          } else if ($key = array_search(strtolower($token[1]), $this->tokenMap)) {
            $return[$offset]= array($key, $token[1]);
          }
        }

        // Console::writeLine('> ', implode(', ', $return[$offset]));
        $offset++;
      }

      $this->string= FALSE;
      return $return;
    }
  }
?>
