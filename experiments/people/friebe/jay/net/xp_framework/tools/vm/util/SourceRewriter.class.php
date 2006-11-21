<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.doclet.RootDoc');

  define('ST_LOOKING_FOR_CLASS',          'looking:class');
  define('ST_LOOKING_FOR_TRY_BRACKET',    'looking:trybracket');
  define('ST_LOOKING_FOR_THROW',          'looking:throw');
  define('ST_LOOKING_FOR_INTERFACES_END', 'looking:iface_end');
  define('ST_LOOKING_FOR_CATCH',          'looking:catch');
  define('ST_FUNCTION_ARGS',              'function-args');
  define('ST_USES_LIST',                  'uses-list');
  define('ST_INSTANCE_OF',                'instance-of');
  
  define('T_TRY',                0x2000);
  define('T_CATCH',              0x2001);
  define('T_THROW',              0x2002);
  define('T_IS_A',               0x2003);

  /**
   * Rewrites sourececode
   *
   * @purpose  Utility
   */
  class SourceRewriter extends Object {
    var
      $names= NULL;
    
    /**
     * Set name mapping. Required operation
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.util.NameMapping mapping
     */
    function setNameMapping(&$mapping) {
      $this->names= &$mapping;
    }
    
    /**
     * Rewrite tokens
     *
     * @access  public
     * @param   array tokens as returned by token_get_all()
     * @param   bool debug default FALSE
     * @return  string
     * @throws  lang.Exception to indicate rewriting failures
     */
    function rewrite($tokens, $debug= FALSE) {
      static $map= array(
        'uses'        => T_USES, 
        'implements'  => T_IMPLEMENTS, 
        'define'      => T_DEFINE,
        'try'         => T_TRY,
        'catch'       => T_CATCH,
        'throw'       => T_THROW,
        'is_a'        => T_IS_A
      );

      $out= '';
      $states= array(ST_INITIAL);
      $skip= FALSE;
      $brackets= 0;
      $classloader= &ClassLoader::getDefault();

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

          case ST_INITIAL.T_COMMENT:
            if ('/**' == substr($t[1], 0, 3)) {           // Class apidoc
              $qualified= $this->names->current->qualifiedName();
              $package= substr($qualified, 0, strrpos($qualified, '.'));

              sort($used);
              $uses= '';
              foreach ($used as $classname) {
                $uses.= 'import '.$classname.";\n";
              }
              $out= rtrim($out)."\n\n".$uses."\npackage ".$this->names->packagedNameOf($package)." {\n\n  ";
            }
            break;

          case ST_INITIAL.T_CLASS:
            if (!$package) {                              // Class apidoc was missing!
              $qualified= $this->names->current->qualifiedName();
              $package= substr($qualified, 0, strrpos($qualified, '.'));

              sort($used);
              $uses= '';
              foreach ($used as $classname) {
                $uses.= 'import '.$classname.";\n";
              }
              $out= rtrim($out)."\n\n".$uses."\n\npackage ".$this->names->packagedNameOf($package)." {\n\n  ";
            }
            array_unshift($states, ST_CLASS);
            $this->names->current->isInterface() && $t[1]= 'interface';
            break;

          case ST_CLASS.'{':
            if (!empty($this->current->interfaces->classes)) {
              $out.= 'implements ';
              $this->current->interfaces->rewind();
              while ($this->current->interfaces->hasNext()) {
                $interface= $this->current->interfaces->next();
                $out.= $this->names->packagedNameOf($interface->qualifiedName()).', ';
              }
              $out= substr($out, 0, -2).' ';
            }
            $skip= FALSE;
            array_unshift($states, ST_CLASS_BODY);
            $brackets[ST_CLASS_BODY]= 1;
            break;

          case ST_CLASS_BODY.'}':
            array_shift($states);
            $t.= "\n}"; // One extra for package statement
            array_shift($states);
            break;

          case ST_CLASS_BODY.T_VAR:
            $t[1]= 'public';                  // Make all members public
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
                  $type= 'void';
                } else {
                  $type= $this->names->forType($return[0]->type);
                }
              }

              // Calculate modifier, defaulting to "public"
              $access= $method->tags('access');
              $modifiers= $access ? $access[0]->text : 'public';
              
              foreach ($method->tags('model') as $tag) {
                $modifiers.= ' '.$tag->text;
                $tag->text == 'abstract' && $abstract= TRUE;
              }

              $t[1]= $modifiers.' '.$type.' '.$t[1];
              break 2;
            }

            $method= NULL;
            return throw(new IllegalStateException('Cannot find method '.$t[1].'()'));
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

          case ST_FUNCTION_ARGS.'&':
            $t= '';
            break;

          case ST_FUNCTION_ARGS.T_VARIABLE:
            $type= ($arguments[$offset]->type
              ? $this->names->forType($arguments[$offset]->type, TRUE).' '
              : ''
            );
            $t[1]= $type.$t[1];
            $offset++;
            break;

          case ST_FUNCTION_ARGS.')':
            $brackets--;
            if (0 == $brackets) {
              array_shift($states);
            }
            break;

          case ST_FUNCTION.'{':
            if ($skip= $this->names->current->isInterface() || $abstract) $out= rtrim($out, ' ').';';

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

          case ST_FUNCTION_BODY.'{':
            $brackets++;
            break;

          case ST_FUNCTION_BODY.T_IS_A:
            array_unshift($states, ST_INSTANCE_OF);
            $instanceof= array('op' => 'expression', 'brackets' => 0);
            $skip= TRUE;
            break;

          case ST_INSTANCE_OF.'(':
            $instanceof['brackets']++;
            $instanceof[$instanceof['op']].= '(';
            break;

          case ST_INSTANCE_OF.')':
            $instanceof['brackets']--;
            $instanceof[$instanceof['op']].= ')';
            if (0 == $instanceof['brackets']) {
              $skip= FALSE;
              $t= $instanceof['expression'].' instanceof '.$instanceof['class'];
              array_shift($states);
            }
            break;

          case ST_INSTANCE_OF.',':
            $instanceof['op']= 'class';
            break;

          case ST_INSTANCE_OF.T_VARIABLE:
          case ST_INSTANCE_OF.T_STRING:
            $instanceof[$instanceof['op']].= $t[1];
            break;

          case ST_INSTANCE_OF.T_CONSTANT_ENCAPSED_STRING:
            $instanceof[$instanceof['op']]= $this->names->packagedNameOf($this->names->qualifiedNameOf(trim($tokens[$i][1], '"\'')));
            break;

          case ST_FUNCTION_BODY.'&':
            if (T_WHITESPACE != $tokens[$i+ 1][0]) {  // Kill reference operator
              $t= '';
            }
            break;

          case ST_FUNCTION_BODY.'}':
            $brackets--;
            if (0 == $brackets) {

              // Shift off both function *and* function body
              array_shift($states);
              array_shift($states);
              $skip= FALSE;
              if ($this->names->current->isInterface() || $abstract) $t= '';
            }
            break;

          case ST_INITIAL.T_TRY:
          case ST_FUNCTION_BODY.T_TRY:
            $skip= TRUE;
            array_unshift($states, ST_LOOKING_FOR_TRY_BRACKET);
            break;

          case ST_LOOKING_FOR_TRY_BRACKET.'{';
            $skip= FALSE;
            $t= 'try {';
            $brackets++;
            array_shift($states);
            break;

          case ST_FUNCTION_BODY.T_RETURN:
            if ('throw' == $tokens[$i+ 2][1]) {           // Look ahead
              $skip= TRUE;
              array_unshift($states, ST_LOOKING_FOR_THROW);
            }
            break;

          case ST_LOOKING_FOR_THROW.T_THROW:
            $skip= FALSE;
            array_shift($states);
            break;

          case ST_CLASS.T_EXTENDS:
            $skip= $this->names->current->isInterface();
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

          case ST_INITIAL.T_IF:
          case ST_FUNCTION_BODY.T_IF:
            if ('catch' == $tokens[$i+ 3][1]) {           // Look ahead
              $skip= TRUE;
              array_unshift($states, ST_LOOKING_FOR_CATCH);
            }
            break;

          case ST_LOOKING_FOR_CATCH.T_CATCH:
            $t[1]= sprintf(                               // Reassemble and advance
              'catch (%s %s)',
              $this->names->packagedNameOf($this->names->qualifiedNameOf(trim($tokens[$i+ 2][1], '\'"'))),
              $tokens[$i+ 5][1]
            );
            $i+= 7;
            $skip= FALSE;
            array_shift($states);
            break;

          case ST_LOOKING_FOR_CLASS.T_STRING:
            $t[1]= $this->names->packagedNameOf($this->names->qualifiedNameOf($t[1]));
            array_shift($states);
            break;

          case ST_LOOKING_FOR_CLASS.T_VARIABLE:
            array_shift($states);
            break;

          case ST_INITIAL.T_IMPLEMENTS:
            $skip= TRUE;
            array_unshift($states, ST_LOOKING_FOR_INTERFACES_END);
            break;

          case ST_LOOKING_FOR_INTERFACES_END.';';
            $skip= FALSE;
            array_shift($states);
            $t= '';
            break;

          case ST_FUNCTION_BODY.'.':
            $t= '~';
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
