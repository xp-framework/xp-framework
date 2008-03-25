<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'util.cmd.ParamString',
    'text.doclet.ClassIterator',
    'text.doclet.ClassDoc',
    'text.doclet.FieldDoc',
    'text.doclet.PackageDoc',
    'text.doclet.MethodDoc'
  );
  
  define('OPTION_ONLY', 0x0000);
  define('HAS_VALUE',   0x0001);

  define('ST_INITIAL',            'initial');
  define('ST_CLASS_BODY',         'classbody');
  define('ST_USES',               'uses');
  define('ST_IMPLEMENTS',         'implements');
  define('ST_CLASS',              'class');
  define('ST_CLASS_VAR',          'classvar');
  define('ST_VARIABLE_VALUE',     'variablevalue');
  define('ST_FUNCTION',           'function');
  define('ST_FUNCTION_ARGUMENTS', 'functionarguments');
  define('ST_ARGUMENT_VALUE',     'argumentvalue');
  define('ST_FUNCTION_BODY',      'functionbody');
  define('ST_DEFINE',             'define');
  define('ST_DEFINE_VALUE',       'definevalue');
  
  define('T_USES',                0x1000);
  define('T_PACKAGE',             0x1001);
  define('T_DEFINE',              0x1002);
  
  /**
   * Represents the root of the program structure information for one 
   * run. From this root all other program structure information can 
   * be extracted. Also represents the command line information - 
   * the classes and options specified by the user. 
   *
   * Example:
   * <code>
   *   class TreeDoclet extends Doclet {
   *     // ...
   *   }
   *
   *   RootDoc::start(new TreeDoclet(), new ParamString());
   * </code>
   *
   * @see      xp://text.doclet.Doclet
   * @purpose  Entry point
   */
  class RootDoc extends Object {
    public
      $classes = NULL,
      $options = array();
    
    /**
     * Start a doclet
     *
     * @param   text.doclet.Doclet doclet
     * @param   util.cmd.ParamString params
     * @return  bool
     * @throws  lang.XPException in case doclet setup fails
     */
    public static function start($doclet, $params) {
      $classes= array();
      $root= new self();
      
      // Separate options from classes
      $valid= $doclet->validOptions();
      for ($i= 1; $i < $params->count; $i++) {
        $option= $params->list[$i];
        
        if (0 == strncmp($option, '--', 2)) {        // Long: --foo / --foo=bar
          $p= strpos($option, '=');
          $name= substr($option, 2, FALSE === $p ? strlen($option) : $p- 2);
          if (isset($valid[$name])) {
            if ($valid[$name] == HAS_VALUE) {
              $root->options[$name]= FALSE === $p ? NULL : substr($option, $p+ 1);
            } else {
              $root->options[$name]= TRUE;
            }
          }
        } else if (0 == strncmp($option, '-', 1)) {   // Short: -f / -f bar
          $name= substr($option, 1);
          if (isset($valid[$name])) {
            if ($valid[$name] == HAS_VALUE) {
              $root->options[$name]= $params->list[++$i];
            } else {
              $root->options[$name]= TRUE;
            }
          }          
        } else {
          $classes[]= $option;
        }
      }
      
      // Set up class iterator
      $root->classes= $doclet->iteratorFor($root, $classes);

      // Start the doclet
      return $doclet->start($root);
    }
    
    /**
     * Returns an option by a given name or the specified default value
     * if the option does not exist.
     *
     * @param   string name
     * @param   string default default NULL
     * @return  string
     */
    public function option($name, $default= NULL) {
      return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * Finds a package info file by a given class name
     *
     * @param   string package
     * @return  string filename
     */
    public function findPackage($package) {
      $filename= str_replace('.', DIRECTORY_SEPARATOR, $package).DIRECTORY_SEPARATOR.'package-info.xp';
      foreach (xp::registry('classpath') as $dir) {
        if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
        return $dir.DIRECTORY_SEPARATOR.$filename;
      }
      return NULL;
    }
    
    /**
     * Finds a class by a given class name
     *
     * @param   string classname
     * @return  string filename
     */
    public function findClass($classname) {
      $filename= str_replace('.', DIRECTORY_SEPARATOR, $classname).'.class.php';
      foreach (xp::registry('classpath') as $dir) {
        if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
        return $dir.DIRECTORY_SEPARATOR.$filename;
      }
      return NULL;
    }

    /**
     * Qualifies a class name by looking at known or used classes.
     *
     * @param   text.doclet.Doc doc
     * @param   string name
     * @return  string qualified name
     */
    public function qualifyName($doc, $name) {
      if (!($lookup= xp::registry('class.'.$name))) {
        foreach (array_keys($doc->usedClasses->classes) as $class) {
          if (xp::reflect($class) == $name) return $class;
        }
      }

      // Nothing found!
      if (!$lookup && !$lookup= xp::nameOf($name)) throw(new IllegalStateException(sprintf(
        'Could not find class %s',
        xp::stringOf($name)
      )));
      
      return $lookup;
    }

    /**
     * Parses a package descroption file and returns a packagedoc element
     *
     * @param   
     * @return  
     */
    public function packageNamed($package) {
      static $cache= array();
      static $map= array('package' => T_PACKAGE);

      if (isset($cache[$package])) return $cache[$package];

      with ($doc= new PackageDoc($package), $doc->setRoot($this)); {

        // Find package-info file. If we cannot find one, ignore it!
        if ($filename= $this->findPackage($package)) {

          // Tokenize contents
          if (!($c= file_get_contents($filename))) {
            throw new IllegalArgumentException('Could not parse "'.$filename.'"');
          }

          $tokens= token_get_all('<?php '.$c.' ?>');
          $annotations= $comment= NULL;
          $name= '';
          $state= ST_INITIAL;          
          for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
            $t= $tokens[$i];
            if (is_array($t) && isset($map[$t[1]])) $t[0]= $map[$t[1]];

            switch ($state.$t[0]) {
              case ST_INITIAL.T_DOC_COMMENT:
                $comment= $t[1];
                break;
            
              case ST_INITIAL.T_PACKAGE:
                $state= ST_CLASS;
                break;
              
              case ST_CLASS.T_STRING:
                $name.= $t[1];
                break;

              case ST_CLASS.'.':    // Package separator
                $name.= '.';
                break;
              
              case ST_CLASS.'{':
                if ($name !== $package) {
                  throw new IllegalArgumentException('Package "'.$package.'" contains package "'.$name.'"');
                }
                $doc->name= $name;
                $doc->rawComment= $comment;
                $doc->annotations= $annotations;
                $comment= $annotations= NULL;
                $name= '';
                $state= ST_CLASS_BODY;
                break;
              
              case ST_CLASS_BODY.'}':
                $state= ST_INITIAL;
                break;
            }
          }
        }
      }      
      return $cache[$package]= $doc;
    }
    
    /**
     * Parses a class file and returns a classdoc element
     *
     * @param   string classname fully qualified class name
     * @return  text.doclet.ClassDoc
     * @throws  lang.IllegalArgumentException if class could not be found or parsed
     */
    public function classNamed($classname) {
      static $cache= array();
      static $map= array('uses' => T_USES, 'define' => T_DEFINE);

      // Check cache
      if (isset($cache[$classname])) return $cache[$classname];
      
      // Check for php namespace - in this case, we have a builtin class. These
      // classes will not be documented for the moment.
      if ('php.' == substr($classname, 0, 4)) return NULL;

      // Find class
      if (!($filename= $this->findClass($classname))) {
        throw new IllegalArgumentException('Could not find '.xp::stringOf($classname));
      }
      
      // Tokenize contents
      if (!($tokens= token_get_all(file_get_contents($filename)))) {
        throw new IllegalArgumentException('Could not parse "'.$filename.'"');
      }

      with ($doc= new ClassDoc(), $doc->setRoot($this)); {
        $annotations= $comment= $package= NULL;
        $modifiers= array();
        $state= ST_INITIAL;          
        for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
          $t= $tokens[$i];
          if (is_array($t) && isset($map[$t[1]])) $t[0]= $map[$t[1]];

          // if (!is_array($t)) {
          //   printf("[ %-20s::%-30s ] %s\n", $state, 'T_NONE', rtrim($t));
          // } else {
          //   printf("[ %-20s::%-30s ] %s\n", $state, token_name($t[0]), rtrim($t[1]));
          // }

          switch ($state.$t[0]) {
            case ST_INITIAL.T_DOC_COMMENT:
            case ST_CLASS_BODY.T_DOC_COMMENT:
              $comment= $t[1];
              break;

            case ST_INITIAL.T_COMMENT:
            case ST_CLASS_BODY.T_COMMENT:
              if (strncmp('#[@', $t[1], 3) == 0) {
                $annotations= substr($t[1], 2);
              } else if (strncmp('#', $t[1], 1) == 0) {
                $annotations.= substr($t[1], 1);
              }

              if (']' == substr(rtrim($t[1]), -1)) {
                $annotations= '['.trim($annotations);
              }
              break;
              
            case ST_INITIAL.T_VARIABLE:
              if ('$package' === $t[1]) {   // RFC #0037: $package= 'lang.reflect';
                while (T_CONSTANT_ENCAPSED_STRING !== $tokens[$i][0] && $i < $s) $i++;
                $package= $tokens[$i][1];
              }
              break;

            case ST_INITIAL.T_USES:
              $state= ST_USES;
              break;

            case ST_USES.T_CONSTANT_ENCAPSED_STRING:
              $cn= trim($t[1], '"\'');
              if (!$this->findClass($cn)) throw new IllegalStateException(
                'Could not find used class "'.$cn.'" for class '.$classname
              );
              $doc->usedClasses->classes[$cn]= NULL;
              break;

            case ST_USES.')':
              $state= ST_INITIAL;
              break;

            case ST_INITIAL.T_DEFINE:
              $state= ST_DEFINE;
              break;

            case ST_DEFINE.T_CONSTANT_ENCAPSED_STRING:
              $state= ST_DEFINE_VALUE;
              $define= trim($t[1], '"\'');
              break;

            case ST_DEFINE_VALUE.T_CONSTANT_ENCAPSED_STRING:
            case ST_DEFINE_VALUE.T_LNUMBER:
            case ST_DEFINE_VALUE.T_DNUMBER:
            case ST_DEFINE_VALUE.T_STRING:
              $doc->constants[$define]= $t[1];
              break;

            case ST_DEFINE_VALUE.')':
              $state= ST_INITIAL;
              break;

            case ST_INITIAL.T_INTERFACE:
              $doc->type= INTERFACE_CLASS;
              // Fall-through intended

            case ST_INITIAL.T_CLASS:
              while (T_STRING !== $tokens[$i][0] && $i < $s) $i++;

              $doc->name= $package ? substr($tokens[$i][1], strlen($package)- 1) : $tokens[$i][1];
              $doc->qualifiedName= $classname;
              $doc->rawComment= $comment;
              $doc->annotations= $annotations;
              $doc->modifiers= $modifiers;
              $comment= $annotations= NULL;
              $modifiers= array();
              $state= ST_CLASS;
              break;

            case ST_CLASS.T_EXTENDS:
              while (T_STRING !== $tokens[$i][0] && $i < $s) $i++;

              $doc->superclass= $this->classNamed($this->qualifyName($doc, $tokens[$i][1]));
              break;

            case ST_CLASS.T_IMPLEMENTS:
              $state= ST_IMPLEMENTS;
              break;
            
            case ST_IMPLEMENTS.T_STRING:
              $doc->interfaces->classes[$this->qualifyName($doc, $t[1])]= TRUE;
              break;

            case ST_CLASS.'{':
            case ST_IMPLEMENTS.'{':
              $state= ST_CLASS_BODY;
              break;

            case ST_CLASS_BODY.T_VARIABLE;
              $state= ST_CLASS_VAR;
              // Fall-through intended

            case ST_CLASS_VAR.T_VARIABLE;
              unset($field);
              $field= new FieldDoc();
              $field->name= $t[1];
              $field->modifiers= $modifiers;
              break;

            case ST_CLASS_VAR.'=':
              $state= ST_VARIABLE_VALUE;
              break;

            case ST_CLASS_VAR.',':
              $doc->fields[]= $field;
              break;

            case ST_CLASS_VAR.';':
              $doc->fields[]= $field;
              $state= ST_CLASS_BODY;
              $modifiers= array();
              break;

            case ST_VARIABLE_VALUE.T_CONSTANT_ENCAPSED_STRING:
            case ST_VARIABLE_VALUE.T_LNUMBER:
            case ST_VARIABLE_VALUE.T_DNUMBER:
            case ST_VARIABLE_VALUE.T_STRING:
              $field->constantValue= $t[1];
              $state= ST_CLASS_VAR;
              break;

            case ST_VARIABLE_VALUE.T_ARRAY:
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

              $field->constantValue= $src;
              $state= ST_CLASS_VAR;
              break;
           
            // Before member declaration (e.g. public static $..., protected function ...)
            case ST_CLASS_BODY.T_PUBLIC:
            case ST_CLASS_BODY.T_PRIVATE:
            case ST_CLASS_BODY.T_PROTECTED:
            case ST_CLASS_BODY.T_STATIC:
            case ST_CLASS_BODY.T_FINAL:
            case ST_CLASS_BODY.T_ABSTRACT:
            
            // Before class declaration (e.g. abstract class ...)
            case ST_INITIAL.T_FINAL:
            case ST_INITIAL.T_ABSTRACT:
              $modifiers[$t[1]]= TRUE;
              break;
            
            case ST_CLASS_BODY.T_FUNCTION:
              while (T_STRING !== $tokens[$i][0] && $i < $s) $i++;

              with ($method= new MethodDoc(), $method->setRoot($this)); {
                $method->name= $tokens[$i][1];
                $method->rawComment= $comment;
                $method->annotations= $annotations;
                $method->modifiers= $modifiers;
                
                // Omit static initializer, it's not a real function
                if ('__static' != $method->name) $doc->methods[]= $method;
              }
              $comment= $annotations= NULL;
              $modifiers= array();
              $state= ST_FUNCTION;
              break;

            case ST_FUNCTION.'(':
              $state= ST_FUNCTION_ARGUMENTS;
              $argument= NULL;
              break;

            case ST_FUNCTION_ARGUMENTS.T_VARIABLE:
              $argument= $t[1];
              break;

            case ST_FUNCTION_ARGUMENTS.',':
              $method->arguments[$argument]= NULL;
              break;

            case ST_FUNCTION_ARGUMENTS.'=':
              $state= ST_ARGUMENT_VALUE;
              break;

            case ST_ARGUMENT_VALUE.T_CONSTANT_ENCAPSED_STRING:
            case ST_ARGUMENT_VALUE.T_LNUMBER:
            case ST_ARGUMENT_VALUE.T_DNUMBER:
            case ST_ARGUMENT_VALUE.T_STRING:
              $method->arguments[$argument]= $t[1];
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

              $method->arguments[$argument]= $src;
              break;

            case ST_ARGUMENT_VALUE.',':
              $state= ST_FUNCTION_ARGUMENTS;
              break;

            case ST_ARGUMENT_VALUE.')':
              $state= ST_FUNCTION;
              break;

            case ST_FUNCTION_ARGUMENTS.')':
              $argument && $method->arguments[$argument]= NULL;
              $state= ST_FUNCTION;
              break;

            case ST_FUNCTION.';':   // Interface and abstract methods have no body
              $state= ST_CLASS_BODY;
              break;        

            case ST_FUNCTION.'{':       
              $brackets= 0;
              do {
                $c= $tokens[$i][0];
                if ('{' == $c) {
                  $brackets++; 
                } else { 
                  if ('}' == $c and --$brackets <= 0) break;
                }
              } while (++$i < $s);

              $state= ST_CLASS_BODY;
              break;        

            case ST_CLASS_BODY.'}':
              $state= ST_INITIAL;
              break;
          }
        }
      }
      
      return $cache[$classname]= $doc;
    }
  }
?>
