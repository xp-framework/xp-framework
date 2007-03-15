<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('text.doclet.Doclet');

  define('ST_LOOKING_FOR_CLASS',          'looking:class');
  define('ST_LOOKING_FOR_TRY_BRACKET',    'looking:trybracket');
  define('ST_LOOKING_FOR_THROW',          'looking:throw');
  define('ST_LOOKING_FOR_INTERFACES_END', 'looking:iface_end');
  define('ST_LOOKING_FOR_CATCH',          'looking:catch');
  define('ST_INSTANCE_OF',                'looking:is_a');
  
  define('T_TRY',                0x2000);
  define('T_CATCH',              0x2001);
  define('T_THROW',              0x2002);
  
  // {{{ MigrationDoclet
  //     Migrates classes
  class MigrationDoclet extends Doclet {
    var
      $mapping  = array(),
      $current  = NULL,
      $output   = '';

    function buildMapping(&$doc) {
      $key= strtolower($doc->name());
      if (isset($this->mapping[$key])) return;
      
      try(); {
        $this->mapping[$key]= $doc->qualifiedName();
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      // Build mapping for superclass if existant
      try(); {
        $doc->superclass && $this->buildMapping($doc->superclass);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      // Build mapping for used classes
      while ($doc->usedClasses->hasNext()) {
        try(); {
          $class= &$doc->usedClasses->next();
          $class && $this->buildMapping($class);
        } if (catch ('Exception', $e)) {
          return throw($e);
        }
      }

      // Build mapping for interfaces
      while ($doc->interfaces->hasNext()) {
        try(); {
          $this->buildMapping($doc->interfaces->next());
        } if (catch('Exception', $e)) {
          return throw($e);
        }
      }
    }

    function qualifiedNameOf($short) {
      $key= strtolower($short);
      if (!isset($this->mapping[$key])) return throw(new IllegalArgumentException(
        'Mapping for "'.$short.'" not found'
      ));
      
      return ($this->current->qualifiedName() == $this->mapping[$key] 
        ? 'self' 
        : $this->mapping[$key]
      );
    }
    
    function packagedNameOf($short) {
      return strtr($this->qualifiedNameOf($short), '.', '~');
    }
    
    function mappedName($name, $extended= FALSE) {
      switch (strtolower($name)) {
        case 'exception': return 'XPException';
        case 'iterator':  return 'XPIterator';
        case 'util.iterator': return 'util.XPIterator';
        case 'lang.object': return 'lang.Generic';
        default: {
          if ($extended) {
            switch (strtolower($name)) {
              case 'object': return 'Generic';
            }
          }
          return $name;
        }
      }
    }
    
    function printUses($usesList, $multiline= FALSE) {
      $uses= array();
      
      // Unique again and use mapped names
      foreach ($usesList as $u) {
        $uses[$this->mappedName($u)]= TRUE;
      }
      
      $uses= array_keys($uses);
      
      $out= '';
      if (sizeof($uses) >= 3) $multiline= TRUE;

      $out.= 'uses(';
      ($multiline ? $out.= "\n    '" : $out.= "'");
      $out.= implode(($multiline ? "',\n    '" : "', '"), $uses);
      ($multiline ? $out.= "'\n  );" : $out.= "');");
      return $out;
    }

    function start(&$root) {
      static $map= array(
        'uses'        => T_USES, 
        'implements'  => T_IMPLEMENTS, 
        'define'      => T_DEFINE,
        'try'         => T_TRY,
        'catch'       => T_CATCH,
        'throw'       => T_THROW
      );
      
      // Build mapping for built-in-classes
      foreach (xp::registry() as $key => $val) {
        if (0 != strncmp('class.', $key, 6)) continue;
        $this->mapping[xp::reflect($key)]= xp::registry($key);
      }
      
      $debug= $root->option('debug');
      
      // Hardcode some keywords
      $this->mapping['xp']= 'xp';
      $this->mapping['parent']= 'parent';

      while ($root->classes->hasNext()) {
        xp::gc();
        try(); {
          $this->current= &$root->classes->next();
          $debug && Console::writeLine('---> Processing ', $this->current->qualifiedName());
        } if (catch('Exception', $e)) {
          return throw($e);
        }

        try(); {
          // Build mapping short names => long names
          $this->buildMapping($this->current);
        } if (catch('Exception', $e)) {
          return throw($e);
        }
        
        // Tokenize file
        $tokens= token_get_all(file_get_contents($root->findClass($this->current->qualifiedName())));
        $out= '';
        $states= array(ST_INITIAL);
        $skip= FALSE;
        $brackets= 0;
        $printedUses= FALSE;
        
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
            case ST_INITIAL.T_COMMENT:
              if ('/**' == substr($t[1], 0, 3)) {
                if (!$printedUses && !empty($this->current->interfaces->classes)) {
                  $out.= $this->printUses(array_keys($this->current->interfaces->classes))."\n\n  ";
                }
              }
              
              break;
            case ST_INITIAL.T_USES:
              $usesList= array();
              $multiline= FALSE;
              
              for ($j= $i; $j < sizeof($tokens); $j++) {
                if (')' == $tokens[$j]) break;
                if (T_WHITESPACE == $tokens[$j][0] && FALSE !== strpos($tokens[$j][1], "\n")) $multiline= TRUE;
                if (T_CONSTANT_ENCAPSED_STRING != $tokens[$j][0]) continue;
                if (trim($tokens[$j][1], '\'"') == 'lang.Interface') continue;
                $usesList[trim($tokens[$j][1], '\'"')]= TRUE;
              }
              
              foreach ($this->current->interfaces->classes as $iface => $dummy) {
                $usesList[$iface]= TRUE;
              }
              
              if (sizeof($usesList) > 0) {
                $out.= $this->printUses(array_keys($usesList), $multiline);
                $i= $j+ 1;
              } else {
                $i= $j+ 2; // Skip uses and newline completely
              }
              $printedUses= TRUE;
              
              $t= '';
              break;
            
            case ST_INITIAL.T_CLASS:
              $qualified= strtr($this->current->qualifiedName(), '.', '~');
              /*$out.= (
                "package ".
                substr($qualified, 0, strrpos($qualified, '~')).
                ' { '
              );*/
              array_unshift($states, ST_CLASS);
              array_unshift($states, ST_LOOKING_FOR_CLASS); // Search classname and perform mapping replacement
              $this->current->isInterface() && $t[1]= 'interface';
              break;
            
            case ST_CLASS.'{':
              empty($this->current->interfaces->classes) || $out.= 'implements '.implode(
                ', ', array_map(create_function('$a', 'return MigrationDoclet::mappedName(substr($a, strrpos($a, ".")+ 1));'), array_keys($this->current->interfaces->classes))
              ).' ';
              $skip= FALSE;
              array_unshift($states, ST_CLASS_BODY);
              $brackets[ST_CLASS_BODY]= 1;
              break;
            
            case ST_CLASS_BODY.'}':
              // $t.= '}'; // One extra for package statement
              array_shift($states);
              break;

            case ST_CLASS_BODY.T_VAR:
              $t[1]= 'public';                  // Make all members public
              break;
            
            case ST_CLASS_BODY.T_COMMENT:
              $t[1]= preg_replace(array(
                  '/ +\* @access[^\r\n]+[\r\n]/', '/ +\* @model[^\r\n]+[\r\n]/',
                ), array(
                  '', '',
                ), $t[1]);
              $t[1]= str_replace('&', '', $t[1]);
              break;

            case ST_CLASS_BODY.T_FUNCTION:
              $function= NULL;
              
              // Find functionname
              for ($j= $i; $j < sizeof($tokens); $j++) {
                if ('(' == $tokens[$j]) break;
                if (T_STRING == $tokens[$j][0]) { $function= $tokens[$j][1]; break; }
              }
              
              $static= FALSE;
              if ($function) {
                foreach ($this->current->methods as $m) {
                  if ($m->name == $function) {
                  
                    // There may only be one "model" tag
                    $tag= array_shift($m->tags('model'));
                    $static= $tag && $tag->text() == 'static';
                    break;
                  }
                }
              }
            
              $t[1]= 'public '.($static ? 'static ' : '').$t[1];           // Make all methods public
              array_unshift($states, ST_FUNCTION);
              break;
          
            case ST_FUNCTION.'&':
              $t= '';
              break;

            case ST_INITIAL.T_FUNCTION:
              array_unshift($states, ST_FUNCTION);
              break;
            
            case ST_FUNCTION_BODY.'&':
              // Convert &new to new
              if ($tokens[$i+ 1][0] != T_WHITESPACE) $t= '';
              break;
            
            case ST_FUNCTION.'{':
              if ($skip= $this->current->isInterface()) $out= rtrim($out, ' ').';';
              array_unshift($states, ST_FUNCTION_BODY);
              $brackets= 1;
              break;

            case ST_FUNCTION_BODY.'{':
              $brackets++;
              break;
              
            case ST_FUNCTION_BODY.T_STRING && 'is_a' == $t[1]:
              array_unshift($states, ST_INSTANCE_OF);
              $instanceof= array('op' => 'expression', 'brackets' => 0);
              $skip= TRUE;
              break;

            case ST_INSTANCE_OF.'[':
            case ST_INSTANCE_OF.']':
            case ST_INSTANCE_OF.'=':
              $instanceof[$instanceof['op']].= $t;
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
                $t= 'is('.trim($instanceof['class'], ')').', '.ltrim($instanceof['expression'], '(').')';
                // $t= trim($instanceof['expression']).' instanceof '.trim($instanceof['class']);
                array_shift($states);
              }
              break;

            case ST_INSTANCE_OF.',':
              $instanceof['op']= 'class';
              break;

            case ST_INSTANCE_OF.T_CONSTANT_ENCAPSED_STRING:
              $hasQuote= ($tokens[$i][1][0] == '"' || $tokens[$i][1][0] == "'");
              $instanceof[$instanceof['op']]= 
                ($hasQuote ? "'" : '').
                $this->mappedName(trim($tokens[$i][1], '"\''), TRUE).
                ($hasQuote ? "'" : '');
              break;

            /* case ST_FUNCTION_BODY.'&':
              if (T_WHITESPACE != $tokens[$i+ 1][0]) {  // Kill reference operator
                $t= '';
              }
              break;*/

            // ST_INSTANCE_OF.* (catch-all)
            case $states[0] == ST_INSTANCE_OF && is_array($t):
              $instanceof[$instanceof['op']].= $t[1];
              break;

            case ST_FUNCTION_BODY.'}':
              $brackets--;
              if (0 == $brackets) {

                // Shift off both function *and* function body
                array_shift($states);
                array_shift($states);
                $skip= FALSE;
                $this->current->isInterface() && $t= '';
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
              $skip= ($this->current->isInterface() && $this->current->superclass && $this->current->superclass->name() == 'Interface');
              array_unshift($states, ST_LOOKING_FOR_CLASS);
              break;
  
            case ST_INITIAL.T_NEW:
            case ST_FUNCTION_BODY.T_NEW:
              array_unshift($states, ST_LOOKING_FOR_CLASS);
              break;
            
            case ST_INITIAL.T_STRING:
            case ST_FUNCTION_BODY.T_STRING && 'is' == $t[1]:
              
              if (T_CONSTANT_ENCAPSED_STRING == $tokens[$i+ 2][0]) {
                // Convert is('lang.Object', $o) into the respective lang.Generic
                $class= trim($tokens[$i+ 2][1], '\'"');
                $tokens[$i+ 2][1]= "'".$this->mappedName($class, TRUE)."'";
              }
              break;
              
              /*if (T_DOUBLE_COLON == $tokens[$i+ 1][0]) {    // Look ahead
                $t[1]= $this->packagedNameOf($t[1]);
              }*/
              break;

            case ST_INITIAL.T_IF:
            case ST_FUNCTION_BODY.T_IF:
              if ('catch' == $tokens[$i+ 3][1]) {           // Look ahead
                $skip= TRUE;
                array_unshift($states, ST_LOOKING_FOR_CATCH);
              }
              break;

            case ST_LOOKING_FOR_CATCH.T_CATCH:
              // Chech for correct coding-standards
              if (T_CONSTANT_ENCAPSED_STRING != $tokens[$i+ 2][0]) return throw(new IllegalStateException(
                'Illegal syntax at "if (catch (" (be sure to omit whitespace after catch'
              ));
              
              $t[1]= sprintf(                               // Reassemble and advance
                'catch (%s %s)',
                //$this->packagedNameOf(trim($tokens[$i+ 2][1], '\'"')),
                trim($tokens[$i+ 2][1], '\'"'),
                $tokens[$i+ 5][1]
              );
              $i+= 7;
              $skip= FALSE;
              array_shift($states);
              break;

            case ST_LOOKING_FOR_CLASS.T_STRING:
              /*$t[1]= $this->packagedNameOf($t[1]);*/
              $t[1]= $this->mappedName($t[1]);
              array_shift($states);
              break;

            case ST_LOOKING_FOR_CLASS.T_VARIABLE:
              array_shift($states);
              break;

            case ST_CLASS.T_IMPLEMENTS:
              $skip= TRUE;
              
              $interfaces= array();
              for ($j= $i; $j < sizeof($tokens); $j++) {
                if ($tokens[$j] == ')') break;
                if ($tokens[$j][0] != T_CONSTANT_ENCAPSED_STRING) continue;
                $interfaces[]= trim($tokens[$j][1], '\'"');
              }
              
              array_unshift($states, ST_LOOKING_FOR_INTERFACES_END);
              break;
            
            case ST_LOOKING_FOR_INTERFACES_END.';':
              $skip= FALSE;
              array_shift($states);
              $t= '';
              break;
          }

          $skip || $out.= is_array($t) ? $t[1] : $t;
        }
        
        $this->output.= $out;
      }
    }
    
    function validOptions() {
      return array('debug' => OPTION_ONLY);
    }
    
    function getOutput() {
      return $this->output;
    }
  }
  // }}}
?>
