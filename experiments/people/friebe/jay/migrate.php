<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('text.doclet.Doclet', 'io.File', 'io.Folder', 'io.FileUtil', 'net.xp_framework.tools.vm.util.NameMapping');
  
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
  define('ST_FUNCTION_ARGS',              'function-args');
  define('ST_USES_LIST',                  'uses-list');
  define('ST_INSTANCE_OF',                'instance-of');
  
  define('T_TRY',                0x2000);
  define('T_CATCH',              0x2001);
  define('T_THROW',              0x2002);
  define('T_IS_A',               0x2003);
  
  define('NS_SEPARATOR',         '.');
  
  // {{{ MigrationNameMapping
  //     Same as NameMapping, but
  class MigrationNameMapping extends NameMapping {

    function getMapping($key) {
      try(); {
        $m= parent::getMapping($key);
      } if (catch('IllegalArgumentException', $e)) {
        // DEBUG $e->printStackTrace();
        Console::writeLine('*** ', $e->getMessage());
        return $key;
      }
      
      return $m;
    }
  }
  
  // {{{ MigrationDoclet
  //     Migrates classes
  class MigrationDoclet extends Doclet {
    var
      $mapping = NULL,
      $current = NULL;

    function buildMapping(&$doc) {
      $key= strtolower($doc->name());
      if (isset($this->mapping[$key])) return;
      
      $this->names->addMapping($key, $doc->qualifiedName());
      
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

    function start(&$root) {
      static $map= array(
        'uses'        => T_USES, 
        'implements'  => T_IMPLEMENTS, 
        'define'      => T_DEFINE,
        'try'         => T_TRY,
        'catch'       => T_CATCH,
        'throw'       => T_THROW,
        'is_a'        => T_IS_A
      );

      $debug= $root->option('debug');
      $this->names= &new MigrationNameMapping();
      $this->names->setNamespaceSeparator(NS_SEPARATOR);
      
      // Build mapping for built-in-classes
      Console::writeLine('===> Starting');
      foreach (xp::registry() as $key => $val) {
        if (0 != strncmp('class.', $key, 6)) continue;
        $this->names->addMapping(xp::reflect($key), trim(xp::registry($key), '<>'));
      }

      if ($output= $root->option('output')) {
        Console::writeLine('---> Writing to ', $output);
        $base= &new Folder($output);
      }
      
      while ($root->classes->hasNext()) {
        $this->current= &$root->classes->next();
        $debug && Console::writeLine('---> Processing ', $this->current->qualifiedName());
        
        // Build mapping short names => long names
        $this->buildMapping($this->current);
        $this->names->setCurrentClass($this->current->qualifiedName());

        // Compile list of classes to be added to uses()
        $used= array();
        $this->current->usedClasses->rewind();
        while ($this->current->usedClasses->hasNext()) {
          $class= $this->current->usedClasses->next();
          $used[]= strtr($this->names->packagedNameOf($class->qualifiedName()), NS_SEPARATOR, '.');
        }
        $this->current->interfaces->rewind();
        while ($this->current->interfaces->hasNext()) {
          $interface= $this->current->interfaces->next();
          $used[]= strtr($this->names->packagedNameOf($interface->qualifiedName()), NS_SEPARATOR, '.');
        }
        
        // Tokenize file
        $tokens= token_get_all(file_get_contents($root->findClass($this->current->qualifiedName())));
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
                $qualified= $this->current->qualifiedName();
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
                $qualified= $this->current->qualifiedName();
                $package= substr($qualified, 0, strrpos($qualified, '.'));

                sort($used);
                $uses= '';
                foreach ($used as $classname) {
                  $uses.= 'import '.$classname.";\n";
                }
                $out= rtrim($out)."\n\n".$uses."\n\npackage ".$this->names->packagedNameOf($package)." {\n\n  ";
              }
              array_unshift($states, ST_CLASS);
              $this->current->isInterface() && $t[1]= 'interface';
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
              foreach ($this->current->methods as $method) {
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
                if ($model= $method->tags('model')) {
                  $modifiers.= ' '.$model[0]->text;
                }
                
                $t[1]= $modifiers.' '.$type.' '.$t[1];
                break 2;
              }
              $method= NULL;
              break;
            
            case ST_FUNCTION.'(':
              array_unshift($states, ST_FUNCTION_ARGS);
              $brackets= 1;
              $offset= 0;
              $arguments= $method->tags('param');
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
              if ($skip= $this->current->isInterface()) $out= rtrim($out, ' ').';';
              
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
              if (0 == $instanceof['op']) {
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
        
        if ($output) {
          try(); {
            $target= &new File($base->getURI().strtr($this->names->packagedNameOf($this->current->qualifiedName()), NS_SEPARATOR, DIRECTORY_SEPARATOR).'.xp');
            $f= &new Folder($target->getPath());
            $f->exists() || $f->create();
            FileUtil::setContents($target, $out);
          } if (catch('IOException', $e)) {
            return throw($e);
          }
          Console::writeLine('---> Wrote ', $target->getURI());
        } else {
          Console::write($out);
        }
      }
    }
    
    function validOptions() {
      return array(
        'debug'   => OPTION_ONLY,
        'output'  => HAS_VALUE
      );
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
