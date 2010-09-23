<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  $package= 'xp.compiler.syntax.xp';

  uses(
    'text.Tokenizer',
    'text.StringTokenizer', 
    'text.StreamTokenizer', 
    'io.streams.InputStream',
    'xp.compiler.syntax.xp.Parser', 
    'xp.compiler.emit.Strings', 
    'text.parser.generic.AbstractLexer'
  );
  
  define('MODIFIER_PACKAGE',  2048);
  define('MODIFIER_INLINE',   4096);
  define('MODIFIER_NATIVE',   8192);

  /**
   * Lexer for XP language
   *
   * @see      xp://text.parser.generic.AbstractLexer
   * @purpose  Lexer
   */
  class xp搾ompiler新yntax暖p微exer extends AbstractLexer {
    protected static
      $keywords  = array(
        'public'        => xp搾ompiler新yntax暖p感arser::T_PUBLIC,
        'private'       => xp搾ompiler新yntax暖p感arser::T_PRIVATE,
        'protected'     => xp搾ompiler新yntax暖p感arser::T_PROTECTED,
        'static'        => xp搾ompiler新yntax暖p感arser::T_STATIC,
        'final'         => xp搾ompiler新yntax暖p感arser::T_FINAL,
        'abstract'      => xp搾ompiler新yntax暖p感arser::T_ABSTRACT,
        'inline'        => xp搾ompiler新yntax暖p感arser::T_INLINE,
        'native'        => xp搾ompiler新yntax暖p感arser::T_NATIVE,
        'const'         => xp搾ompiler新yntax暖p感arser::T_CONST,
        
        'package'       => xp搾ompiler新yntax暖p感arser::T_PACKAGE,
        'import'        => xp搾ompiler新yntax暖p感arser::T_IMPORT,
        'class'         => xp搾ompiler新yntax暖p感arser::T_CLASS,
        'interface'     => xp搾ompiler新yntax暖p感arser::T_INTERFACE,
        'enum'          => xp搾ompiler新yntax暖p感arser::T_ENUM,
        'extends'       => xp搾ompiler新yntax暖p感arser::T_EXTENDS,
        'implements'    => xp搾ompiler新yntax暖p感arser::T_IMPLEMENTS,
        'instanceof'    => xp搾ompiler新yntax暖p感arser::T_INSTANCEOF,
        'clone'         => xp搾ompiler新yntax暖p感arser::T_CLONE,     

        'operator'      => xp搾ompiler新yntax暖p感arser::T_OPERATOR,
        'throws'        => xp搾ompiler新yntax暖p感arser::T_THROWS,

        'throw'         => xp搾ompiler新yntax暖p感arser::T_THROW,
        'try'           => xp搾ompiler新yntax暖p感arser::T_TRY,
        'catch'         => xp搾ompiler新yntax暖p感arser::T_CATCH,
        'finally'       => xp搾ompiler新yntax暖p感arser::T_FINALLY,
        
        'return'        => xp搾ompiler新yntax暖p感arser::T_RETURN,
        'new'           => xp搾ompiler新yntax暖p感arser::T_NEW,
        'as'            => xp搾ompiler新yntax暖p感arser::T_AS,
        'this'          => xp搾ompiler新yntax暖p感arser::T_THIS,
        
        'for'           => xp搾ompiler新yntax暖p感arser::T_FOR,
        'foreach'       => xp搾ompiler新yntax暖p感arser::T_FOREACH,
        'in'            => xp搾ompiler新yntax暖p感arser::T_IN,
        'do'            => xp搾ompiler新yntax暖p感arser::T_DO,
        'while'         => xp搾ompiler新yntax暖p感arser::T_WHILE,
        'break'         => xp搾ompiler新yntax暖p感arser::T_BREAK,
        'continue'      => xp搾ompiler新yntax暖p感arser::T_CONTINUE,

        'with'          => xp搾ompiler新yntax暖p感arser::T_WITH,

        'if'            => xp搾ompiler新yntax暖p感arser::T_IF,
        'else'          => xp搾ompiler新yntax暖p感arser::T_ELSE,
        'switch'        => xp搾ompiler新yntax暖p感arser::T_SWITCH,
        'case'          => xp搾ompiler新yntax暖p感arser::T_CASE,
        'default'       => xp搾ompiler新yntax暖p感arser::T_DEFAULT,
      );

    protected static
      $lookahead= array(
        '.' => array('..' => xp搾ompiler新yntax暖p感arser::T_DOTS),
        '-' => array('-=' => xp搾ompiler新yntax暖p感arser::T_SUB_EQUAL, '--' => xp搾ompiler新yntax暖p感arser::T_DEC),
        '>' => array('>=' => xp搾ompiler新yntax暖p感arser::T_GE, '>>' => xp搾ompiler新yntax暖p感arser::T_SHR),
        '<' => array('<=' => xp搾ompiler新yntax暖p感arser::T_SE, '<<' => xp搾ompiler新yntax暖p感arser::T_SHL),
        '~' => array('~=' => xp搾ompiler新yntax暖p感arser::T_CONCAT_EQUAL),
        '+' => array('+=' => xp搾ompiler新yntax暖p感arser::T_ADD_EQUAL, '++' => xp搾ompiler新yntax暖p感arser::T_INC),
        '*' => array('*=' => xp搾ompiler新yntax暖p感arser::T_MUL_EQUAL),
        '%' => array('%=' => xp搾ompiler新yntax暖p感arser::T_MOD_EQUAL),
        '=' => array('==' => xp搾ompiler新yntax暖p感arser::T_EQUALS, '=>' => xp搾ompiler新yntax暖p感arser::T_DOUBLE_ARROW),
        '!' => array('!=' => xp搾ompiler新yntax暖p感arser::T_NOT_EQUALS),
        ':' => array('::' => xp搾ompiler新yntax暖p感arser::T_DOUBLE_COLON),
        '|' => array('||' => xp搾ompiler新yntax暖p感arser::T_BOOLEAN_OR, '|=' => xp搾ompiler新yntax暖p感arser::T_OR_EQUAL),
        '&' => array('&&' => xp搾ompiler新yntax暖p感arser::T_BOOLEAN_AND, '&=' => xp搾ompiler新yntax暖p感arser::T_AND_EQUAL),
        '^' => array('^=' => xp搾ompiler新yntax暖p感arser::T_XOR_EQUAL),
      );

    const 
      DELIMITERS = " ^|&?!.:;,@%~=<>(){}[]#+-*/\"'\r\n\t\$`";

    public
      $fileName  = NULL;

    protected
      $comment   = NULL,
      $tokenizer = NULL,
      $forward   = array();

    /**
     * Constructor
     *
     * @param   var input either a string or an InputStream
     * @param   string source
     */
    public function __construct($input, $source) {
      if ($input instanceof InputStream) {
        $this->tokenizer= new StreamTokenizer($input, self::DELIMITERS, TRUE);
      } else {
        $this->tokenizer= new StringTokenizer($input, self::DELIMITERS, TRUE);
      }
      $this->fileName= $source;
      $this->position= $this->forward= array(1, 1);   // Y, X
    }

    /**
     * Create a new node 
     *
     * @param   xp.compiler.ast.Node
     * @param   bool comment default FALSE whether to pass comment
     * @return  xp.compiler.ast.Node
     */
    public function create($n, $comment= FALSE) {
      $n->position= $this->position;
      if ($comment && $this->comment) {
        $n->comment= $this->comment;
        $this->comment= NULL;
      }
      return $n;
    }

    /**
     * Get next token and recalculate position
     *
     * @param   string delim default self::DELIMITERS
     * @return  string token
     */
    protected function nextToken($delim= self::DELIMITERS) {
      $t= $this->tokenizer->nextToken($delim);
      $l= substr_count($t, "\n");
      if ($l > 0) {
        $this->forward[0]+= $l;
        $this->forward[1]= strlen($t) - strrpos($t, "\n");
      } else {
        $this->forward[1]+= strlen($t);
      }
      return $t;
    }
    
    /**
     * Push back token and recalculate position
     *
     * @param   string token
     */
    protected function pushBack($t) {
      $l= substr_count($t, "\n");
      if ($l > 0) {
        $this->forward[0]-= $l;
        $this->forward[1]= strlen($t) - strrpos($t, "\n");
      } else {
        $this->forward[1]-= strlen($t);
      }
      $this->tokenizer->pushBack($t);
    }
    
    /**
     * Throws an error, appending the starting position to the message
     *
     * @param   string class
     * @param   string message
     * @throws  lang.Throwable
     */
    protected function raise($class, $message) {
      raise($class, $message.' starting at line '.$this->position[0].', offset '.$this->position[1]);
    }
  
    /**
     * Advance this 
     *
     * @return  bool
     */
    public function advance() {
      while ($hasMore= $this->tokenizer->hasMoreTokens()) {
        $this->position= $this->forward;
        $token= $this->nextToken();
        
        // Check for whitespace-only
        if (FALSE !== strpos(" \n\r\t", $token)) {
          continue;
        } else if ("'" === $token{0} || '"' === $token{0}) {
          $this->token= xp搾ompiler新yntax暖p感arser::T_STRING;
          $this->value= '';
          do {
            if ($token{0} === ($t= $this->nextToken($token{0}))) {
              // Empty string, e.g. "" or ''
              break;
            }
            $this->value.= $t;
            $l= strlen($this->value);
            if ($l > 0 && '\\' === $this->value{$l- 1} && !($l > 1 && '\\' === $this->value{$l- 2})) {
              $this->value= substr($this->value, 0, -1).$this->nextToken($token{0});
              continue;
            } 
            if ($token{0} !== $this->nextToken($token{0})) {
              $this->raise('lang.IllegalStateException', 'Unterminated string literal');
            }
            break;
          } while ($hasMore= $this->tokenizer->hasMoreTokens());
          if ('"' === $token{0}) {
            try {
              $this->value= Strings::expandEscapesIn($this->value);
            } catch (FormatException $e) {
              $this->raise('lang.FormatException', $e->getMessage());
            }
          } else {
            $this->value= str_replace('\\\\', '\\', $this->value);
          }
        } else if ('$' === $token{0}) {
          $this->token= xp搾ompiler新yntax暖p感arser::T_VARIABLE;
          $this->value= $this->nextToken();
        } else if (isset(self::$keywords[$token])) {
          $this->token= self::$keywords[$token];
          $this->value= $token;
        } else if ('/' === $token{0}) {
          $ahead= $this->nextToken();
          if ('/' === $ahead) {           // Single-line comment
            $this->nextToken("\n");
            continue;
          } else if ('*' === $ahead) {    // Multi-line comment
            $comment= '';
            do { 
              $t= $this->nextToken('/'); 
              $comment.= $t;
            } while ('*' !== $t{strlen($t)- 1});
            
            // Copy api doc comments
            if ($comment && '*' === $comment{0}) {
              $this->comment= $comment;
            }
            $this->nextToken('/');
            continue;
          } else if ('=' === $ahead) {
            $this->token= xp搾ompiler新yntax暖p感arser::T_DIV_EQUAL;
            $this->value= '/=';
          } else {
            $this->token= ord($token);
            $this->value= $token;
            $this->pushBack($ahead);
          }
        } else if (isset(self::$lookahead[$token])) {
          $ahead= $this->nextToken();
          $combined= $token.$ahead;
          if (isset(self::$lookahead[$token][$combined])) {
            $this->token= self::$lookahead[$token][$combined];
            $this->value= $combined;
          } else {
            $this->token= ord($token);
            $this->value= $token;
            $this->pushBack($ahead);
          }
        } else if (FALSE !== strpos(self::DELIMITERS, $token) && 1 == strlen($token)) {
          $this->token= ord($token);
          $this->value= $token;
        } else if (ctype_digit($token)) {
          $ahead= $this->nextToken();
          if ('.' === $ahead{0}) {
            $decimal= $this->nextToken();
            if (!ctype_digit($decimal)) {
              $this->raise('lang.FormatException', 'Illegal decimal number <'.$token.$ahead.$decimal.'>');
            }
            $this->token= xp搾ompiler新yntax暖p感arser::T_DECIMAL;
            $this->value= $token.$ahead.$decimal;
          } else {
            $this->token= xp搾ompiler新yntax暖p感arser::T_NUMBER;
            $this->value= $token;
            $this->pushBack($ahead);
          }
        } else if ('0' === $token{0} && 'x' === @$token{1}) {
          if (!ctype_xdigit(substr($token, 2))) {
            $this->raise('lang.FormatException', 'Illegal hex number <'.$token.'>');
          }
          $this->token= xp搾ompiler新yntax暖p感arser::T_HEX;
          $this->value= $token;
        } else {
          $this->token= xp搾ompiler新yntax暖p感arser::T_WORD;
          $this->value= $token;
        }
        
        break;
      }
      
      // DEBUG fprintf(STDERR, "@ %3d,%3d: %d `%s`\n", $this->position[1], $this->position[0], $this->token, addcslashes($this->value, "\0..\17"));
      return -1 === $this->token ? FALSE : $hasMore;
    }
  }
?>
