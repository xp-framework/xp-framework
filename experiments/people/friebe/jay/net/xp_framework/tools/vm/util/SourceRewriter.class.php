<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.doclet.RootDoc');

  define('ST_LOOKING_FOR_CLASS',          'looking:class');
  define('ST_FUNCTION_ARGS',              'function-args');
  define('ST_USES_LIST',                  'uses-list');
  define('ST_STATIC_INITIALIZER',         'static-initializer');
  
  /**
   * Rewrites sourececode
   *
   * @purpose  Utility
   */
  class SourceRewriter extends Object {
    public 
      $names= NULL;
    
    /**
     * Set name mapping. Required operation
     *
     * @param   net.xp_framework.tools.vm.util.NameMapping mapping
     */
    public function setNameMapping($mapping) {
      $this->names= $mapping;
    }
    
    /**
     * Rewrite tokens
     *
     * @param   array tokens as returned by token_get_all()
     * @param   bool debug default FALSE
     * @return  string
     * @throws  lang.Exception to indicate rewriting failures
     */
    public function rewrite($tokens, $debug= FALSE) {
      static $map= array(
        'uses'        => T_USES, 
        'define'      => T_DEFINE,
      );

      $out= '';
      $states= array(ST_INITIAL);
      $skip= FALSE;
      $brackets= array();

      // Compile list of classes to be added to uses()
      $used= array();
      $this->names->current->usedClasses->rewind();
      while ($this->names->current->usedClasses->hasNext()) {
        $class= $this->names->current->usedClasses->next();
        $used[]= strtr($this->names->packagedNameOf($class->qualifiedName()), NS_SEPARATOR, '.');
      }
      $this->names->current->interfaces->rewind();
      while ($this->names->current->interfaces->hasNext()) {
        $interface= $this->names->current->interfaces->next();
        $used[]= strtr($this->names->packagedNameOf($interface->qualifiedName()), NS_SEPARATOR, '.');
      }

      $package= NULL;
      for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
        $t= $tokens[$i];
        if (is_array($t) && isset($map[$t[1]])) $t[0]= $map[$t[1]];

        // Debug information
        if ($debug) {
          if (is_array($tokens[$i])) {
            Console::writeLinef('%-20s: %-30s %s', $states[0], token_name($tokens[$i][0]), $tokens[$i][1]);
          } else {
            Console::writeLinef('%-20s: %-30s %s', $states[0], 'T_NONE', $tokens[$i]);
          }
        }

        // State-based actions
        switch ($states[0].$t[0]) {
          case ST_INITIAL.T_OPEN_TAG:
            $t= '';
            break;

          case ST_INITIAL.T_CLOSE_TAG:
            $t= '';
            break;

          case ST_INITIAL.T_USES:
            $skip= TRUE;
            array_unshift($states, ST_USES_LIST);
            break;

          case ST_USES_LIST.';':
            $skip= FALSE;
            $t= '';
            array_shift($states);
            break;

          case ST_INITIAL.T_DOC_COMMENT:
            $qualified= $this->names->current->qualifiedName();
            $package= substr($qualified, 0, strrpos($qualified, '.'));

            sort($used);
            $uses= '';
            foreach ($used as $classname) {
              $uses.= '  import '.$classname.";\n";
            }
            $out= rtrim($out)."\npackage ".$this->names->packagedNameOf($package)." {\n".$uses."\n  ";
            break;

          case ST_INITIAL.T_CLASS:
          case ST_INITIAL.T_INTERFACE:
            if (!$package) {                              // Class apidoc was missing!
              $qualified= $this->names->current->qualifiedName();
              $package= substr($qualified, 0, strrpos($qualified, '.'));

              sort($used);
              $uses= '';
              foreach ($used as $classname) {
                $uses.= '  import '.$classname.";\n";
              }
              $out= rtrim($out)."\npackage ".$this->names->packagedNameOf($package)." {\n".$uses."\n  ";
            }
            array_unshift($states, ST_CLASS);
            break;

          case ST_CLASS.'{':
            $skip= FALSE;
            array_unshift($states, ST_CLASS_BODY);
            $brackets[ST_CLASS_BODY]= 1;
            break;

          case ST_CLASS_BODY.'}':
            array_shift($states);
            $t.= "\n}"; // One extra for package statement
            array_shift($states);
            break;

          case ST_CLASS_BODY.T_COMMENT:
            if ('#[' == substr($t[1], 0, 2)) {    // Annotation!
              $t[1]= substr($t[1], 1);
            }
            break;

          case ST_CLASS_BODY.T_FUNCTION:
            $skip= TRUE;
            array_unshift($states, ST_FUNCTION);
            break;

          case ST_INITIAL.T_FUNCTION:
            array_unshift($states, ST_FUNCTION);
            break;

          case ST_FUNCTION.'&':
            $skip= TRUE;
            break;

          case ST_FUNCTION.T_STRING:
            if ('__static' == $t[1]) {
              array_unshift($states, ST_STATIC_INITIALIZER);
              break;
            }
            $skip= FALSE;
            $abstract= FALSE;
            foreach ($this->names->current->methods as $method) {
              if ($method->name != $t[1]) continue;

              // Calculate return type, defaulting to "void"
              // No return types for con- and destructors!
              $type= '';
              if ('__construct' != $t[1] && '__destruct' != $t[1]) {
                $return= $method->tags('return');

                if (empty($return)) {
                  $type= 'void ';
                } else {
                  try {
                    $type= $this->names->forType($return[0]->type).' ';
                  } catch (IllegalArgumentException $e) {
                    Console::writeLine('In method '.$method->name.'() returning '.xp::stringOf($return[0]).' -> '.$e->getMessage());
                    $type= 'mixed ';
                  }
                }
              }

              $t[1]= $type.$t[1];
              break 2;
            }

            $method= NULL;
            throw new IllegalStateException('Cannot find method '.$t[1].'()');
            break;
          
          case ST_STATIC_INITIALIZER.'{':
            $skip= FALSE;
            array_shift($states);
            array_unshift($states, ST_FUNCTION_BODY);
            $brackets= 1;
            break;

          case ST_FUNCTION.'(':
            array_unshift($states, ST_FUNCTION_ARGS);
            $brackets= 1;
            $offset= 0;
            $arguments= $method ? $method->tags('param') : array();
            break;

          case ST_FUNCTION_ARGS.'(':
            $brackets++;
            break;

          case ST_FUNCTION_ARGS.T_STRING:   // Class type hint
          case ST_FUNCTION_ARGS.T_ARRAY:    // Array type hint
            $arguments[$offset]->type= $t[1];
            $skip= TRUE;
            break;

          case ST_FUNCTION_ARGS.T_VARIABLE:
            $skip= FALSE;
            $type= ($arguments[$offset]->type
              ? $this->names->forType($arguments[$offset]->type, TRUE).' '
              : ''
            );
            $t[1]= $type.$t[1];
            $offset++;
            break;

          case ST_FUNCTION_ARGS.'=':
            array_unshift($states, ST_ARGUMENT_VALUE);
            break;

          case ST_ARGUMENT_VALUE.',':
            array_shift($states);
            break;

          case ST_ARGUMENT_VALUE.T_ARRAY:
            $brackets= 0;
            $src= '';
            do {
              $t= $tokens[$i];
              $src.= is_array($t) ? $t[1] : $t;
              if ('(' == $t[0]) {
                $brackets++;
              } else {
                if (')' == $t[0] and --$brackets <= 0) break;
              }
            } while (++$i < $s);

            $t= $src;
            break;

          case ST_ARGUMENT_VALUE.')':
            array_shift($states);
            array_shift($states);
            break;
            
          case ST_FUNCTION_ARGS.')':
            $brackets--;
            if (0 == $brackets) {
              array_shift($states);
            }
            break;

          case ST_FUNCTION.'{':
            if ($method && ($throws= $method->tags('throws'))) {
              $t= 'throws ';
              foreach ($throws as $thrown) {
                $t.= strtr($thrown->exception->qualifiedName(), '.', NS_SEPARATOR).', ';
              }
              $t= substr($t, 0, -2).' {';
            }

            array_unshift($states, ST_FUNCTION_BODY);
            $brackets= 1;
            break;

          case ST_FUNCTION.';':   // Method without body
            array_shift($states);
            break;

          case ST_FUNCTION_BODY.'{':
            $brackets++;
            break;

          case ST_FUNCTION_BODY.'}':
            $brackets--;
            if (0 == $brackets) {

              // Shift off both function *and* function body
              array_shift($states);
              array_shift($states);
              $skip= FALSE;
            }
            break;

          case ST_CLASS.T_EXTENDS:
            if ('Object' == $tokens[$i+ 2][1]) {    // Look ahead
              $skip= TRUE;
            }
            array_unshift($states, ST_LOOKING_FOR_CLASS);
            break;

          case ST_INITIAL.T_NEW:
          case ST_FUNCTION_BODY.T_NEW:
            array_unshift($states, ST_LOOKING_FOR_CLASS);
            break;

          case ST_INITIAL.T_STRING:
          case ST_FUNCTION_BODY.T_STRING:
            if (T_DOUBLE_COLON == $tokens[$i+ 1][0]) {    // Look ahead
              $t[1]= $this->names->packagedNameOf($this->names->qualifiedNameOf($t[1]));
            }
            break;

          case ST_LOOKING_FOR_CLASS.T_STRING:
            $t[1]= $this->names->packagedNameOf($this->names->qualifiedNameOf($t[1]));
            array_shift($states);
            break;

          case ST_LOOKING_FOR_CLASS.T_VARIABLE:
            array_shift($states);
            break;

          case ST_FUNCTION_BODY.'.':
          case ST_INITIAL.'.':
            $t= ' ~ ';
            break;

          case ST_FUNCTION_BODY.T_CONCAT_EQUAL:
            $t[1]= '~=';
            break;

        }

        $skip || $out.= is_array($t) ? $t[1] : $t;
      }
      return $out;
    }
  }
?>
