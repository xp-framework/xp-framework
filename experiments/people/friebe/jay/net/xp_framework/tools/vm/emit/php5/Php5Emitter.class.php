<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
 
  uses(
    'net.xp_framework.tools.vm.emit.Emitter',
    'net.xp_framework.tools.vm.util.Modifiers'
  );
 
  /**
   * Emits PHP5 compatible sourcecode
   *
   * @purpose  Emitter
   */
  class Php5Emitter extends Emitter {
    var
      $bytes   = '',
      $context = array(),
      $operators= array(
        '*' => 'times',
        '+' => 'plus',
        '-' => 'minus',
        '/' => 'divide',
        '.' => 'concat'
      );
      
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->context['package']= '';
      $this->context['imports']= array();
      $this->context['types']= array();
      $this->context['overloaded']= array();
      $this->context['annotations']= array();
      $this->context['default']= array();
      $this->context['class']= $this->context['method']= '<main>';
      
      // Builtin classes
      $this->context['classes']= array(
        'xp·lang·Object'    => array(
          'toString'        => TRUE,
          'getClassName'    => TRUE,
        ),
        'xp·lang·Throwable' => array(
          '__construct'     => TRUE,
          'toString'        => TRUE,    // overwritten
          'getClassName'    => TRUE,    // inherited
        ),
      );
      $this->context['classes']['xp·lang·Exception']= $this->context['classes']['xp·lang·Throwable'];
      $this->context['classes']['xp·lang·SystemExit']= $this->context['classes']['xp·lang·Throwable'];
      $this->context['classes']['xp·lang·IllegalAccessException']= $this->context['classes']['xp·lang·Throwable'];
      $this->context['classes']['xp·lang·NullPointerException']= $this->context['classes']['xp·lang·Throwable'];
      $this->context['classes']['xp·io·IOException']= $this->context['classes']['xp·lang·Throwable'];
    }

    /**
     * Retrieves qualified class name for a given class name.
     *
     * @access  protected
     * @param   string class
     * @return  string
     */
    function qualifiedName($class) {
      static $special= array('parent', 'self', 'xp');
      
      if (in_array($class, $special)) return $class;
      if ('php~' == substr($class, 0, 4)) return substr($class, 4);

      return strtr((strstr($class, '~') ? $class : $this->prefixedClassnameFor($class)), '~', '·');
    }
    
    /**
     * Retrieves prefixed class name for a given class name. Handles imports
     *
     * @access  protected
     * @param   string class
     * @return   string
     */
    function prefixedClassnameFor($class) {
      if (isset($this->context['imports'][$this->context['package']][$class]))
        return $this->context['imports'][$this->context['package']][$class];
        
      return $this->context['package'].$class;
    }
    
    /**
     * Retrieves type for a given node
     *
     * @access  protected
     * @param   &net.xp_framework.tools.vm.VNode node
     * @return  string
     */
    function typeOf(&$node) {
      if (is_a($node, 'NewNode')) {
        return $node->class->name;
      } else if (is_a($node, 'VariableNode')) {
        if ('$this' == $node->name) return $this->context['class'];
        return $this->context['types'][$this->context['class'].'::'.$this->context['method'].$node->name];
      } else if (is_a($node, 'MethodCallNode')) {
        return $this->context['types'][$node->class.'::'.$node->method->name];
      } else if (is_a($node, 'ParameterNode')) {
        return $node->type;
      } else if (is_a($node, 'BinaryNode')) {
        // TODO: Check operator overloading
        return NULL;
      } else if (is_a($node, 'ObjectReferenceNode')) {
        $ctype= is_string($node->class) ? $node->class : $this->typeOf($node->class);
        return $this->context['types'][$ctype.'::$'.$node->member->name];
      } else if ('"' == $node{0}) { // Double-quoted string
        return 'string';
      } else if ("'" == $node{0}) { // Single-quoted string
        return 'string';
      } else if (is_int($node) || is_a($node, 'LongNumberNode')) {
        return 'integer';
      } else if (is_float($node) || is_a($node, 'DoubleNumberNode')) {
        return 'double';
      } else if (is_string($node)) switch (strtolower($node)) {
        case 'true': return 'bool';
        case 'false': return 'bool';
        case 'null': return 'object';
      }
      
      $this->cat && $this->cat->warn('Cannot defer type from', $node);
      return NULL;  // Unknown
    }
    
    /**
     * Checks whether a given node has an annotation by the specified name
     *
     * @access  protected
     * @param   &net.xp_framework.tools.vm.VNode node
     * @param   string name
     * @return  bool
     */
    function hasAnnotation(&$node, $name) {
      foreach ($node->annotations as $annotation) {
        if ($annotation->type == $name) return TRUE;
      }
      return FALSE;
    }
    
    /**
     * Returns a method name. Handles overloaded methods
     *
     * @access  protected
     * @param   &net.xp_framework.tools.vm.VNode node
     * @return  string name
     */
    function methodName(&$node) {
      if (!$this->hasAnnotation($node, 'overloaded')) return $node->name;

      $this->context['overloaded'][$this->context['class'].'::'.$node->name]= TRUE;
      $name= $node->name;
      foreach ($node->parameters as $param) {
        $name.= $this->typeOf($param);
      }
      return $name;
    }

    /**
     * Checks whether a class implements an interface
     *
     * @access  protected
     * @param   string class
     * @param   string interface
     */
    function checkImplementation($class, $interface) {
      foreach ($this->context['classes'][$interface] as $name => $decl) {
        if (isset($this->context['classes'][$class][$name])) continue;
        
        $this->cat && $this->cat->error(
          $class, 'does not implement', $interface.'::'.$name,
          $class, 'methods=', xp::stringOf($this->context['classes'][$class]),
          $interface, ' methods=', xp::stringOf($this->context['classes'][$interface])
        );
        
        $this->addError(new CompileError(2000, $class.' does not implement '.$interface.'::'.$name));
      }
    }

    /**
     * Checks whether a given node is of a given type
     *
     * @access  protected
     * @param   &net.xp_framework.tools.vm.VNode node
     * @param   string type
     * @return  string type
     */
    function checkedType(&$node, $type) {
      // Console::writeLine($node->toString().'.TYPE('.$this->typeOf($node).') =? ', $type);
      
      // NULL indicates unknown, so no checks performed!
      if (NULL === $type || NULL === ($ntype= $this->typeOf($node))) return $type;  

      // FIXME: Inheritance/Cast?
      if ($ntype != $type) {
        $this->addError(new CompileError(3001, 'Type mismatch: '.$node->toString().'`s type ('.xp::stringOf($ntype).') != '.xp::stringOf($type)));
        return NULL;
      }

      return $type;
    }
    
    /**
     * Emits a list of parameters
     *
     * @access  protected
     * @param   net.xp_framework.tools.vm.ParameterNode[] parameters
     * @return  string source source to embed inside method declaration
     */
    function emitParameters($parameters) {
      $embed= '';
      $this->context['default'][$this->context['class'].'::'.$this->context['method']]= array();
      foreach ($parameters as $i => $param) {
      
        // Vararg or not vararg
        if ($param->vararg) {
          $embed.= '$__a= func_get_args(); '.$param->name.'= array_slice($__a, '.$i.');';
          $this->context['types'][$this->context['class'].'::'.$this->context['method'].$param->name]= array($param->type);
          
          if ($i != sizeof($parameters) - 1) {
            return $this->addError(new CompileError(1210, 'Vararags parameters must be the last parameter'));
          }
        } else {
          $this->context['types'][$this->context['class'].'::'.$this->context['method'].$param->name]= $param->type;
          $this->bytes.= $param->name;
        }
        
        // Parameter default value
        if ($param->default) {
          $this->bytes.= '= ';
          $this->emit($param->default);
          $this->context['default'][$this->context['class'].'::'.$this->context['method']][$i]= &$param->default;
        }
        $this->bytes.= ', ';
      }

      // Strip trailing comma
      $this->bytes= rtrim($this->bytes, ', ');

      return $embed;
    }

    /**
     * Emits a list of annotations
     *
     * @access  protected
     * @param   net.xp_framework.tools.vm.AnnotationNode[] parameters
     */
    function emitAnnotations($annotations) {
      $list= array();
      foreach ($annotations as $annotation) {
        
        // TODO: Generic compile-time-only annotations concept!
        if ('overloaded' == $annotation->type) continue;

        $list[$annotation->type]= $annotation->value;
      }
      
      $list && $this->context['annotations'][$this->context['class']][$this->context['method']]= $list;
    }
    
    /**
     * Emits a single node
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emit(&$node) {
      
      // Check if we find an array offset somewhere, e.g. $x->getElements()[0]
      // This will need to be translated to xp::element($x->getElements(), 0)
      if (is_a($node, 'VNode') && $node->chain) foreach ($node->chain as $i => $expr) {
        if (is_a($expr, 'ArrayOffsetNode')) {
          $this->bytes.= 'xp::wraparray(';
          $node->chain[$i]->first= TRUE;
          break;
        }
      }
      
      parent::emit($node);
    }

    /**
     * Emits an array of nodes
     *
     * @access  public
     * @param   net.xp_framework.tools.vm.VNode[] nodes
     */
    function emitAll($nodes) {
      foreach ($nodes as $node) {
        $this->emit($node);
        $this->bytes.= ";\n  ";
      }
    }
    
    /**
     * Retrieves result 
     *
     * @access  public
     * @return  string
     */
    function getResult() { 
      return "<?php\n  ".$this->bytes."\n?>";
    }

    /**
     * Emits an array
     *
     * @access  public
     * @param   array array
     */
    function emitArray($array) { 
      $this->bytes.= 'array(';
      foreach ($array as $key => $val) {
        $this->emit($key);
        $this->bytes.= ' => ';
        $this->emit($val);
        $this->bytes.= ', ';
      }
      $this->bytes.= ')';
    }

    /**
     * Emits an integer
     *
     * @access  public
     * @param   int integer
     */
    function emitInteger($integer) { 
      $this->bytes.= $integer;
    }

    /**
     * Emits a string
     *
     * @access  public
     * @param   string string
     */
    function emitString($string) { 
      $this->bytes.= "'".str_replace("'", "\'", $string)."'";
    }

    /**
     * Emits a boolean
     *
     * @access  public
     * @param   bool bool
     */
    function emitBoolean($bool) { 
      $this->bytes.= $bool ? 'TRUE' : 'FALSE';
    }

    /**
     * Emits a null
     *
     * @access  public
     */
    function emitNull() { 
      $this->bytes.= 'NULL';
    }

    /**
     * Emits a constant
     *
     * @access  public
     */
    function emitConstant($name) {
      $this->bytes.= $name;
    }
    
    /**
     * Emits PackageDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitPackageDeclaration(&$node) { 
      $this->context['package']= $node->name.'~';
      foreach ($node->statements as $node) {
        $this->emit($node);
      }
      
      unset($this->context['imports'][$this->context['package']]);
      $this->context['package']= NULL;
    }
    
    /**
     * Emits FunctionDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitFunctionDeclaration(&$node) { 
      $this->bytes.= 'function '.$node->name.'(';
      $embed= $this->emitParameters($node->parameters);
      $this->bytes.= ') {'.$embed;
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }

    /**
     * Emits MethodDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitMethodDeclaration(&$node) {
      $method= $this->methodName($node);
      $this->context['types'][$this->context['class'].'::'.$method]= $node->return;
      $this->context['method']= $method;
      $this->context['classes'][$this->context['class']][$method]= TRUE; // XXX DECL?
      
      $this->emitAnnotations($node->annotations);

      $this->bytes.= implode(' ', Modifiers::namesOf($node->modifiers)).' function '.$method.'(';
      
      // Method arguments
      $embed= $this->emitParameters($node->parameters);
      $this->bytes.= ')';

      // Method body
      if (NULL === $node->statements) {
        $this->bytes.= ';';
      } else {
        $this->bytes.= '{'.$embed;
        $this->emitAll($node->statements);
        $this->bytes.= '}';
      }

      $this->context['method']= '<main>';
    }

    /**
     * Emits ConstructorDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitConstructorDeclaration(&$node) { 
      $method= $this->methodName($node);
      $this->context['method']= $method;
      $this->context['types'][$this->context['class'].'::'.$method]= $this->context['class'];
      
      $this->bytes.= implode(' ', Modifiers::namesOf($node->modifiers)).' function '.$method.'(';
      $embed= $this->emitParameters($node->parameters);
      $this->bytes.= ')';

      if ($node->statements) {
        $this->bytes.= '{'.$embed;
        $this->emitAll($node->statements);
        $this->bytes.= '}';
      } else {
        $this->bytes.= ';';
      }

      $this->context['method']= '<main>';
    }

    /**
     * Emits DestructorDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitDestructorDeclaration(&$node) { 
      $method= '__destruct';
      $this->context['method']= $method;
      $this->context['types'][$this->context['class'].'::'.$method]= $this->context['class'];
      
      $this->bytes.= implode(' ', Modifiers::namesOf($node->modifiers)).' function '.$method.'(';
      $embed= $this->emitParameters($node->parameters);
      $this->bytes.= ')';

      if ($node->statements) {
        $this->bytes.= '{'.$embed;
        $this->emitAll($node->statements);
        $this->bytes.= '}';
      } else {
        $this->bytes.= ';';
      }

      $this->context['method']= '<main>';
    }
    
    /**
     * Lookup a class by its qualified name
     *
     * @access  protected
     * @param   string q qualified name
     * @return  array or NULL if the class wasn't found
     */
    function lookupClass($q) {
      if (!isset($this->context['classes'][$q])) {
      
        // Search classpath
        $filename= strtr($q, '·', DIRECTORY_SEPARATOR);
        foreach (explode(PATH_SEPARATOR, CLASSPATH) as $node) {
          $in= $node.DIRECTORY_SEPARATOR.$filename.'.xp';
          if (!file_exists($in)) continue;
          
          // Found the file, tokenize, parse and emit it.
          $lexer= &new Lexer(file_get_contents($in), $in);
          $out= &new File(str_replace('.xp', '.php5', $in));

          // XXX TODO XXX Error handling
          $parser= &new Parser($lexer);
          $nodes= $parser->yyparse($lexer);
          
          // XXX TODO XXX Error handling
          $emitter= &new Php5Emitter();
          $emitter->emitAll($nodes);
          
          // XXX TODO XXX Error handling
          FileUtil::setContents($out, $emitter->getResult());

          // XXX TODO XXX Merge rest of context with ours...
          $this->context['classes'][$q]= $emitter->context['classes'][$q];
          
          $this->cat && $this->cat->info('Compiled ', $q);
          return $this->context['classes'][$q];
        }
      
        // Could not find the class in CLASSPATH
        $this->cat && $this->cat->error(
          'Class:', $q, 'does not exist,',
          'declared=', implode(', ', array_keys($this->context['classes']))
        );
        return NULL;
      }
      
      return $this->context['classes'][$q];
    }
       
    /**
     * Emits ClassDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitClassDeclaration(&$node) {
      $this->context['properties']= array();
      $this->context['class']= $this->qualifiedName($node->name);
      $this->context['classes'][$this->context['class']]= array();
      $this->context['operators'][$this->context['class']]= array();
      $this->context['annotations'][$this->context['class']]= array();
      $extends= $this->qualifiedName($node->extends ? $node->extends : 'xp~lang~Object');

      $this->emitAnnotations($node->annotations);
      
      $node->modifiers & MODIFIER_ABSTRACT && $this->bytes.= 'abstract ';
      $node->modifiers & MODIFIER_FINAL && $this->bytes.= 'final ';

      $this->bytes.= 'class '.$this->context['class'].' extends '.$extends;

      // Copy members from parent class
      if (!($parent= $this->lookupClass($extends))) {
        $this->addError(new CompileError(1000, 'Parent class of '.$node->name.' ('.$extends.') does not exist'));
      }
      $this->context['classes'][$this->context['class']]= $parent;

      // Interfaces
      if ($node->implements) {
        $this->bytes.= ' implements ';
        foreach ($node->implements as $name) {
          $interface= $this->qualifiedName($name);
          if (!isset($this->context['classes'][$interface])) {
            $this->cat && $this->cat->error(
              'Interface:', $extends, 'does not exist,',
              'declared=', implode(', ', array_keys($this->context['classes']))
            );

            $this->addError(new CompileError(1001, 'Interface '.$interface.' does not exist'));
          }
          $this->bytes.= $interface.', ';
        }
        $this->bytes= substr($this->bytes, 0, -2);
      }

      // Class body
      $this->bytes.= '{';
      foreach ($node->statements as $stmt) {
        $this->emit($stmt);
      }
      
      // Property simulation via __get / __set
      // FIXME: Combination of __get() and foreach is buggy, see
      // http://derickrethans.nl/overloaded_properties_get.php
      if ($this->context['properties']) {
        $this->bytes.= "\n".'static $__properties= array(';
        foreach ($this->context['properties'] as $property) {
          $this->bytes.= "\n  '".substr($property->name, 1)."' => array(".
            ($property->accessors['get'] ? "'".$property->accessors['get']."'" : 'NULL').', '.
            ($property->accessors['set'] ? "'".$property->accessors['set']."'" : 'NULL').
          '),';
        }
        $this->bytes.= ');';
        $this->bytes.= '
          function __get($name) {
            if (!isset(self::$__properties[$name])) die(\'Read of non-existant property "\'.$name.\'"\');
            if (NULL === self::$__properties[$name][0]) {
              throw xp::exception(new xp·lang·IllegalAccessException(\'Cannot access property "\'.$name.\'"\'));
            } else if (\'$\' == self::$__properties[$name][0][0]) {
              return $this->{substr(self::$__properties[$name][0], 1)};
            } else {
              return $this->{self::$__properties[$name][0]}();
            }
          }

          function __set($name, $value) {
            if (!isset(self::$__properties[$name])) die(\'Write of non-existant property "\'.$name.\'"\');
            if (NULL === self::$__properties[$name][1]) {
              throw xp::exception(new xp·lang·IllegalAccessException(\'Cannot access property "\'.$name.\'"\'));
            } else if (\'$\' == self::$__properties[$name][1][0]) {
              $this->{substr(self::$__properties[$name][1], 1)}= $value;
            } else {
              $this->{self::$__properties[$name][1]}($value);
            }
          }
        ';
      }

      $this->bytes.= '}';
      
      // Check interface implementations
      foreach ($node->implements as $interface) {
        $this->checkImplementation($this->context['class'], $this->qualifiedName($interface));
      }

      // Annotations list
      if (!empty($this->context['annotations'][$this->context['class']])) {
        $this->bytes.= 'function __'.$this->context['class']."meta() { return array(\n";
        foreach ($this->context['annotations'][$this->context['class']] as $scope => $list) {
          $this->bytes.= "'".$scope."' => array(\n";
          foreach ($list as $key => $value) {
            $this->bytes.= "'".$key."' => ";
            $this->emit($value);
            $this->bytes.= ",\n";
          }
          $this->bytes.= "),\n";
        }
        $this->bytes.= ');}';
      }

      $this->context['class']= '<main>';
    }

    /**
     * Emits FunctionCalls
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitFunctionCall(&$node) { 
      $this->bytes.= $node->name.'(';
      foreach ($node->arguments as $arg) {
        $this->emit($arg);
        $this->bytes.= ', ';
      }
      $node->arguments && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ')';
    }

    /**
     * Emits Members
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitMember(&$node) {
      $this->bytes.= $node->name;
      if (NULL === $node->offset) return;

      $this->bytes.= '[';
      $this->emit($node->offset);
      $this->bytes.= ']';
    }

    /**
     * Emits MethodCalls
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitMethodCall(&$node) {
      if (is_string($node->class)) {      // Static
        $this->bytes.= $this->qualifiedName($node->class).'::';
        $type= $node->class;
      } else if ($node->class) {          // Instance
        $this->emit($node->class);    
        $type= $this->typeOf($node->class);
        $this->bytes.= '->';
      } else {                            // Chains
        $type= $this->typeOf($this->context['chain_prev']);
        $this->bytes.= '->';
      }

      $method= $node->method->name;
      if (isset($this->context['overloaded'][$type.'::'.$method])) {
        foreach ($node->arguments as $arg) {
          $method.= $this->typeOf($arg);
        }
      }
      $this->bytes.= $method.'(';
      for ($i= 0; $i < sizeof($node->arguments); $i++) {
        $this->emit($node->arguments[$i]);
        $this->bytes.= ', ';
      }
      
      // Pass default args
      if (!empty($this->context['default'][$type.'::'.$method])) {
        for ($defaults= $this->context['default'][$type.'::'.$method]; $i <= max(array_keys($defaults)); $i++) {
          $this->emit($defaults[$i]);
          $this->bytes.= ', ';
        }
      }
      
      $this->bytes= rtrim($this->bytes, ', ').')';
 
      // Chain: $x->getClass()->getName()
      if (!$node->chain) return;
      
      foreach ($node->chain as $chain) {
        $this->context['chain_prev']= $chain;
        $this->emit($chain);
      }
    }

    /**
     * Emits Nots
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitNot(&$node) { 
      $this->bytes.= '!';
      $this->emit($node->expression);
    }

    /**
     * Emits ObjectReferences
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitObjectReference(&$node) { 
      $this->emit($node->class);
      $this->bytes.= '->';
      $this->emit($node->member);

      if (!$node->chain) return;
      
      foreach ($node->chain as $chain) {
        $this->context['chain_prev']= $chain;
        $this->emit($chain);
      }
    }

    /**
     * Emits Binarys
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitBinary(&$node) {
      $type= $this->typeOf($node->left);

      // Check for operator overloading
      if (isset($this->context['operators'][$type][$node->operator])) {
        return $this->emit(new MethodCallNode(
          $type, 
          new MemberNode('__operator'.$this->operators[$node->operator]),
          array($node->left, $node->right)
        ));
      }
      
      // Regular operator
      $this->emit($node->left);
      $this->bytes.= $node->operator;
      $this->emit($node->right);
    }

    /**
     * Emits Variables
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitVariable(&$node) { 
      $this->bytes.= $node->name;
      if (NULL === $node->offset) return;

      $this->bytes.= '[';
      $this->emit($node->offset);
      $this->bytes.= ']';
    }

    /**
     * Emits Assigns
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitAssign(&$node) {
      $this->emit($node->variable);
      $this->bytes.= '= ';
      $this->emit($node->expression);

      // Handle ObjectReferenceNode ($this->buffer) vs. of VariableNode ($name)
      if (is_a($node->variable, 'ObjectReferenceNode')) {
        $scope= $node->variable->class.'::$'.$node->variable->member->name;   // FIXME :$this!
      } else {
        $scope= $this->context['class'].'::'.$this->context['method'].$node->variable->name;
      }

      $this->context['types'][$scope]= $this->checkedType($node->variable, $this->typeOf($node->expression));
    }

    /**
     * Emits BinaryAssigns
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitBinaryAssign(&$node) { 
      $type= $this->typeOf($node->variable);
      $this->emit($node->variable);

      // Check for operator overloading
      if (isset($this->context['operators'][$type][$node->operator])) {
        $this->bytes.= '= ';
        $m= &new MethodCallNode(
          $type, 
          new MemberNode('__operator'.$this->operators[$node->operator]),
          array($node->variable, $node->expression)
        );

        $this->context['types'][$this->context['class'].'::'.$this->context['method'].$node->variable->name]= $this->typeOf($m);
        return $this->emit($m);
      }

      $this->bytes.= $node->operator.'= ';
      $this->emit($node->expression);

      $this->context['types'][$this->context['class'].'::'.$this->context['method'].$node->variable->name]= 'string';   // FIXME!
    }

    /**
     * Emits Ifs
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitIf(&$node) { 
      $this->bytes.= 'if (';
      $this->emit($node->condition);
      $this->bytes.= ')';
      
      $this->bytes.= '{ ';
      $this->emitAll($node->statements);
      $this->bytes.= '}';
      
      if ($node->else) {
        $this->bytes.= ' else ';
        if (is_array($node->else)) {
          $this->bytes.= '{';
          $this->emitAll($node->else);
          $this->bytes.= '}';
        } else {
          $this->bytes.= ' ';
          $this->emit($node->else);
        }
      }
    }

    /**
     * Emits Exits
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitExit(&$node) { 
      $this->bytes.= 'throw xp::exception(new xp·lang·SystemExit(';
      $node->expression && $this->emit($node->expression);
      $this->bytes.= '))';
    }

    /**
     * Emits News
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitNew(&$node) {

      // Check if we find an array offset somewhere, e.g. new ArrayList()->getElements()[0]
      // This will need to be translated to xp::element(new ArrayList(), 0)
      if ($node->instanciation->chain) foreach ($node->instanciation->chain as $i => $expr) {
        if (is_a($expr, 'ArrayOffsetNode')) {
          $this->bytes.= 'xp::wraparray(';
          $node->instanciation->chain[$i]->first= TRUE;
          break;
        }
      }

      if ($node->instanciation->declaration) {
        $this->bytes.= 'xp::instance(\''.$this->qualifiedName($node->class->name).'\', array(';
        foreach ($node->instanciation->arguments as $arg) {
          $this->emit($arg);
          $this->bytes.= ', ';
        }
        $node->instanciation->arguments && $this->bytes= substr($this->bytes, 0, -2);
        $this->bytes.= '), \'{';
        $b= $this->bytes;

        $this->bytes= '';
        foreach ($node->instanciation->declaration as $decl) {
          $this->emit($decl);
        }

        $this->bytes= $b.str_replace('\'', '\\\'', $this->bytes).'}\'';
        $node->instanciation->chain || $this->bytes.= ')';
      } else {
        $node->instanciation->chain && $this->bytes.= 'xp::create(';
        
        if (isset($this->context['overloaded'][$node->class->name.'::__construct'])) {
          $ctor= '__construct';
          foreach ($node->instanciation->arguments as $arg) {
            $ctor.= $this->typeOf($arg);
          }
          $this->bytes.= 'xp::spawn(\''.$this->qualifiedName($node->class->name).'\', \''.$ctor.'\', array(';
          foreach ($node->instanciation->arguments as $arg) {
            $this->emit($arg);
            $this->bytes.= ', ';
          }
          $node->instanciation->arguments && $this->bytes= substr($this->bytes, 0, -2);
          $this->bytes.= '))';
        } else {
          $this->bytes.= 'new '.$this->qualifiedName($node->class->name).'(';
          foreach ($node->instanciation->arguments as $arg) {
            $this->emit($arg);
            $this->bytes.= ', ';
          }
          $node->instanciation->arguments && $this->bytes= substr($this->bytes, 0, -2);
          $this->bytes.= ')';
        }
      }

      if ($node->instanciation->chain) {
        $this->bytes.= ')';
        foreach ($node->instanciation->chain as $chain) {
          $this->emit($chain);
        }
      }
    }
    
    /**
     * Emits ImportLists
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitImportList(&$node) {
      foreach ($node->list as $import) { $this->emit($import); }
    }
    
    /**
     * Emits Imports
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitImport(&$node) {
      if (is_a($node->source, 'ClassReferenceNode')) $source= $node->source->name;
      
      $destination= $node->destination;
    
      // Calculate destination name if none was supplied
      if (!$destination) {
        $destination= substr($source, strrpos($source, '~')+ 1);
      }
      
      // Register import
      $this->context['imports'][$this->context['package']][$destination]= $source;
    }

    /**
     * Emits Trys
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitTry(&$node) { 
      $this->bytes.= "try {\n  ";
      $this->emitAll($node->statements);      

      $this->bytes.= '} catch (XPException $__e) { ';
      $this->bytes.= 'if ($__e->cause instanceof '.$this->qualifiedName($node->catch->class).') { ';
      $this->bytes.= $node->catch->variable.'= $__e->cause; ';
      
      foreach ($node->catch->statements as $stmt) {
        if (is_a($stmt, 'ReturnNode') || is_a($stmt, 'ThrowNode')) {
          $node->finally && $this->emitAll($node->finally->statements);
        }
      
        $this->emit($stmt);
        $this->bytes.= ';';
      }
      
      foreach ($node->catch->catches as $catch) {
        $this->bytes.= '} else if ($__e->cause instanceof '.$this->qualifiedName($catch->class).') { ';
        $this->bytes.= $catch->variable.'= $__e->cause; ';
        $this->emitAll($catch->statements);
      }
      
      $this->bytes.= '} else { throw $__e; } }';
      
      $node->finally && $this->emitAll($node->finally->statements);
    }

    /**
     * Emits Echos
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitEcho(&$node) {
      $this->bytes.= 'echo ';
      foreach ($node->args as $arg) {
        $this->emit($arg);
        $this->bytes.= ', ';
      }
      $this->bytes= substr($this->bytes, 0, -2);
    }

    /**
     * Emits Returns
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitReturn(&$node) { 
      $this->bytes.= 'return ';
      $this->emit($node->value);
    }

    /**
     * Emits Ternarys
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitTernary(&$node) {
      $this->emit($node->condition);
      $this->bytes.= ' ? ';
      $this->emit($node->expression);
      $this->bytes.= ' : ';
      $this->emit($node->conditional);
    }

    /**
     * Emits Fors
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitFor(&$node) {
      $this->bytes.= 'for (';
      foreach ($node->init as $expr) {
        $this->emit($expr);
        $this->bytes.= ', ';
      }
      $node->init && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ';';
 
      foreach ($node->condition as $expr) {
        $this->emit($expr);
        $this->bytes.= ', ';
      }
      $node->condition && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ';';
 
      foreach ($node->loop as $expr) {
        $this->emit($expr);
        $this->bytes.= ', ';
      }
      $node->loop && $this->bytes= substr($this->bytes, 0, -2);

      $this->bytes.= ') {';
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }
    
    /**
     * Emits PostIncs
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitPostInc(&$node) {
      $this->emit($node->expression);
      $this->bytes.= '++';
    }

    /**
     * Emits MemberDeclarationLists
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitMemberDeclarationList(&$node) { 
      $members= '';
      foreach ($node->members as $member) {
        $this->context['types'][$this->context['class'].'::'.$member->name]= $node->type;
        if (is_a($member, 'PropertyDeclarationNode')) {
          $this->context['properties'][]= $member;
        } else {
          $members.= $member->name.', ';
        }
      }
      $members && $this->bytes.= implode(' ', Modifiers::namesOf($node->modifiers)).' '.substr($members, 0, -2).';';
    }

    /**
     * Emits OperatorDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitOperatorDeclaration(&$node) {       
      $method= '__operator'.$this->operators[$node->name];
      $this->context['method']= $method;
      $this->context['types'][$this->context['class'].'::'.$method]= $this->context['class'];
      $this->context['operators'][$this->context['class']][$node->name]= TRUE;

      $this->bytes.= 'function '.$method.'(';
      foreach ($node->parameters as $param) {
        $this->bytes.= $param->name;
        if ($param->default) {
          $this->bytes.= '= ';
          $this->emit($param->default);
        }
        $this->bytes.= ', ';
      }
      $node->parameters && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ') {';
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }

    /**
     * Emits Throws
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitThrow(&$node) {       
      $this->bytes.= 'throw xp::exception(';
      $this->emit($node->value);
      $this->bytes.= ')';
    }

    /**
     * Emits InstanceOfs
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitInstanceOf(&$node) {
      $this->emit($node->object);
      $this->bytes.= ' instanceof ';
      $this->emit($node->type);
    }

    /**
     * Emits ClassReferences
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitClassReference(&$node) {
      $this->bytes.= $node->name;
    }

    /**
     * Emits InterfaceDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitInterfaceDeclaration(&$node) {
      $this->context['class']= $this->qualifiedName($node->name);
      $this->context['classes'][$this->context['class']]= array();
      $this->bytes.= 'interface '.$this->context['class'];

      // Handle interface inheritance
      if ($node->extends) {
        $this->bytes.= ' extends ';
        foreach ($node->extends as $interface) {
          $extends= $this->qualifiedName($interface);

          if (!isset($this->context['classes'][$extends])) {
            $this->cat && $this->cat->error(
              'Interface:', $extends, 'does not exist,',
              'declared=', implode(', ', array_keys($this->context['classes']))
            );

            $this->addError(new CompileError(1001, 'Interface '.$extends.' does not exist'));
          }

          $this->context['classes'][$this->context['class']]= $this->context['classes'][$extends];
          $this->bytes.= $extends.', ';
        }
        $this->bytes= substr($this->bytes, 0, -2);
      } 

      $this->bytes.= '{';
      foreach ($node->statements as $stmt) {
        if (is_a($stmt, 'MethodDeclarationNode')) {
          $this->context['classes'][$this->context['class']][$this->methodName($stmt)]= TRUE; // XXX DECL?
        }
      }
      $this->bytes.= '}';
      $this->context['class']= '<main>';
    }

    /**
     * Emits Whiles
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitWhile(&$node) { 
      $this->bytes.= 'while (';
      $this->emit($node->condition);
      $this->bytes.= ') {';
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }

    /**
     * Emits Do ... Whiles
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitDoWhile(&$node) {
      $this->bytes.= 'do {';
      $this->emitAll($node->statements);
      $this->bytes.= '} while (';
      $this->emit($node->condition);
      $this->bytes.= ')';
    }

    /**
     * Emits Foreachs
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitForeach(&$node) {
      $this->bytes.= 'foreach (';
      $this->emit($node->expression);
      $this->bytes.= ' as ';
      $this->emit($node->key);
      if ($node->value) {
        $this->bytes.= ' => ';
        $this->emit($node->value);
      }
      $this->bytes.= ') {';
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }

    /**
     * Emits Switches
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitSwitch(&$node) {
      $this->bytes.= 'switch (';
      $this->emit($node->condition);
      $this->bytes.= ') {';
      $this->emitAll($node->cases);
      $this->bytes.= '}';
    }

    /**
     * Emits Cases
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitCase(&$node) {
      $this->bytes.= 'case ';
      $this->emit($node->expression);
      $this->bytes.= ': ';
      $this->emitAll($node->statements);
    }

    /**
     * Emits Defaults
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitDefault(&$node) { 
      $this->bytes.= 'default: ';
      $this->emitAll($node->statements);
    }

    /**
     * Emits Breaks
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitBreak(&$node) { 
      $this->bytes.= 'break';
      if ($node->expression) {
        $this->bytes.= ' ';
        $this->emit($node->expression);
      }
    }

    /**
     * Emits Continues
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitContinue(&$node) {
      $this->bytes.= 'continue';
      if ($node->expression) {
        $this->bytes.= ' ';
        $this->emit($node->expression);
      }
    }

    /**
     * Emits static variables list
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitStaticVariableList(&$node) {
      $this->bytes.= 'static ';
      foreach ($node->list as $static) {
        $this->emit($static);
        $this->bytes.= ', ';
      }
      $this->bytes= substr($this->bytes, 0, -2);
    }

    /**
     * Emits static variables
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitStaticVariable(&$node) {
      $this->bytes.= $node->name;
      if (NULL !== $node->initial) {
        $this->bytes.= '= ';
        $this->emit($node->initial);
      }
    }

    /**
     * Emits isset
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitIsset(&$node) {
      $this->bytes.= 'isset(';
      foreach ($node->list as $expr) {
        $this->emit($expr);
        $this->bytes.= ', ';
      }
      $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ')';
    }

    /**
     * Emits empty
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitEmpty(&$node) {
      $this->bytes.= 'empty(';
      $this->emit($node->expression);
      $this->bytes.= ')';
    }

    /**
     * Emits constant references
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitConstantReference(&$node) {
      $this->bytes.= ($node->class ? $node->class.'::' : '').$node->name;
    }

    /**
     * Emits constant references
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitBooleanOperator(&$node) {
      $this->emit($node->left);
      $this->bytes.= ' '.$node->operator.' ';
      $this->emit($node->right);
    }

    /**
     * Emits array offsets (used for getElements()[0])
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitArrayOffset(&$node) {
      $node->first && $this->bytes.= ')->backing';
      $this->bytes.= '[';
      $this->emit($node->expression);
      $this->bytes.= ']';
    }

    /**
     * Emits expression casts
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitExpressionCast(&$node) {
      static $supported= array('int', 'string', 'bool', 'double', 'array');
      
      if (in_array($node->type, $supported)) {
        $this->bytes.= '('.$node->type.')';
        $this->emit($node->expression);
      } else {
        $this->bytes.= 'xp::cast(\''.$node->type.'\', ';
        $this->emit($node->expression);
        $this->bytes.= ')';
      }
    }

    /**
     * Emits bracketed expressions ("(" expression ")")
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitBracketedExpression(&$node) {
      $this->bytes.= '(';
      $this->emit($node->expression);
      $this->bytes.= ')';
    }

    /**
     * Emits silenced expressions ("@" expression)
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitSilencedExpression(&$node) {
      $this->bytes.= '@';
      $this->emit($node->expression);
    }

    /**
     * Emits array access
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitArrayAccess(&$node) {
      $this->emit($node->expression);
      $this->bytes.= '[';
      $this->emit($node->offset);
      $this->bytes.= ']';
    }

    /**
     * Emits static members
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitStaticMember(&$node) {
      $this->bytes.= $node->class.'::';
      $this->emit($node->member);
    }

    /**
     * Emits list()-assignment
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitListAssign(&$node) {
      $this->bytes.= 'list(';
      foreach ($node->assignments as $expr) {
        $this->emit($expr);
        $this->bytes.= ', ';
      }
      $this->bytes.= ')= ';
      $this->emit($node->expression);
    }
    
    /**
     * Emits a double number
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitDoubleNumber(&$node) {
      $this->bytes.= $node->value;
    }

    /**
     * Emits a long number
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitLongNumber(&$node) {
      if ('0x' == substr($node->value, 0, 2)) {
        $this->bytes.= hexdec($node->value);
      } else {
        $this->bytes.= $node->value;
      }
    }
  }
?>
