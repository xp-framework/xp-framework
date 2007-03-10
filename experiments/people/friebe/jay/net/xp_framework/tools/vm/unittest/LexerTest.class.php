<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'net.xp_framework.tools.vm.Parser',
    'net.xp_framework.tools.vm.Lexer'
  );

  /**
   * Tests Lexer utility class
   *
   * @purpose  Unit Test
   */
  class LexerTest extends TestCase {

    /**
     * Helper method
     *
     * @access  protected
     * @param   string tokens comma-delimited token/value list
     * @param   string src
     * @throws  unittest.AssertionFailedError in case an error occurs
     */
    public function assertTokens($tokens, $src) {
      $l= new Lexer($src, $this->name);
      
      for ($i= 0, $t= strtok($tokens, ', '); $l->advance(); $i++, $t= strtok(', ')) {
        if ('"' == $t{0}) {
          $value= substr($t, 1, -1);
          $token= ord($value);
        } else if ('0' == $t{0}) {
          $token= hexdec($t);
          $value= chr($token);
        } else if (FALSE !== ($p= strpos($t, '<'))) {
          $token= constant('TOKEN_'.substr($t, 0, $p));
          $value= substr($t, $p+ 1, strrpos($t, '>')- $p- 1);
        } else {
          $token= constant('TOKEN_'.$t);
          $value= NULL;
        }
        
        if (($token != $l->token) || ($value !== NULL && $value != $l->value)) {
          throw new AssertionFailedError(
            'At position '.$i.' of <'.$src.'>', 
            array($l->token, $l->value),  // Actual
            array($token, $value)         // Expected
          );
        }
      }
    }
    
    /**
     * Tests keywords tokens
     *
      */
    #[@test]
    public function keyWords() {
      $keywords= array(
        'abstract'     => 'T_ABSTRACT',
        'as'           => 'T_AS',
        'break'        => 'T_BREAK',
        'case'         => 'T_CASE',
        'catch'        => 'T_CATCH',
        'class'        => 'T_CLASS',
        // 'clone'        => 'T_CLONE',   - not yet implemented
        'const'        => 'T_CONST',
        'continue'     => 'T_CONTINUE',
        'declare'      => 'T_DECLARE',
        'default'      => 'T_DEFAULT',
        'do'           => 'T_DO',
        'else'         => 'T_ELSE',
        'enum'         => 'T_ENUM',
        'extends'      => 'T_EXTENDS',
        'final'        => 'T_FINAL',
        'finally'      => 'T_FINALLY',
        'for'          => 'T_FOR',
        'foreach'      => 'T_FOREACH',
        'function'     => 'T_FUNCTION',
        'if'           => 'T_IF',
        'import'       => 'T_IMPORT',
        'implements'   => 'T_IMPLEMENTS',
        'interface'    => 'T_INTERFACE',
        'instanceof'   => 'T_INSTANCEOF',
        'native'       => 'T_NATIVE',
        'new'          => 'T_NEW',
        'operator'     => 'T_OPERATOR',
        'package'      => 'T_PACKAGE',
        'private'      => 'T_PRIVATE',
        'property'     => 'T_PROPERTY',
        'protected'    => 'T_PROTECTED',
        'public'       => 'T_PUBLIC',
        'return'       => 'T_RETURN',
        // 'self'         => 'T_SELF',    - not yet implemented
        'static'       => 'T_STATIC',
        // 'super'        => 'T_SUPER',   - not yet implemented
        'switch'       => 'T_SWITCH',
        // 'this'         => 'T_THIS',    - not yet implemented
        'throw'        => 'T_THROW',
        'throws'       => 'T_THROWS',     // unsure about whether this needs to be a keyword!
        'try'          => 'T_TRY',
        // 'using'        => 'T_USING',   - not yet implemented
        'var'          => 'T_VAR',
        'void'         => 'T_VOID',       // unsure about whether this needs to be a keyword!
        'while'        => 'T_WHILE',
        '__construct'  => 'T_CONSTRUCT',  // unsure about whether this needs to be a keyword!
        '__destruct'   => 'T_DESTRUCT',   // unsure about whether this needs to be a keyword!
      );
      
      $this->assertTokens(
        implode(', ', array_values($keywords)), 
        implode(' ', array_keys($keywords))
      );
    }

    /**
     * Tests PHP-style strings with variables which we do not support
     *
      */
    #[@test]
    public function stringsWithVariables() {
      $strings= array(
        '"$name"',
        '"${name}"',
      );
    
      foreach ($strings as $string) {
        $this->assertTokens('T_CONSTANT_ENCAPSED_STRING<'.$string.'>', $string);
      }
    }

    /**
     * Tests PHP4 and PHP5 keywords that we don't regard as keywords
     *
      */
    #[@test]
    public function phpKeywords() {
      $deprecated= array(
        'require',
        'include',
        'require_once',
        'include_once',
        'eval',
        'isset',
        'unset',
        'use',
        'endif',
        'endwhile',
        'endfor',
        'endforeach',
        'enddeclare',
        // 'cfunction',   - maps to T_FUNCTION
        'exit',
        'and',
        'or',
        'print',
        'global',
        // 'list',        - needed at the moment
        // 'array',       - needed at the moment
        'empty',
        'elsif',
      );
    
      foreach ($deprecated as $word) {
        $this->assertTokens('T_STRING<'.$word.'>', $word);
      }
    }

    /**
     * Tests PHP4 and PHP5 "magic" constants (__*__) that we don't regard as
     * keywords.
     *
      */
    #[@test]
    public function phpMagicConstants() {
      $deprecated= array(
        '__FILE__',
        '__LINE__',
        '__CLASS__',
        '__FUNCTION__',
        '__METHOD__',
        '__COMPILER_HALT_OFFSET__',
      );
    
      foreach ($deprecated as $word) {
        $this->assertTokens('T_STRING<'.$word.'>', $word);
      }
    }

    /**
     * Tests operator tokens
     *
      */
    #[@test]
    public function operatorTokens() {
      $operators= array(
        '::'        => 'T_DOUBLE_COLON',
        '->'        => 'T_OBJECT_OPERATOR',
        '||'        => 'T_BOOLEAN_OR',
        '&&'        => 'T_BOOLEAN_AND',
        '++'        => 'T_INC',
        '--'        => 'T_DEC',
        '>>'        => 'T_SR',
        '<<'        => 'T_SL',
        '=>'        => 'T_DOUBLE_ARROW',
        '>='        => 'T_IS_GREATER_OR_EQUAL',
        '<='        => 'T_IS_SMALLER_OR_EQUAL',
        '=='        => 'T_IS_EQUAL',
        '!='        => 'T_IS_NOT_EQUAL',
        '+='        => 'T_PLUS_EQUAL',
        '.='        => 'T_CONCAT_EQUAL',
        '-='        => 'T_MINUS_EQUAL',
        '/='        => 'T_DIV_EQUAL',
        '*='        => 'T_MUL_EQUAL',
        '%='        => 'T_MOD_EQUAL',
        '|='        => 'T_OR_EQUAL',
        '^='        => 'T_XOR_EQUAL',
        '&='        => 'T_AND_EQUAL',
        '>>='       => 'T_SR_EQUAL',
        '<<='       => 'T_SL_EQUAL',
        '==='       => 'T_IS_IDENTICAL',
        '!=='       => 'T_IS_NOT_IDENTICAL',
        // '<=>'       => 'T_COMPARE',    - not yet implemented
      );
      
      $this->assertTokens(
        implode(', ', array_values($operators)), 
        implode(' ', array_keys($operators))
      );
    }
    
    /**
     * Tests echo and constant string
     *
      */
    #[@test]
    public function echoAndString() {
      $this->assertTokens(
        'T_ECHO, T_CONSTANT_ENCAPSED_STRING<"Hello">, ";"', 
        'echo "Hello"'
      );
    }

    /**
     * Tests variable assignment
     *
      */
    #[@test]
    public function variableAssignment() {
      $this->assertTokens(
        'T_VARIABLE<$i>, "=", T_LNUMBER<0>, ";"', 
        '$i= 0;'
      );
      $this->assertTokens(
        'T_VARIABLE<$f>, "=", T_DNUMBER<0.0>, ";"', 
        '$f= 0.0;'
      );
      $this->assertTokens(
        'T_VARIABLE<$s>, "=", T_CONSTANT_ENCAPSED_STRING<"string">, ";"', 
        '$s= "string";'
      );
       $this->assertTokens(
        'T_VARIABLE<$b>, "=", T_STRING<TRUE>, ";", T_VARIABLE<$b>, "=", T_STRING<FALSE>, ";"', 
        '$b= TRUE; $b= FALSE;'
      );
      $this->assertTokens(
        'T_VARIABLE<$b>, "=", T_STRING<NULL>, ";"', 
        '$b= NULL;'
      );
    }

    /**
     * Tests class declarations (class, enum, interface)
     *
      */
    #[@test]
    public function classDeclarations() {
      $this->assertTokens(
        'T_CLASS, T_STRING<CFoo>, T_EXTENDS, T_STRING<Bar>, "{", "}"', 
        'class CFoo extends Bar { }'
      );
      $this->assertTokens(
        'T_ENUM, T_STRING<EFoo>, "{", "}"', 
        'enum EFoo { }'
      );
      $this->assertTokens(
        'T_INTERFACE, T_STRING<IFoo>, T_EXTENDS, T_STRING<IBar>, 0x2c, T_STRING<IBaz>, "{", "}"', 
        'interface IFoo extends IBar, IBaz { }'
      );
    }

    /**
     * Tests control flow keywords (if / else / switch)
     *
      */
    #[@test]
    public function controlFlow() {
      $this->assertTokens(
        'T_IF, "(", T_VARIABLE<$a>, ")", T_VARIABLE<$b>, "=", T_STRING<TRUE>, ";", T_ELSE, T_VARIABLE<$c>, T_INC, ";"', 
        'if ($a) $b= TRUE; else $c++;'
      );
      $this->assertTokens(
        'T_SWITCH, "(", T_VARIABLE<$s>, ")", "{", T_CASE, T_LNUMBER<1>, ":", T_BREAK, ";", T_DEFAULT, ":", T_RETURN, ";", "}"', 
        'switch ($s) { case 1: break; default: return; }'
      );
    }

    /**
     * Tests loop keywords (for, foreach, do, while)
     *
      */
    #[@test]
    public function loops() {
      $this->assertTokens(
        'T_FOR, "(", T_VARIABLE<$i>, "=", T_LNUMBER<0>, ";", T_VARIABLE<$i>, "<", T_LNUMBER<100>, ";" T_VARIABLE<$i>, T_INC, ")", ";"', 
        'for ($i= 0; $i < 100; $i++);'
      );
      $this->assertTokens(
        'T_FOREACH, "(", T_VARIABLE<$list>, T_AS, T_VARIABLE<$k>, T_DOUBLE_ARROW, T_VARIABLE<$v>, ")", ";"', 
        'foreach ($list as $k => $v);'
      );
      $this->assertTokens(
        'T_DO, "{", T_VARIABLE<$i>, T_INC, ";", "}", T_WHILE, "(", T_VARIABLE<$i>, "<", T_LNUMBER<100>, ")", ";"', 
        'do { $i++; } while ($i < 100);'
      );
      $this->assertTokens(
        'T_WHILE, "(", T_VARIABLE<$i>, "<", T_LNUMBER<100>, ")", "{", T_VARIABLE<$i>, T_INC, ";", "}"', 
        'while ($i < 100) { $i++; }'
      );
    }
  }
?>
