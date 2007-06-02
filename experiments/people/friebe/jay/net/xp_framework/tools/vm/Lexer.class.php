<?php
  define('LEXER_PACKAGE_SEPARATOR', '.');
  
  uses('text.parser.generic.AbstractLexer');

  class Lexer extends AbstractLexer {
    var 
      $tokens,
      $position,
      $N = 0,
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
      $this->fileName= $fileName;
    }

    function advance() {
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

        switch ($this->tokens[$this->pos][0]){
          case TOKEN_T_WHITESPACE:
          case TOKEN_T_COMMENT:
          case TOKEN_T_DOC_COMMENT:
            $this->pos++;
            continue;
          default: 
            return $this->update();
        }
      }

      return FALSE;
    }

    function update(){
      $this->token= $this->tokens[$this->pos][0];
      $this->value= $this->tokens[$this->pos][1];
      $this->position= array($this->tokens[$this->pos][2], $this->tokens[$this->pos][3]);
      return TRUE;
    }

    function tokenGetAll($source) {
      $tokens= token_get_all('<?php '.$source.' ?>');
      $return= array();
      $next= 0;
      $line= 1;
      $offset= 0;
      for ($id= 1, $s= sizeof($tokens); $id < $s- 1; $id++) {
        $token= $tokens[$id];

        // Map
        if (!is_array($token)) {
          $return[$next] = array(ord($token), $token);
          $token= $return[$next];
        } else {
          if (in_array($token[0], array(T_INT_CAST, T_DOUBLE_CAST, T_STRING_CAST, T_ARRAY_CAST, T_OBJECT_CAST, T_BOOL_CAST, T_UNSET_CAST))) {
            $return[$next]= array(TOKEN_T_CAST, trim($token[1], '()'));
          } else {
            $c= 'TOKEN_'.token_name($token[0]);
            $return[$next]= array(
              defined($c) ? constant($c) : TOKEN_T_STRING, // Map PHP tokens to ours
              $token[1]
            );
            $token= $return[$next];
          }
        }

        if ($token[0] == 126 && $tokens[$id+ 1][0] == '=') {
          $return[$next]= array(TOKEN_T_CONCAT_EQUAL, '=~');
          $id++;
        } else if ($token[0] == 34) {    // "

          // Look ahead for encapsed
          $value= '';
          while (($tokens[++$id][0] != '"') && $id < $s) {
            $value.= is_array($tokens[$id]) ? $tokens[$id][1] : $tokens[$id];
          }

          $return[$next]= array(TOKEN_T_CONSTANT_ENCAPSED_STRING, '"'.$value.'"');
        } else if (
          $token[0] == TOKEN_T_STRING && 
          isset($tokens[$id- 1]) && 
          $tokens[$id-1][0] != TOKEN_T_OBJECT_OPERATOR && 
          $tokens[$id-1][0] != TOKEN_T_DOUBLE_COLON
        ) {
          if (LEXER_PACKAGE_SEPARATOR == $tokens[$i= $id+ 1][0]) {
            $classname= '';
            while (LEXER_PACKAGE_SEPARATOR == $tokens[$i][0]) {
              $classname.= $tokens[$i- 1][1].LEXER_PACKAGE_SEPARATOR;
              $i+= 2;
            }
            if (in_array($tokens[$i- 1][0], array(T_STRING, T_FUNCTION, T_LIST, T_ARRAY, T_CLASS))) {
              // For PHP5, add T_INTERFACE
              $return[$next]= array(TOKEN_T_CLASSNAME, $classname.$tokens[$i- 1][1]);
              $token = $return[$next];
              $id= $i- 1;  // Skip tokens
            }
          } else if ($key = array_search(strtolower($token[1]), $this->tokenMap)) {
            $return[$next]= array($key, $token[1]);
          }
        } else if ($token[0] == TOKEN_T_IS_SMALLER_OR_EQUAL && $tokens[$id+ 1][0] == '>') {
          $return[$next]= array(TOKEN_T_COMPARE, '<=>');
          $id++;
        }

        $return[$next][2]= $line;
        $return[$next][3]= $offset;

        if (0 != ($breaks= substr_count($return[$next][1], "\n"))) {
          $line+= $breaks;
          $offset= strlen($return[$next][1]) - strrpos($return[$next][1], "\n")- 1;
          
        } else {
          $offset+= strlen($return[$next][1]);
        }

        // Console::writeLine('> ', addcslashes(implode('|', $return[$next]), "\0..\17"));
        $next++;
      }
      return $return;
    }
  }
?>
