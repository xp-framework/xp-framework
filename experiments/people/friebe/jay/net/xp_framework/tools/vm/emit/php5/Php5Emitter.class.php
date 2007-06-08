<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
 
  uses(
    'io.File',
    'io.FileUtil',
    'util.profiling.Timer',
    'net.xp_framework.tools.vm.Lexer',
    'net.xp_framework.tools.vm.Parser',
    'net.xp_framework.tools.vm.emit.Emitter'
  );
  
  define('PACKAGE_SEPARATOR', '.');
  define('DEFAULT_PACKAGE',   'main'.PACKAGE_SEPARATOR);
 
  /**
   * Emits PHP5 compatible sourcecode
   *
   * @purpose  Emitter
   */
  class Php5Emitter extends Emitter {
    public
      $bytes        = '',
      $context      = array(),
      $overloadable = array(
        '*' => 'times',
        '+' => 'plus',
        '-' => 'minus',
        '/' => 'divide',
        '~' => 'concat',
        '%' => 'modulo',
      );
    
    protected static 
      $builtin= array();
    
    static function __static() {
      foreach (array_merge(get_declared_classes(), get_declared_interfaces()) as $name) {
        $r= new ReflectionClass($name);
        if ($r->isInternal()) {
          self::$builtin[$name]= array();
        }
      }
      self::$builtin['lang·Object']= array();
    }
      
    /**
     * Constructor
     *
     */
    public function __construct() {
      static $langImported= FALSE;
      
      $this->context['package']= DEFAULT_PACKAGE;
      $this->context['imports']= array();
      $this->context['uses']= array();
      $this->context['types']= array();
      $this->context['overloaded']= array();
      $this->context['default']= array();
      $this->context['operators']= array();
      $this->context['class']= $this->context['method']= '<main>';
      
      // Builtin classes
      $this->context['classes']= self::$builtin;
      
      // Auto-import lang.*
      if (!$langImported) $langImported= $this->importAllOf('lang', FALSE);
    }
    
    /**
     * Import all files in a given package
     *
     * @access  protected
     * @param   string package
     * @param   bool use default TRUE whether to add these classes to uses()
     * @return  int how many files where imported
     */
    public function importAllOf($package, $use= TRUE) {
      $imported= 0;
      $pdir= DIRECTORY_SEPARATOR.strtr($package, '.', DIRECTORY_SEPARATOR);
      
      foreach (explode(PATH_SEPARATOR, CLASSPATH) as $node) {
        if (!is_dir($node.$pdir)) continue;

        $d= dir($node.$pdir);
        while ($file= $d->read()) {
          if (2 != sscanf($file, '%[^.].%s', $classname, $ext) || 'class.xp' != $ext) continue;
          
          $use && $this->context['uses'][$package.'.'.$classname]= $d->path.DIRECTORY_SEPARATOR.$file;
          $this->context['imports'][$this->context['package']][$classname]= $package.'·'.$classname;
          $imported++;
        }
        $d->close();
      }
      return $imported;
    }

    /**
     * Retrieves qualified type name for a given type.
     *
     * @access  protected
     * @param   string type
     * @return  string
     */
    public function typeName($type) {
      static $primitives= array('int', 'string', 'double', 'bool', NULL);

      if (is_array($type)) return $this->typeName($type[0]).'[]';
      if (in_array($type, $primitives)) return $type;
      if ('mixed' == $type) return NULL;
      if ('array' == $type) return 'mixed[]';
      if ('self' == $type) return $this->context['class'];
      if ('parent' == $type) return $this->context['classes'][$this->context['class']][0];
      
      return $this->qualifiedName($type);
    }

    /**
     * Retrieves qualified class name for a given class name.
     *
     * @access  protected
     * @param   string class
     * @param   bool imports default TRUE whether to look for qualified names in imports
     * @return  string
     */
    public function qualifiedName($class, $imports= TRUE) {
      static $special= array('parent', 'self', 'xp', 'null');

      if (in_array($class, $special)) return $class;
      if ('php.' == substr($class, 0, 4)) return substr($class, 4);
      if (strstr($class, '·')) return $class; // Already qualified!

      return strtr((strstr($class, PACKAGE_SEPARATOR) ? $class : $this->prefixedClassnameFor($class, $imports)), PACKAGE_SEPARATOR, '·');
    }
    
    /**
     * Retrieves prefixed class name for a given class name. Handles imports
     *
     * @access  protected
     * @param   string class
     * @param   bool imports default TRUE whether to look for qualified names in imports
     * @return  string
     */
    public function prefixedClassnameFor($class, $imports= TRUE) {
      if ($imports && isset($this->context['imports'][$this->context['package']][$class])) {
        return $this->context['imports'][$this->context['package']][$class];
      }

      return $this->context['package'].$class;
    }

    /**
     * Sets type for a given hash
     *
     * @access  protected
     * @param   string hash
     * @return  mixed type
     */
    public function setType($hash, $type) {
      $this->context['types'][$hash]= $type;
      // DEBUG Console::writeLine($hash, ' => ', xp::stringOf($type));
    }
    
    /**
     * Sets context class
     *
     * @access  protected
     * @param   string hash
     * @return  mixed type
     */
    public function setContextClass($name) {
      $this->context['class']= $name;
      // DEBUG Console::writeLine('* {contextclass= ', $name, '} *');
    }
 
    /**
     * Retrieves scope for a given node
     *
     * @access  protected
     * @param   net.xp_framework.tools.vm.VNode node
     * @return  string
     */
    public function scopeFor($node) {
    
      // $x
      if ($node instanceof VariableNode) {
        return $this->context['class'].'::'.$this->context['method'].$node->name;
      }

      // $a->buffer (typeOf($node) = class, "buffer" == $node->name)
      if ($node instanceof ObjectReferenceNode) {
        return $this->typeOf($node).'::$'.$node->member->name;
      } 
      
      // $r[]= ... ($node->expression= VariableNode($r))
      if ($node instanceof ArrayAccessNode) {
        return $this->scopeFor($node->expression).'#'.($node->offset ? $node->offset->value : '');
      }
      
      // self::$instance
      if ($node instanceof StaticMemberNode) {
        return $node->class.'::$'.$node->member->name;
      }

      $this->addError(new CompileError(9000, 'Internal compiler error: Cannot associate scope w/ '.xp::stringOf($node)));
      return NULL;
    }
    
    /**
     * Retrieves type for a given node
     *
     * @access  protected
     * @param   net.xp_framework.tools.vm.VNode node
     * @return  string
     */
    public function typeOf($node) {
      if ($node instanceof NewNode) {
        if (!$node->instanciation->chain) return $this->qualifiedName($node->class->name);
        
        $cclass= $this->context['class'];   // Backup
        $this->setContextClass($this->qualifiedName($node->class->name));
        foreach ($node->instanciation->chain as $chain) {
          $this->setContextClass($this->typeOf($chain));
        }
        $type= $this->context['class'];
        $this->setContextClass($cclass);    // Restore
        return $type;
      } else if ($node instanceof VariableNode) {
        if ('$this' == $node->name) return $this->context['class'];
        return $this->context['types'][$this->context['class'].'::'.$this->context['method'].$node->name];
      } else if ($node instanceof MethodCallNode) {
        $ctype= NULL === $node->class ? $this->context['class'] : (is_string($node->class) 
          ? $this->qualifiedName($node->class) 
          : $this->typeOf($node->class)
        );
        
        if (!$node->chain) return $this->context['types'][$ctype.'::'.$node->method->name];
        
        $cclass= $this->context['class'];   // Backup
        $this->setContextClass($this->context['types'][$ctype.'::'.$node->method->name]);
        foreach ($node->chain as $chain) {
          $this->setContextClass($this->typeOf($chain));
        }
        $type= $this->context['class'];
        $this->setContextClass($cclass);    // Restore
        return $type;
      } else if ($node instanceof ParameterNode) {
        return $this->typeName($node->type);
      } else if ($node instanceof BinaryNode) {
        if (in_array($node->op, array('<', '<=', '>', '>=', '==', '!=', '===', '!=='))) return 'bool';
        $type= $this->typeOf($node->left);
        // TODO: Check operator overloading
        return $type;
      } else if ($node instanceof ObjectReferenceNode) {
        $ctype= NULL === $node->class ? $this->context['class'] : (is_string($node->class) 
          ? $this->qualifiedName($node->class) 
          : $this->typeOf($node->class)
        );
        if (!$node->chain) return $this->context['types'][$ctype.'::$'.$node->member->name];

        $cclass= $this->context['class'];   // Backup
        $this->setContextClass($this->context['types'][$ctype.'::$'.$node->member->name]);
        foreach ($node->chain as $chain) {
          $this->setContextClass($this->typeOf($chain));
        }
        $type= $this->context['class'];
        $this->setContextClass($cclass);    // Restore
        
        return $type;
      } else if ($node instanceof ArrayDeclarationNode) {
        return array($node->type);
      } else if ($node instanceof ArrayAccessNode) {
        $t= $this->typeOf($node->expression);
        return is_array($t) ? $t[0] : $t;         // $list[0] vs. $string{0}
      } else if ($node instanceof ExpressionCastNode) {
        return $this->typeName($node->type);
      } else if (is_int($node) || $node instanceof LongNumberNode) {
        return 'int';
      } else if (is_float($node) || $node instanceof DoubleNumberNode) {
        return 'double';
      } else if ($node instanceof VNode) { 
        // Intentionally empty
      } else if ('"' == $node{0}) { // Double-quoted string
        return 'string';
      } else if ("'" == $node{0}) { // Single-quoted string
        return 'string';
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
     * @param   net.xp_framework.tools.vm.VNode node
     * @param   string name
     * @return  bool
     */
    public function hasAnnotation($node, $name) {
      foreach ($node->annotations as $annotation) {
        if ($annotation->type == $name) return TRUE;
      }
      return FALSE;
    }
    
    /**
     * Returns a method name. Handles overloaded methods
     *
     * @access  protected
     * @param   net.xp_framework.tools.vm.VNode node
     * @return  string name
     */
    public function methodName($node) {
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
    public function checkImplementation($class, $interface) {
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
     * @param   net.xp_framework.tools.vm.VNode node
     * @param   string type
     * @return  string type
     */
    public function checkedType($node, $type) {
      // Console::writeLine($node->toString().'.TYPE('.$this->typeOf($node).') =? ', $type);
      
      // NULL indicates unknown, so no checks performed!
      if (NULL === $type || NULL === ($ntype= $this->typeName($this->typeOf($node)))) return $type;  
      
      // Easiest case: Types match exactly
      $type= $this->typeName($type);
      if ($ntype == $type) return $type;
      
      // Array types
      if ('[][]' == substr($ntype, -2).substr($type, -2)) return $type;
      
      // Check inheritance
      if (isset($this->context['classes'][$ntype]) && isset($this->context['classes'][$type])) {
        $parent= $ntype;
        do {
          if ($parent === $type) return $parent;
          if (
            isset($this->context['classes'][$parent][1]) && 
            isset($this->context['classes'][$parent][1][$type])
          ) return $type;
        } while ($parent= $this->context['classes'][$parent][0]);
        
        $parent= $type;
        do {
          if ($parent === $ntype) return $parent;
          if (
            isset($this->context['classes'][$parent][1]) && 
            isset($this->context['classes'][$parent][1][$ntype])
          ) return $type;
        } while ($parent= $this->context['classes'][$parent][0]);
      }
      
      // Every check failed, raise an error
      $this->addError(new CompileError(3001, 'Type mismatch: '.xp::stringOf($node).'`s type ('.xp::stringOf($ntype).') != '.xp::stringOf($type)));
      return NULL;
    }
    
    /**
     * Emits a list of parameters
     *
     * @access  protected
     * @param   net.xp_framework.tools.vm.ParameterNode[] parameters
     * @return  string source source to embed inside method declaration
     */
    public function emitParameters($parameters) {
      $embed= '';
      $this->context['default'][$this->context['class'].'::'.$this->context['method']]= array();
      foreach ($parameters as $i => $param) {
      
        // Vararg or not vararg
        if ($param->vararg) {
          $embed.= '$__a= func_get_args(); '.$param->name.'= array_slice($__a, '.$i.');';
          $this->setType($this->context['class'].'::'.$this->context['method'].$param->name, $this->typeName(array($param->type)));
          $this->setType($this->context['class'].'::'.$this->context['method'].'@'.$i, $this->typeName($param->type).'*');
          
          if ($i != sizeof($parameters) - 1) {
            return $this->addError(new CompileError(1210, 'Vararags parameters must be the last parameter'));
          }
        } else {
          $this->setType($this->context['class'].'::'.$this->context['method'].'@'.$i, $this->typeName($param->type));
          $this->setType($this->context['class'].'::'.$this->context['method'].$param->name, $this->typeName($param->type));
          $this->bytes.= $param->name;
        }
        
        // Parameter default value
        if ($param->default) {
          $this->bytes.= '= ';
          $this->emit($param->default);
          $this->context['default'][$this->context['class'].'::'.$this->context['method']][$i]= $param->default;
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
    public function emitAnnotations($annotations) {
      $source= '';
      foreach ($annotations as $annotation) {
        
        // TODO: Generic compile-time-only annotations concept!
        if ('overloaded' == $annotation->type) continue;

        $source.= '@'.$annotation->type;
        if ($annotation->value instanceof ConstantReferenceNode && 'NULL' === $annotation->value->name) {

          // [@test]
        } else if ($annotation->value instanceof ArrayDeclarationNode) {

          // [@webservice(name= "XX", public= TRUE, roles= array("guest", "any"))]
          $source.= '(';
          foreach ($annotation->value->elements as $key => $value) {
            $source.= trim($key, '\'"').'= ';

            $b= $this->bytes;
            $this->bytes= '';
            $this->emit($value);
            $source.= $this->bytes;
            $this->bytes= $b;
          }
          $source.= ')';
        } else if (is_string($annotation->value)) {

          // [@example('Calculator'))]
          $source.= '('.$annotation->value.')';
        }
        $source.= ', ';
      }
      
      if ('' != $source) $this->bytes.= "\n#[".rtrim($source, ', ')."]\n";
    }
    
    /**
     * Emits a single node
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emit($node) {
      
      // Check if we find an array offset somewhere, e.g. $x->getElements()[0]
      // This will need to be translated to xp::wraparray($x->getElements(), 0)
      if ($node instanceof VNode && isset($node->chain)) foreach ($node->chain as $i => $expr) {
        if ($expr instanceof ArrayOffsetNode) {
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
     * @param   net.xp_framework.tools.vm.VNode[] nodes
     */
    public function emitAll($nodes) {
      foreach ($nodes as $node) {
        $this->emit($node);
        $this->bytes.= ";\n  ";
      }
    }

    /**
     * Emits a block
     *
     * @param   net.xp_framework.tools.vm.VNode[] nodes
     */
    public function emitBlock($nodes) { 
      $this->bytes.= '{';
      $this->emitAll($nodes);
      $this->bytes.= '}';
    }
    
    /**
     * Retrieves result 
     *
     * @return  string
     */
    public function getResult() { 
      $src= "<?php\n  ";
      if (!empty($this->context['uses'])) {
        $src.= 'uses(\''.implode('\', \'', array_keys($this->context['uses'])).'\');';
      }
      
      return $src.$this->bytes."\n?>";
    }

    /**
     * Emits an integer
     *
     * @param   int integer
     */
    public function emitInteger($integer) { 
      $this->bytes.= $integer;
    }

    /**
     * Emits a string
     *
     * @param   string string
     */
    public function emitString($string) { 
      $this->bytes.= "'".str_replace("'", "\'", $string)."'";
    }

    /**
     * Emits a constant
     *
     */
    public function emitConstant($name) {
      $this->bytes.= $name;
    }
    
    /**
     * Emits PackageDeclarations
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitPackageDeclaration($node) { 
      $this->context['package']= $node->name.PACKAGE_SEPARATOR;
      $this->bytes.= '$package= \''.$node->name.'\'; ';
      foreach ($node->statements as $node) {
        $this->emit($node);
      }
      
      unset($this->context['imports'][$this->context['package']]);
      $this->context['package']= DEFAULT_PACKAGE;
    }
    
    /**
     * Emits FunctionDeclarations
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitFunctionDeclaration($node) { 
      $this->bytes.= 'function '.$node->name.'(';
      $embed= $this->emitParameters($node->parameters);
      $this->bytes.= ') {'.$embed;
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }
    
    /**
     * Emits native source for a given class, method and signature
     *
     * @param   string class
     * @param   string method
     * @param   net.xp_framework.tools.vm.ParameterNode[] parameters
     */
    public function emitNativeSourceFor($class, $method, $parameters) {
      $function= $class.'·'.$method;
      $this->cat && $this->cat->debug('Linking native source for', $class.'::'.$method.'()');
      
      $tokens= token_get_all(file_get_contents(str_replace('.xp', '.native.php5', $this->getFileName())));
      
      // Skip until we find the function
      $s= sizeof($tokens);
      while (T_FUNCTION != $tokens[$i][0] || $function != $tokens[$i+ 2][1]) { 
        if ($i++ < $s) continue;

        $this->addError(new CompileError(8000, 'Cannot find native method implementation '.$function.'()'));
        return;
      }
      
      // Record all args
      $args= array();
      $n= 0;
      while ('{' != $tokens[$i][0] && $i++ < $s) {
        T_VARIABLE == $tokens[$i][0] && $args[$tokens[$i][1]]= $parameters[$n++]->name;
      }
      
      // Check signature
      if ($n != sizeof($parameters)) {
        $this->addError(new CompileError(8001, sprintf(
          'Native method %s() signature mismatch (have: %d, expect: %d)',
          $function,
          $n,
          sizeof($parameters)
        )));
        return;
      }
      
      // Copy function body
      $brackets= 0;
      do {
        switch ($tokens[$i][0]) {
          case '{': $brackets++; break;
          case '}': $brackets--; if (0 == $brackets) break 2; break;
          case T_VARIABLE: $this->bytes.= isset($args[$tokens[$i][1]]) ? $args[$tokens[$i][1]] : $tokens[$i][1]; continue 2;
        }
        $this->bytes.= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i][0];  
      } while ($i++ < $s);
      $this->bytes.= '}';
    }

    /**
     * Emits MethodDeclarations
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitMethodDeclaration($node) {
      $method= $this->methodName($node);
      $this->setType($this->context['class'].'::'.$method, $this->typeName($node->returns));
      $this->context['method']= $method;
      $this->context['classes'][$this->context['class']][$method]= TRUE; // XXX DECL?
      
      $this->emitAnnotations($node->annotations);

      $this->bytes.= implode(' ', Modifiers::namesOf($node->modifiers)).' function '.$method.'(';
      
      // Method arguments
      $embed= $this->emitParameters($node->parameters);
      $this->bytes.= ')';

      // Method body
      if (NULL === $node->statements) {
        if ($node->modifiers & MODIFIER_NATIVE) {
          $this->emitNativeSourceFor($this->context['class'], $method, $node->parameters);
        } else {
          $this->bytes.= ';';   // TODO: May only be true if in interface or if abstract
        }
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
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitConstructorDeclaration($node) { 
      $method= $this->methodName($node);
      $this->context['method']= $method;
      $this->setType($this->context['class'].'::'.$method, $this->context['class']);
      
      $this->bytes.= implode(' ', Modifiers::namesOf($node->modifiers)).' function '.$method.'(';
      $embed= $this->emitParameters($node->parameters);
      $this->bytes.= ')';

      if ($node->statements !== NULL) {
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
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitDestructorDeclaration($node) { 
      $method= '__destruct';
      $this->context['method']= $method;
      $this->setType($this->context['class'].'::'.$method, $this->context['class']);
      
      $this->bytes.= implode(' ', Modifiers::namesOf($node->modifiers)).' function '.$method.'(';
      $embed= $this->emitParameters($node->parameters);
      $this->bytes.= ')';

      if ($node->statements !== NULL) {
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
    public function lookupClass($q) {
      if (!isset($this->context['classes'][$q])) {
      
        // Search classpath
        $filename= strtr($q, array(
          'main·' => '',
          '·'     => DIRECTORY_SEPARATOR
        ));
        foreach (array_merge(
          explode(PATH_SEPARATOR, CLASSPATH),
          array(dirname($this->getFilename()))
        ) as $node) {
          $in= $node.DIRECTORY_SEPARATOR.$filename.'.class.xp';
          if (!file_exists($in)) continue;
          
          $t= new Timer();
          $this->cat && $this->cat->info('Compiling', $q);
          
          // Found the file, tokenize, parse and emit it.
          $t->start();
          $lexer= new Lexer(file_get_contents($in), $in);
          
          // XXX TODO XXX Error handling
          $parser= new Parser($lexer);
          $nodes= $parser->parse($lexer);

          $t->stop();          
          $parse= $t->elapsedTime();
          
          // XXX TODO XXX Error handling
          $t->start();
          $emitter= new Php5Emitter();
          $emitter->setTrace($this->cat);
          $emitter->setFilename($in);
          $emitter->context['classes']= $this->context['classes'];
          $emitter->emitAll($nodes);
          
          $t->stop();   
          $emit= $t->elapsedTime();
          
          if ($emitter->hasErrors()) foreach ($emitter->getErrors() as $err) {
            $this->addError($err);
            return NULL;
          }

          // XXX TODO XXX Error handling
          // FileUtil::setContents(new File(str_replace('.xp', '.php', $in)), $emitter->getResult());

          // XXX TODO Merge not only classes but rest, too...
          foreach (array_keys($emitter->context['classes']) as $merge) {
            $this->context['classes'][$merge]= $emitter->context['classes'][$merge];
            $this->context['operators'][$merge]= $emitter->context['operators'][$merge];
          }

          // Remember we compiled this from an external file
          $this->cat && $this->cat->infof(
            'Emit<%s>: Compiled %s (parse: %.3f seconds, emit: %.3f seconds)',
            $this->getFilename(),
            $q,
            $parse,
            $emit
          );
         
          $this->context['uses'][strtr($q, array(
            'main'  => dirname($this->getFilename()),
            '·'     => '.'    // NAMESPACE_SEPARATOR_IN_USES_STATEMENTS
          ))]= $in;
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
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitClassDeclaration($node) {
      $this->context['properties']= array();
      $class= $this->qualifiedName($node->name, FALSE);
      $this->setContextClass($class);
      $extends= $this->qualifiedName($node->extends ? $node->extends : 'lang.Object');
      $this->context['operators'][$this->context['class']]= array();

      $this->emitAnnotations($node->annotations);
      
      $node->modifiers & MODIFIER_ABSTRACT && $this->bytes.= 'abstract ';
      $node->modifiers & MODIFIER_FINAL && $this->bytes.= 'final ';

      $this->bytes.= 'class '.$this->context['class'].('lang·Object' == $class ? '' : ' extends '.$extends);

      // Copy members from parent class
      if (NULL === ($parent= $this->lookupClass($extends))) {
        $this->addError(new CompileError(1000, 'Parent class of '.$node->name.' ('.$extends.') does not exist'));
      }
      $this->context['classes'][$this->context['class']]= $parent;
      
      // Add inheritance chain
      $this->context['classes'][$this->context['class']][0]= $extends;
      
      // Interfaces
      if ($node->interfaces) {
        $this->context['classes'][$this->context['class']][1]= array();
        $this->bytes.= ' implements ';
        foreach ($node->interfaces as $name) {
          $interface= $this->qualifiedName($name);
          
          if (NULL === $this->lookupClass($interface)) {
            $this->cat && $this->cat->error(
              'Interface:', $extends, 'does not exist,',
              'declared=', implode(', ', array_keys($this->context['classes']))
            );

            $this->addError(new CompileError(1001, 'Interface '.$interface.' implemented by '.$node->name.' does not exist'));
          }
          
          $this->context['classes'][$this->context['class']][1][$interface]= TRUE;
          $this->bytes.= $interface.', ';
        }
        $this->bytes= substr($this->bytes, 0, -2);
      }

      // Copy types from parent class. Might be overwritten later on
      foreach ($this->context['types'] as $k => $type) {
        if (0 === strpos($k, $extends)) {
          $n= $this->context['class'].substr($k, strlen($extends));
          $this->setType($n, $type);
        }
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
          public function __get($name) {
            if (!isset(self::$__properties[$name])) die(\'Read of non-existant property "\'.$name.\'"\');
            if (NULL === self::$__properties[$name][0]) {
              throw new lang·IllegalAccessException(\'Cannot access property "\'.$name.\'"\');
            } else if (\'$\' == self::$__properties[$name][0][0]) {
              return $this->{substr(self::$__properties[$name][0], 1)};
            } else {
              return $this->{self::$__properties[$name][0]}();
            }
          }

          public function __set($name, $value) {
            if (!isset(self::$__properties[$name])) die(\'Write of non-existant property "\'.$name.\'"\');
            if (NULL === self::$__properties[$name][1]) {
              throw new lang·IllegalAccessException(\'Cannot access property "\'.$name.\'"\');
            } else if (\'$\' == self::$__properties[$name][1][0]) {
              $this->{substr(self::$__properties[$name][1], 1)}= $value;
            } else {
              $this->{self::$__properties[$name][1]}($value);
            }
          }
        ';
      }

      $this->bytes.= '}';
      
      // Check interface implementations if this class is not abstract
      if (!($node->modifiers & MODIFIER_ABSTRACT)) {
        foreach ($node->interfaces as $interface) {
          $this->checkImplementation($this->context['class'], $this->qualifiedName($interface));
        }
      }

      $this->setContextClass('<main>');
    }

    /**
     * Emits FunctionCalls
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitFunctionCall($node) {
    
      // Check for mapping - TBD: Use functioncall-mapper-objects?
      if (method_exists($this, $mapped= 'emit'.$node->name.'FunctionCall')) {
        $this->{$mapped}($node);
        return;
      }

      $this->bytes.= $node->name.'(';
      foreach ($node->arguments as $arg) {
        $this->emit($arg);
        $this->bytes.= ', ';
      }
      $node->arguments && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ')';
    }
    
    /**
     * Emits exit() FunctionCalls
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitExitFunctionCall($node) {
      $this->bytes.= 'throw new lang·SystemExit(';
      switch (sizeof($node->arguments)) {
        case 0: break;
        case 1: $this->emit($node->arguments[0]); break;
        default: $this->addError(new CompileError(4000, 'Wrong number of arguments to exit()'));
      }
      $this->bytes.= ')';
    }
    
    public function emitMemberName($name) {
      if (is_string($name)) {          // $test->member;
        $this->bytes.= $name;
        return $name;
      } else if (is_array($name)) {    // $test->{$member};
        $this->bytes.= '{';
        $this->emit($name[0]);
        $this->bytes.= '}';
        // TODO: $this->addError(new CompilerMessage('...');
        return '${}';
      } else {
        $this->addError(new CompileError(12, 'Unknown member node '.xp::stringOf($name)));
      }
    }

    /**
     * Emits Members
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitMember($node) {
      $this->emitMemberName($node->name);
      if (NULL === $node->offset) return;

      $this->bytes.= '[';
      $this->emit($node->offset);
      $this->bytes.= ']';
    }

    /**
     * Emits MethodCalls
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitMethodCall($node) {
      if (is_string($node->class)) {      // Static
        $this->bytes.= $this->qualifiedName($node->class).'::';
        $type= $this->qualifiedName($node->class);
      } else if ($node->class) {          // Instance
        $this->emit($node->class);    
        $type= $this->typeOf($node->class);
        $this->bytes.= '->';
      } else {                            // Chains
        $type= $this->context['class'];   
        $this->bytes.= '->';
      }

      // Calculate method name to invoke. Note method overloading
      // does not work with dynamic method calls!
      $overloaded= '';
      if (is_string($node->method->name) && isset($this->context['overloaded'][$type.'::'.$node->method->name])) {
        foreach ($node->arguments as $arg) $overloaded.= $this->typeOf($arg);
      }
      
      // Signature
      $name= $this->emitMemberName($node->method->name);
      $this->bytes.= $overloaded.'(';
      $vartype= NULL;
      for ($i= 0; $i < sizeof($node->arguments); $i++) {
      
        // Check if the argument is vararg argument.
        if (!$vartype) {
          $argtype= $this->context['types'][$type.'::'.$node->method->name.'@'.$i];
          '*' == substr($argtype, -1) && $argtype= $vartype= substr($argtype, 0, -1);
        } else {
          $argtype= $vartype;
        }

        $this->checkedType($node->arguments[$i], $argtype);
        $this->emit($node->arguments[$i]);
        $this->bytes.= ', ';
      }
      
      // Pass default args
      if (!empty($this->context['default'][$type.'::'.$name])) {
        for ($defaults= $this->context['default'][$type.'::'.$name]; $i <= max(array_keys($defaults)); $i++) {
          $this->emit($defaults[$i]);
          $this->bytes.= ', ';
        }
      }
      
      $this->bytes= rtrim($this->bytes, ', ').')';
 
      // Chain: $x->getClass()->getName()
      if (!$node->chain) return;
      
      $cclass= $this->context['class'];   // backup
      $this->setContextClass($this->context['types'][$type.'::'.$node->method->name]);
      foreach ($node->chain as $chain) {
        $this->emit($chain);
        $this->setContextClass($this->typeOf($chain));
      }
      $this->setContextClass($cclass);    // restore
    }

    /**
     * Emits Nots
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitNot($node) { 
      $this->bytes.= '!';
      $this->emit($node->expression);
    }

    /**
     * Emits ObjectReferences
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitObjectReference($node) { 
      $node->class && $this->emit($node->class);
      $this->bytes.= '->';
      $this->emit($node->member);

      if (!$node->chain) return;
      
      $cclass= $this->context['class'];   // backup
      $node->class && $this->setContextClass($this->typeOf($node->member));
      foreach ($node->chain as $chain) {
        $this->emit($chain);
        $this->setContextClass($this->typeOf($chain));
      }
      $this->setContextClass($cclass);    // restore
    }
    
    public function mappedOperator($op) {
      return ('~' == $op{0}) ? '.' : $op;
    }

    /**
     * Emits Unaries
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitUnary($node) {
      $this->bytes.= $this->mappedOperator($node->op);
      $this->emit($node->expression);
    }

    /**
     * Emits Binaries
     *
     * Caution: left can be empty!
     * <pre>
     *   + [expr]= BinaryNode(NULL, '-', expr)
     *   left + right = BinaryNode(left, '+', right)
     * </pre>
     *
     * XXX Optimization possibility: $i = 1 + 2 can be optimized to $i = 3; XXX
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitBinary($node) {
      if ($node->left) {
        $type= $this->typeOf($node->left);

        // Check for operator overloading
        if (isset($this->context['operators'][$type][$node->op])) {
          
          // Check for comparison operator
          if (1 === $this->context['operators'][$type][$node->op]) {
            $this->bytes.= '(0 '.$node->op.' -1 * ';
            $this->emit(new MethodCallNode(
              $type, 
              new MemberNode('__operator'.$this->overloadable[$node->op]),
              array($node->left, $node->right)
            ));
            $this->bytes.= ')';
            return;
          }
          
          // Regular operator
          return $this->emit(new MethodCallNode(
            $type, 
            new MemberNode('__operator'.$this->overloadable[$node->op]),
            array($node->left, $node->right)
          ));
        }

        // Regular operator
        $this->emit($node->left);
      }
      
      $this->bytes.= $this->mappedOperator($node->op);
      $this->emit($node->right);
    }

    /**
     * Emits Variables
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitVariable($node) { 
      $this->bytes.= $node->name;
      if (NULL === $node->offset) return;

      $this->bytes.= '[';
      $this->emit($node->offset);
      $this->bytes.= ']';
    }
    
    /**
     * Emits Assigns
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitAssign($node) {
      $this->emit($node->variable);
      $this->bytes.= '= ';
      $this->emit($node->expression);
      $this->setType($this->scopeFor($node->variable), $this->checkedType($node->variable, $this->typeOf($node->expression)));
    }

    /**
     * Emits BinaryAssigns
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitBinaryAssign($node) { 
      $type= $this->typeOf($node->variable);
      $this->emit($node->variable);

      // Check for operator overloading
      if (isset($this->context['operators'][$type][$node->op])) {
        $this->bytes.= '= ';
        $m= new MethodCallNode(
          $type, 
          new MemberNode('__operator'.$this->overloadable[$node->op]),
          array($node->variable, $node->expression)
        );

        $this->setType($this->context['class'].'::'.$this->context['method'].$node->variable->name, $this->typeOf($m));
        return $this->emit($m);
      }

      $this->bytes.= $this->mappedOperator($node->op).'= ';
      $this->emit($node->expression);
      $this->setType($this->scopeFor($node->variable), $this->typeOf($node->expression));
    }

    /**
     * Emits Ifs
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitIf($node) { 
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
     * Emits New nodes
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitNew($node) {

      // Check if we find an array offset somewhere, e.g. new ArrayList()->getElements()[0]
      // This will need to be translated to xp::element(new ArrayList(), 0)
      if ($node->instanciation->chain) foreach ($node->instanciation->chain as $i => $expr) {
        if ($expr instanceof ArrayOffsetNode) {
          $this->bytes.= 'xp::wraparray(';
          $node->instanciation->chain[$i]->first= TRUE;
          break;
        }
      }

      if ($node->instanciation->declaration) {
        $this->bytes.= 'newinstance(\''.$this->qualifiedName($node->class->name).'\', array(';
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
        $node->instanciation->chain && $this->bytes.= 'create(';
        
        if (isset($this->context['overloaded'][$this->qualifiedName($node->class->name).'::__construct'])) {
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
        
        $cclass= $this->context['class'];   // backup
        $this->setContextClass($this->qualifiedName($node->class->name));
        foreach ($node->instanciation->chain as $chain) {
          $this->emit($chain);
          $this->setContextClass($this->typeOf($chain));
        }
        
        $this->setContextClass($cclass);    // restore
      }
    }
    
    /**
     * Emits ImportLists
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitImportList($node) {
      foreach ($node->list as $import) { 
        $this->emit($import);
      }
    }
    
    /**
     * Emits Import
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitImport($node) {
      $source= $node->source->name;
      if (NULL === $this->lookupClass($this->qualifiedName($source, FALSE))) {
        $this->addError(new CompileError(7000, 'Imported class '.$source.' does not exist'));
      }

      $destination= $node->destination;

      // Calculate destination name if none was supplied
      if (!$destination) {
        $destination= substr($source, strrpos($source, PACKAGE_SEPARATOR)+ 1);
      }

      // Register import
      $this->context['imports'][$this->context['package']][$destination]= $source;
    }

    /**
     * Emits ImportLists
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitImportAll($node) {
      $this->importAllOf($node->from);
    }    
    
    /**
     * Emits Trys
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitTry($node) { 
      $this->bytes.= "try {\n  ";
      $this->emitAll($node->statements);      

      // First catch
      $this->bytes.= '} catch ('.$this->qualifiedName($node->firstCatch->class).' '.$node->firstCatch->variable.') { ';
      
      // Emit statements in catch block, injecting finally wherever necessary
      foreach ($node->firstCatch->statements as $stmt) {
        if ($stmt instanceof ReturnNode || $stmt instanceof ThrowNode) {
          $node->finallyBlock && $this->emitAll($node->finallyBlock->statements);
        }
      
        $this->emit($stmt);
        $this->bytes.= ';';
      }
      
      // Additional catches
      foreach ($node->firstCatch->catches as $catch) {
        $this->bytes.= '} catch ('.$this->qualifiedName($catch->class).' '.$catch->variable.') { ';

        // Emit statements in catch block, injecting finally wherever necessary
        foreach ($catch->statements as $stmt) {
          if ($stmt instanceof ReturnNode || $stmt instanceof ThrowNode) {
            $node->finallyBlock && $this->emitAll($node->finallyBlock->statements);
          }

          $this->emit($stmt);
          $this->bytes.= ';';
        }
      }
      
      $this->bytes.= '}';
      
      $node->finallyBlock && $this->emitAll($node->finallyBlock->statements);
    }

    /**
     * Emits Echos
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitEcho($node) {
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
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitReturn($node) { 
      $this->bytes.= 'return ';
      if (!$node->value) return;
      
      // Check for void methods
      // The main block may contain returns with arbitrary type
      if ('<main>' !== $this->context['method'] && 'void' === $this->context['types'][$this->context['class'].'::'.$this->context['method']]) {
        $this->addError(new CompileError(3002, sprintf(
          'Method %s() declared void but returns %s type',
          $this->context['class'].'::'.$this->context['method'],
          $this->typeOf($node->value)
        )));
        return;
      }

      $this->checkedType(
        $node->value, 
        $this->context['types'][$this->context['class'].'::'.$this->context['method']]
      );
      $this->emit($node->value);
    }

    /**
     * Emits Ternarys
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitTernary($node) {
      $this->emit($node->condition);
      $this->bytes.= ' ? ';
      $this->emit($node->expression);
      $this->bytes.= ' : ';
      $this->emit($node->conditional);
    }

    /**
     * Emits Fors
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitFor($node) {
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
     * Emits PreIncs
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitPreInc($node) {
      $this->bytes.= '++';
      $this->emit($node->expression);
    }

    /**
     * Emits PostIncs
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitPostInc($node) {
      $this->emit($node->expression);
      $this->bytes.= '++';
    }

    /**
     * Emits PreDecs
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitPreDec($node) {
      $this->bytes.= '--';
      $this->emit($node->expression);
    }

    /**
     * Emits PostDecs
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitPostDec($node) {
      $this->emit($node->expression);
      $this->bytes.= '--';
    }

    /**
     * Emits MemberDeclarationLists
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitMemberDeclarationList($node) { 
      $bytes= $this->bytes;
      $this->bytes= '';

      $members= FALSE;
      foreach ($node->members as $member) {
        $this->setType($this->context['class'].'::'.$member->name, $this->typeName($node->type));
        if ($member instanceof PropertyDeclarationNode) {
          $this->context['properties'][]= $member;
        } else {
          $this->bytes.= $member->name.'= ';
          $member->initial === NULL ? $this->bytes.= 'NULL' : $this->emit($member->initial);
          $this->bytes.= ', ';
          $members= TRUE;
        }
      }
      $this->bytes= $bytes.($members ? implode(' ', Modifiers::namesOf($node->modifiers)).' '.substr($this->bytes, 0, -2).';' : '');
    }

    /**
     * Emits OperatorDeclarations
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitOperatorDeclaration($node) {       
      $method= '__operator'.$this->overloadable[$node->name];
      $this->context['method']= $method;

      if ('__compare' == $node->name) {   // <=> overloads all comparision operators
        foreach (array('==', '!=', '<=', '>=', '<', '>') as $op) {
          $this->context['operators'][$this->context['class']][$op]= 1;
        }
        $type= 'int';   // Returns -1, 0 or 1
      } else {
        $this->context['operators'][$this->context['class']][$node->name]= TRUE;
        $type= $this->context['class'];
      }
      $this->setType($this->context['class'].'::'.$method, $type);

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
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitThrow($node) {       
      $this->bytes.= 'throw ';
      $this->emit($node->value);
      $this->bytes.= '';
    }

    /**
     * Emits InstanceOfs
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitInstanceOf($node) {
      $this->emit($node->object);
      $this->bytes.= ' instanceof ';
      $this->emit($node->type);
    }

    /**
     * Emits ClassReferences
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitClassReference($node) {
      $this->bytes.= $this->qualifiedName($node->name);
    }

    /**
     * Emits InterfaceDeclarations
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitInterfaceDeclaration($node) {
      $this->setContextClass($this->qualifiedName($node->name, FALSE));
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
        if ($stmt instanceof MethodDeclarationNode) {
          $this->context['classes'][$this->context['class']][$this->methodName($stmt)]= TRUE; // XXX DECL?
        }
      }
      $this->bytes.= '}';
      $this->setContextClass('<main>');
    }

    /**
     * Emits Whiles
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitWhile($node) { 
      $this->bytes.= 'while (';
      $this->emit($node->condition);
      $this->bytes.= ') {';
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }

    /**
     * Emits Do ... Whiles
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitDoWhile($node) {
      $this->bytes.= 'do {';
      $this->emitAll($node->statements);
      $this->bytes.= '} while (';
      $this->emit($node->condition);
      $this->bytes.= ')';
    }

    /**
     * Emits Foreachs
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitForeach($node) {
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
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitSwitch($node) {
      $this->bytes.= 'switch (';
      $this->emit($node->condition);
      $this->bytes.= ') {';
      $this->emitAll($node->cases);
      $this->bytes.= '}';
    }

    /**
     * Emits Cases
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitCase($node) {
      $this->bytes.= 'case ';
      $this->emit($node->expression);
      $this->bytes.= ': ';
      $this->emitAll($node->statements);
    }

    /**
     * Emits Defaults
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitDefault($node) { 
      $this->bytes.= 'default: ';
      $this->emitAll($node->statements);
    }

    /**
     * Emits Breaks
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitBreak($node) { 
      $this->bytes.= 'break';
      if ($node->expression) {
        $this->bytes.= ' ';
        $this->emit($node->expression);
      }
    }

    /**
     * Emits Continues
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitContinue($node) {
      $this->bytes.= 'continue';
      if ($node->expression) {
        $this->bytes.= ' ';
        $this->emit($node->expression);
      }
    }

    /**
     * Emits static variables list
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitStaticVariableList($node) {
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
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitStaticVariable($node) {
      $this->bytes.= $node->name;
      if (NULL !== $node->initial) {
        $this->bytes.= '= ';
        $this->emit($node->initial);
      }
    }

    /**
     * Emits constant references
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitConstantReference($node) {
      $this->bytes.= ($node->class ? $node->class.'::' : '').$node->name;
    }

    /**
     * Emits constant references
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitBooleanOperator($node) {
      $this->emit($node->left);
      $this->bytes.= ' '.$node->op.' ';
      $this->emit($node->right);
    }

    /**
     * Emits array offsets (used for getElements()[0])
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitArrayOffset($node) {
      $node->first && $this->bytes.= ')->backing';
      $this->bytes.= '[';
      $this->emit($node->expression);
      $this->bytes.= ']';
    }

    /**
     * Emits expression casts
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitExpressionCast($node) {
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
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitBracketedExpression($node) {
      $this->bytes.= '(';
      $this->emit($node->expression);
      $this->bytes.= ')';
    }

    /**
     * Emits silenced expressions ("@" expression)
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitSilencedExpression($node) {
      $this->bytes.= '@';
      $this->emit($node->expression);
    }

    /**
     * Emits array access
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitArrayAccess($node) {
      $this->emit($node->expression);
      $this->bytes.= '[';
      $node->offset && $this->emit($node->offset);
      $this->bytes.= ']';
    }

    /**
     * Emits static members
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitStaticMember($node) {
      $this->bytes.= $this->qualifiedName($node->class).'::';
      $this->emit($node->member);
    }

    /**
     * Emits list()-assignment
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitListAssign($node) {
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
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitDoubleNumber($node) {
      $this->bytes.= $node->value;
    }

    /**
     * Emits a long number
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitLongNumber($node) {
      if ('0' == $node->value{0} && strlen($node->value) > 1) {
        $this->bytes.= 'x' == $node->value{1} ? hexdec($node->value) : octdec($node->value);
      } else {
        $this->bytes.= $node->value;
      }
    }

    /**
     * Emits an array declaration
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitArrayDeclaration($node) { 
      $this->bytes.= 'array(';
      foreach ($node->elements as $key => $val) {
        $this->emit($key);
        $this->bytes.= ' => ';
        $this->emit($val);
        $this->bytes.= ', ';
      }
      $this->bytes.= ')';
    }

    /**
     * Emits clone
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitClone($node) { 
      $this->bytes.= 'clone ';
      $this->emit($node->expression);
    }

    /**
     * Emits static initializer block
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emitStaticInitializer($node) { 
      $this->bytes.= 'static function __static() {';
      $this->emitAll($node->block);
      $this->bytes.= '}';
    }
  }
?>
