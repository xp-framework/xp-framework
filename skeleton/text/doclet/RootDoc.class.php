<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'util.cmd.ParamString',
    'ClassIterator',
    'ClassDoc', 
    'MethodDoc'
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
  define('T_IMPLEMENTS',          0x1001);
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
   * @see      xp://Doclet
   * @purpose  Entry point
   */
  class RootDoc extends Object {
    var
      $classes = NULL,
      $options = array();
    
    /**
     * Start a doclet
     *
     * @model   static
     * @access  public
     * @param   &Doclet doclet
     * @param   &util.cmd.ParamString params
     * @return  bool
     */
    function start(&$doclet, &$params) {
      $classes= array();
      $root= &new RootDoc();
      
      // Separate options from classes
      $valid= $doclet->validOptions();
      for ($i= 1; $i < $params->count; $i++) {
        $option= &$params->list[$i];
        
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
        } elseif (0 == strncmp($option, '-', 1)) {   // Short: -f / -f bar
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
      $root->classes= &new ClassIterator($classes);
      $root->classes->root= &$root;

      // Start the doclet
      return $doclet->start($root);
    }
    
    /**
     * Returns an option by a given name or the specified default value
     * if the option does not exist.
     *
     * @access  public
     * @param   string name
     * @param   string default default NULL
     * @return  string
     */
    function option($name, $default= NULL) {
      return isset($this->options[$name]) ? $this->options[$name] : $default;
    }
    
    /**
     * Finds a class by a given class name
     *
     * @access  protected
     * @param   string classname
     * @return  string filename
     */
    function findClass($classname) {
      $filename= str_replace('.', DIRECTORY_SEPARATOR, $classname).'.class.php';
      foreach (array_unique(explode(PATH_SEPARATOR, ini_get('include_path'))) as $dir) {
        if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
        return $dir.DIRECTORY_SEPARATOR.$filename;
      }
      return NULL;
    }
    
    /**
     * Parses a class file and returns a classdoc element
     *
     * @access  public
     * @param   string classname fully qualified class name
     * @return  &ClassDoc
     * @throws  lang.IllegalArgumentException if class could not be found or parsed
     */
    function &classNamed($classname) {
      static $cache= array();
      static $map= array('uses' => T_USES, 'implements' => T_IMPLEMENTS, 'define' => T_DEFINE);

      // Check cache
      if (isset($cache[$classname])) return $cache[$classname];

      // Find class
      if (!($filename= $this->findClass($classname))) {
        return throw(new IllegalArgumentException('Could not find "'.$classname.'"'));
      }
      
      // Tokenize contents
      if (!($tokens= token_get_all(file_get_contents($filename)))) {
        return throw(new IllegalArgumentException('Could not parse "'.$filename.'"'));
      }

      with ($doc= &new ClassDoc(), $doc->setRoot($this)); {
        $annotations= $comment= NULL;
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
            case ST_INITIAL.T_COMMENT:
            case ST_CLASS_BODY.T_COMMENT:
              if (0 == strncmp($t[1], '/**', 3)) {
                $comment= $t[1];
                break;
              }

              if (strncmp('#[@', $t[1], 3) == 0) {
                $annotations= substr($t[1], 2);
              } elseif (strncmp('#', $t[1], 1) == 0) {
                $annotations.= substr($t[1], 1);
              }

              if (']' == substr(rtrim($t[1]), -1)) {
                $annotations= '['.trim($annotations);
              }
              break;

            case ST_INITIAL.T_USES:
              $state= ST_USES;
              break;

            case ST_USES.T_CONSTANT_ENCAPSED_STRING:
              $doc->usedClasses->classes[trim($t[1], '"\'')]= NULL;
              break;

            case ST_USES.')':
              $state= ST_INITIAL;
              break;

            case ST_INITIAL.T_DEFINE:
              $state= ST_DEFINE;
              break;

            case ST_DEFINE.T_CONSTANT_ENCAPSED_STRING:
              $state= ST_DEFINE_VALUE;
              $define= $t[1];
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

            case ST_INITIAL.T_CLASS:
              while (T_STRING !== $tokens[$i][0] && $i < $s) $i++;

              $doc->name= $tokens[$i][1];
              $doc->qualifiedName= $classname;
              $doc->rawComment= $comment;
              $doc->annotations= $annotations;
              $comment= $annotations= NULL;
              $state= ST_CLASS;
              break;

            case ST_CLASS.T_EXTENDS:
              while (T_STRING !== $tokens[$i][0] && $i < $s) $i++;
              $search= strtolower($tokens[$i][1]);
        
              if (!($lookup= xp::registry('class.'.$search))) {
                foreach (array_keys($doc->usedClasses->classes) as $class) {
                  if (($cmp= xp::reflect($class)) != $search) continue;
                  xp::registry('class.'.$cmp, $class);
                  $lookup= $class;
                  break;
                }
              }
              $doc->superclass= &$this->classNamed($lookup);
              break;

            case ST_CLASS.'{':
              $state= ST_CLASS_BODY;
              break;

            case ST_CLASS_BODY.T_VAR:
              $state= ST_CLASS_VAR;
              break;

            case ST_CLASS_VAR.T_VARIABLE;
              $var= $t[1];
              break;

            case ST_CLASS_VAR.'=':
              $state= ST_VARIABLE_VALUE;
              break;

            case ST_CLASS_VAR.',':
              $doc->fields[$var]= NULL;
              break;

            case ST_CLASS_VAR.';':
              $doc->fields[$var]= NULL;
              $state= ST_CLASS_BODY;
              break;

            case ST_VARIABLE_VALUE.T_CONSTANT_ENCAPSED_STRING:
            case ST_VARIABLE_VALUE.T_LNUMBER:
            case ST_VARIABLE_VALUE.T_DNUMBER:
            case ST_VARIABLE_VALUE.T_STRING:
              $doc->fields[$var]= $t[1];
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

              $doc->fields[$var]= $src;
              break;
           
            case ST_VARIABLE_VALUE.',':
              $state= ST_CLASS_VAR;
              break;

            case ST_VARIABLE_VALUE.';':
              $state= ST_CLASS_BODY;
              break;
              
            case ST_CLASS_BODY.T_FUNCTION:
              while (T_STRING !== $tokens[$i][0] && $i < $s) $i++;

              with ($method= &new MethodDoc(), $method->setRoot($this)); {
                $method->name= $tokens[$i][1];
                $method->rawComment= $comment;
                $method->annotations= $annotations;
              }
              $doc->methods[]= &$method;
              $comment= $annotations= NULL;
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

            case ST_INITIAL.T_IMPLEMENTS:
              $state= ST_IMPLEMENTS;
              break;

            case ST_IMPLEMENTS.T_CONSTANT_ENCAPSED_STRING:
              $doc->interfaces->classes[trim($t[1], '"\'')]= NULL;
              break;

            case ST_IMPLEMENTS.')':
              $state= ST_INITIAL;
              break;
          }
        }
      }
      
      return $cache[$classname]= &$doc;
    }
  }
?>
