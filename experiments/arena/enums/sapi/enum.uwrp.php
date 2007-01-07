<?php
/* This file provides the enum uses wrapper for the XP framework
 * 
 * $Id$
 */

  uses('Enum');

  // {{{ class uwrp·enum
  //     Stream wrapper for uses()
  class uwrp·enum {
    var
      $buffer     = '',
      $offset     = 0;

    function stream_open($path, $mode, $options, &$open) {
      $url= parse_url($path);
      
      $tokens= token_get_all(file_get_contents(strtr($url['host'], '.', DIRECTORY_SEPARATOR).'.enum.php'));
      $state= ENUM_PARSER_ST_INITIAL;
      $bracket= '';
      for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
        switch ($state.$tokens[$i][0]) {
          case ENUM_PARSER_ST_INITIAL.T_STRING:
            if ('enum' != $tokens[$i][1]) {
              $this->buffer.= $tokens[$i];
              break;
            }
            do { $i++;} while (T_STRING !== $tokens[$i][0]);
            $class= $tokens[$i][1];
            $this->buffer.= 'class '.$class.' extends Enum';
            $state= ENUM_PARSER_ST_DECL;
            break;
          
          case ENUM_PARSER_ST_DECL.T_STRING:
            $member= $tokens[$i][1];
            $value= NULL;
            break;
          
          case ENUM_PARSER_ST_DECL.'(':
            $value= '';
            do { 
              $i++;
              $value.= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
            } while (')' !== $tokens[$i+ 1][0]);
            $i++;
            break;

          case ENUM_PARSER_ST_DECL.',':
            $members[$member]= $value;
            break;
          
          case ENUM_PARSER_ST_DECL.'}': 
            $bracket= '}';
            // Fall through

          case ENUM_PARSER_ST_DECL.';':
            $state= ENUM_PARSER_ST_BODY;
            
            // static initializer
            $this->buffer.= 'public static $registry= array();';
            $this->buffer.= 'static function __static() { self::$registry= array(';
            foreach (array_keys($members) as $ordinal => $member) {
              define($member, $ordinal, 0);
              $this->buffer.= '  '.$member.' => new '.$class.'(\''.$member.'\', '.xp::stringOf($members[$member]).'),';
            }
            $this->buffer.= '); } ';
            
            // size() method
            $this->buffer.= 'public static function size() { return '.(sizeof($members)+ 1).'; } ';
            
            // values() method
            $this->buffer.= 'public static function values() { return self::$registry; } ';
            
            // valueOf() method
            $this->buffer.= 'public static function valueOf($ordinal) { return self::$registry[$ordinal]; }';
            
            // Add closing bracket if necessary
            $this->buffer.= $bracket;
            break;
          
          default:
            $this->buffer.= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
        }
      }
      
      // DEBUG echo $this->buffer;
      return TRUE;
    }  

    function stream_read($count) {
      $chunk= substr($this->buffer, $this->offset, $count);
      $this->offset+= $count;
      return $chunk;
    }

    function stream_eof() {
      return $this->offset > strlen($this->buffer);
    }
  }
  // }}}
?>
