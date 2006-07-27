<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('text.doclet.Doclet');
  
  $help= <<<__
Subjective: Migrate PHP4 classes and scripts using XP to PHP

* Replace try(); with try
* Replace if (catch('EXCEPTION_CLASS_NAME', \$e)) with 
  catch (EXCEPTION_CLASS_NAME \$e)
* Add correct namespace to new CLASS_NAME
* Add correct namespace to extends CLASS_NAME
* Add correct namespace to static CLASS_NAME::METHOD_NAME calls
* Add package statement around classes
* Rewrite implements(__FILE__, 'IMPLEMENTED_INTERFACE') to 
  implements IMPLEMENTED_INTERFACE
* Replace return throw() with throw()
* Replace class INTERFFACE_NAME extends Interface with
  interface INTERFFACE_NAME
* Remove method body from interface methods

Usage:
php migrate.php <<fully_qualified_class_name>>
__;

  define('ST_LOOKING_FOR_CLASS',          'looking:class');
  define('ST_LOOKING_FOR_TRY_BRACKET',    'looking:trybracket');
  define('ST_LOOKING_FOR_THROW',          'looking:throw');
  define('ST_LOOKING_FOR_INTERFACES_END', 'looking:iface_end');
  define('ST_LOOKING_FOR_CATCH',          'looking:catch');
  define('ST_LOOKING_FOR_IS_A_END',       'looking:isa_end');
  define('ST_LOOKING_FOR_IS_END',         'looking:is_end');

  define('T_TRY',                0x2000);
  define('T_CATCH',              0x2001);
  define('T_THROW',              0x2002);
  
  // {{{ MigrationDoclet
  //     Migrates classes
  class MigrationDoclet extends Doclet {
    var
      $mapping = array(),
      $current = NULL;

    function buildMapping(&$doc) {
      $key= strtolower($doc->name());
      if (isset($this->mapping[$key])) return;
      
      $this->mapping[$key]= $doc->qualifiedName();
      
      // Build mapping for superclass if existant
      $doc->superclass && $this->buildMapping($doc->superclass);
      
      // Build mapping for used classes
      while ($doc->usedClasses->hasNext()) {
        $this->buildMapping($doc->usedClasses->next());
      }

      // Build mapping for interfaces
      while ($doc->interfaces->hasNext()) {
        $this->buildMapping($doc->interfaces->next());
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
    
    function mappedName($name) {
      switch (strtolower($name)) {
        case 'exception': return 'XPException';
        case 'iterator':  return 'XPIterator';
        case 'util.iterator': return 'util.XPIterator';
        case 'lang.object': return 'lang.Generic';
        case 'object': return 'Generic';
        default: return $name;
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
        $this->current= &$root->classes->next();
        $debug && Console::writeLine('---> Processing ', $this->current->qualifiedName());
        
        // Build mapping short names => long names
        $this->buildMapping($this->current);
        
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
                $usesList[trim($tokens[$j][1], '\'"')]= TRUE;
              }
              
              foreach ($this->current->interfaces->classes as $iface => $dummy) {
                $usesList[$iface]= TRUE;
              }
              
              $out.= $this->printUses(array_keys($usesList), $multiline);
              $printedUses= TRUE;
              
              $i= $j+ 1;
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

            case ST_INITIAL.T_FUNCTION:
              array_unshift($states, ST_FUNCTION);
              break;
            
            case ST_FUNCTION_BODY.'&':
              // Convert &new to new
              if (T_NEW == $tokens[$i+ 1][0]) $t= '';
              break;
            
            case ST_FUNCTION.'{':
              if ($skip= $this->current->isInterface()) $out= rtrim($out, ' ').';';
              array_unshift($states, ST_FUNCTION_BODY);
              $brackets= 1;
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
              $skip= $this->current->isInterface();
              array_unshift($states, ST_LOOKING_FOR_CLASS);
              break;
  
            case ST_INITIAL.T_NEW:
            case ST_FUNCTION_BODY.T_NEW:
              array_unshift($states, ST_LOOKING_FOR_CLASS);
              break;
            
            case ST_INITIAL.T_STRING:
            case ST_FUNCTION_BODY.T_STRING:
            
              // Replace is_a() with is() calls
              if ('is_a' === $t[1]) {
                array_unshift($states, ST_LOOKING_FOR_IS_A_END);
                $t= '';
                break;
              }
              
              // Convert is('lang.Object', $o) into the respective lang.Generic
              if ('is' === $t[1]) {
                array_unshift($states, ST_LOOKING_FOR_IS_END);
                break;
              }
              
              /*if (T_DOUBLE_COLON == $tokens[$i+ 1][0]) {    // Look ahead
                $t[1]= $this->packagedNameOf($t[1]);
              }*/
              break;
            
            case ST_LOOKING_FOR_IS_END.T_CONSTANT_ENCAPSED_STRING:
              if ("'" == $t[1]{0} || '"' == $t[1]{0}) {
                $t[1]= "'".$this->mappedName(trim($t[1], '\'"'))."'";
              }
              break;
            
            case ST_LOOKING_FOR_IS_END.')':
              array_shift($states);
              break;
            
            case ST_LOOKING_FOR_IS_A_END.T_VARIABLE:
              $isAobjectToken= $t;
              $t= '';
              break;
            
            case ST_LOOKING_FOR_IS_A_END.T_WHITESPACE:
            case ST_LOOKING_FOR_IS_A_END.',':
            case ST_LOOKING_FOR_IS_A_END.'(':
              $t= '';
              break;
            
            case ST_LOOKING_FOR_IS_A_END.')';
              $out.= 'is(\''.$this->mappedName(trim($isAclassToken[1], '\'"')).'\', '.$isAobjectToken[1].')';
              $t= '';
              array_shift($states); // Popp off ST_ISA_BODY
              break;

            case ST_LOOKING_FOR_IS_A_END.T_CONSTANT_ENCAPSED_STRING:
              $isAclassToken= $t;
              $t= '';
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
        
        Console::write($out);
      }
    }
    
    function validOptions() {
      return array('debug' => OPTION_ONLY);
    }
  }
  // }}}

  // {{{ main
  $p= &new ParamString();
  if ($p->exists('help', '?')) {
    Console::writeLine($help);
    exit(1);
  }

  RootDoc::start(new MigrationDoclet(), $p);
  // }}}
?>
