<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');

  define('NAMESPACE_SEPARATOR',   '·');

  // {{{ final class compiler
  //     Stream wrapper
  class uwrp·compiler {
    var
      $buffer     = '',
      $offset     = 0;

    function resolveClass($name, $current, $imports) {
      if ('self' == $name) {
        return $current;
      } else if (isset($imports[$name])) {
        return $imports[$name];
      } else {
        return $name;
      }
    }

    // {{{ bool stream_open(string path, string mode, int options, &string open)
    //     Open wrapper
    function stream_open($path, $mode, $options, &$open) {
      $url= parse_url($path);
      $file= strtr($url['host'], '.', DIRECTORY_SEPARATOR).'.class.php';

      if (!($contents= file_get_contents($file))) return FALSE;
      
      $tokens= token_get_all('<?php '.$contents.' ?>');
      $this->buffer= '';
      $package= NULL;
      $inFunction= FALSE;
      $implements= array();
      $reflection= array();
      $imports= array();
      $modifiers= 0;

      for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
        switch ($tokens[$i][0]) {
          case T_STRING: {
            switch ($tokens[$i][1]) {
              case 'package': {
                $package= $tokens[$i+ 2][1];
                $i+= 2;
                break;
              }
              
              case 'implements': {
                $i++;
                do {
                  if (T_STRING == $tokens[$i][0]) {
                    $implements[]= strtr($this->resolveClass($tokens[$i][1], $class, $imports), NAMESPACE_SEPARATOR, '.');
                  }
                } while ('{' != $tokens[$i++][0]);
                $i-= 2;
                break;
              }
              
              case 'interface': {
                $class= ltrim($package.NAMESPACE_SEPARATOR.$tokens[$i+ 2][1], NAMESPACE_SEPARATOR);
                $reflection= array(
                  'modifiers' => $modifiers,
                  'methods'   => array()
                );
                $modifiers= array();
                xp::registry('class.'.strtolower($class), strtr($class, NAMESPACE_SEPARATOR, '.'));
                $this->buffer.= 'class '.$class.('extends' == $tokens[$i+ 4][1] ? '' : ' extends Interface');
                $i+= 2;
                break;
              }

              case 'throws': {
                $i++;
                do {
                  if (T_STRING == $tokens[$i][0]) {
                    $reflection['methods'][$inFunction]['throws'][]= strtr($this->resolveClass($tokens[$i][1], $class, $imports), NAMESPACE_SEPARATOR, '.');
                  }
                } while ('{' != $tokens[$i++][0]);
                $i-= 2;
                break;
              }
              
              case 'import': {
                $name= $tokens[$i+ 2][1];
                while ('~' == $tokens[$i+ 3]) {
                  $name.= '·'.$tokens[++$i+ 3][1];
                  $i++;
                }
                $imports[substr($name, strrpos($name, NAMESPACE_SEPARATOR)+ 1)]= $name;
                $i+= 3;
                $this->buffer.= 'include(\'xp://'.strtr($name, NAMESPACE_SEPARATOR, '.').'\');';
                break;
              }
              
              case 'try': {
                $this->buffer.= 'try();';
                break;
              }

              case 'catch': {
                $this->buffer.= 'if (catch(\''.$this->resolveClass($tokens[$i+ 3][1], $class, $imports).'\', '.$tokens[$i+ 5][1].'))';
                $i+= 6;
                break;
              }
              
              case 'throw': {
                $this->buffer.= 'return throw';
                break;
              }

              case 'public':
              case 'private':
              case 'protected': {
                if (T_VARIABLE == $tokens[$i+ 2][0]) {
                  $this->buffer.= 'var '.$tokens[$i+ 1][1];
                } else {
                  $modifiers |= constant('MODIFIER_'.strtoupper($tokens[$i][1]));
                }
                $i++;
                break;
              }
                 
              case 'final':
              case 'abstract': {
                $modifiers |= constant('MODIFIER_'.strtoupper($tokens[$i][1]));
                $i++;
                break;
              }

              default:
                $this->buffer.= $tokens[$i][1];
            }
            break;
          }
          
          case T_EXTENDS: {
            $extends= $tokens[$i+ 2][1];
            while ('~' == $tokens[$i+ 3]) {
              $extends.= '·'.$tokens[++$i+ 3][1];
              $i++;
            }
            $this->buffer.= 'extends '.$this->resolveClass($extends, $class, $imports);
            $i+= 2;
            break;
          }
          
          case T_NEW: {
            $name= $tokens[$i+ 2][1];
            while ('~' == $tokens[$i+ 3]) {
              $name.= '·'.$tokens[++$i+ 3][1];
              $i++;
            }
            $this->buffer.= 'new '.$this->resolveClass($name, $class, $imports);
            $i+= 2;
            break;
          }
          
          case T_DOUBLE_COLON:
            $this->buffer= (
              substr($this->buffer, 0, -1 * strlen($tokens[$i- 1][1])).
              $this->resolveClass($tokens[$i- 1][1], $class, $imports).'::'
            );
            break;
          
          case T_FUNCTION: {
            $reference= '&' == $tokens[$i+ 2];
            $inFunction= $tokens[$i+ 2+ $reference][1];
            $reflection['methods'][$inFunction]= array(
              'modifiers' => $modifiers,
              'throws'    => array()
            );
            $modifiers= 0;
            $throws= array();
            $functionBrackets= 0;
            $this->buffer.= 'function';
            break;
          }

          case T_OBJECT_OPERATOR:
            if (T_VARIABLE != $tokens[$i- 1][0]) {
              // TBI: Search backwards until end of expression
            }
            $this->buffer.= '->';
            
            break;
          
          case T_STATIC: {
            if ($inFunction) {
              $this->buffer.= 'static';
            } else {
              $modifiers |= MODIFIER_STATIC;
              $i++;
            }
            break;
          }
          
          case T_CLASS: {
            $class= ltrim($package.NAMESPACE_SEPARATOR.$tokens[$i+ 2][1], NAMESPACE_SEPARATOR);
            $reflection= array(
              'modifiers' => $modifiers,
              'methods'   => array()
            );
            xp::registry('class.'.strtolower($class), strtr($class, NAMESPACE_SEPARATOR, '.'));
            $this->buffer.= 'class '.$class;
            $i+= 2;
            break;
          }

          case T_CLOSE_TAG: {
            $this->buffer.= '?>';
            break 2;
          }
          
          case '{':
            $inFunction && $functionBrackets++;
            $this->buffer.= '{';
            break;

          case '}':
            if ($inFunction) {
              $functionBrackets--;
              if (0 == $functionBrackets) $inFunction= FALSE;
            }
            $this->buffer.= '}';
            break;

          case '[':
            if (!$inFunction) {   // Where else would we allow "["?
              // DEBUG echo $class.'::ANNOTATION!', "\n";
              $this->buffer.= '#[';
            } else {
              $this->buffer.= '[';
            }
            break;

          default:
            $this->buffer.= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
        }
      }
      
      if (!empty($implements)) {
        $this->buffer.= '  implements(\''.$class.'.class.php\', \''.implode('\', ', $implements).'\');'."\n".'?>';
      }
      
      // DEBUG echo $url['host'], ' => '; var_dump($this->buffer, $reflection);

      return TRUE;
    }  
    // }}}

    // {{{ string stream_read(int count)
    //     Read wrapper
    function stream_read($count) {
      $chunk= substr($this->buffer, $this->offset, $count);
      $this->offset+= $count;
      return $chunk;
    }
    // }}}

    // {{{ bool stream_eof(void)
    //     EOF wrapper
    function stream_eof() {
      return $this->offset > strlen($this->buffer);
    }
    // }}}
  }
  // }}}

  // {{{ main
  stream_register_wrapper('xp', 'uwrp·compiler');
  $main= strtr(substr($_SERVER['PHP_SELF'], 0, -10), array(
    '/'   => '.',
    '\\'  => '.'
  ));
  include('xp://'.$main);
  
  try(); {
    $r= call_user_func(array(strtr($main, '.', NAMESPACE_SEPARATOR), 'main'), $argv);
  } if (catch('Throwable', $e)) {
    xp::error('[xp::unhandled] '.xp::stringOf($e));
    // Bails out
  }
  exit((int)$r);
  // }}}
?>
