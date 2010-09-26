<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.types.Types', 'xp.compiler.types.TypeName');

  /**
   * Represents a reflected type
   *
   * @test    xp://tests.types.TypeReflectionTest
   */
  class TypeReflection extends Types {
    protected $class= NULL;
    
    /**
     * Constructor
     *
     * @param   lang.XPClass class
     */
    public function __construct(XPClass $class) {
      $this->class= $class;
    }

    /**
     * Returns parent type
     *
     * @return  xp.compiler.types.Types
     */
    public function parent() {
      if ($parent= $this->class->getParentClass()) {
        return new self($parent);
      }
      return NULL;
    }
    
    /**
     * Returns name
     *
     * @return  string
     */
    public function name() {
      return $this->class->getName();
    }

    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function literal() {
      return xp::reflect($this->class->getName());
    }
    
    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function kind() {
      if ($this->class->isInterface()) {
        return parent::INTERFACE_KIND;
      } else if ($this->class->isEnum()) {
        return parent::ENUM_KIND;
      } else {
        return parent::CLASS_KIND;
      }
    }

    /**
     * Checks whether a given type instance is a subclass of this class.
     *
     * @param   xp.compiler.types.Types
     * @return  bool
     */
    public function isSubclassOf(Types $t) {
      try {
        return $this->class->isSubclassOf($t->name());
      } catch (ClassNotFoundException $e) {
        return FALSE;
      }
    }

    /**
     * Returns whether this type is enumerable (that is: usable in foreach)
     *
     * @see     php://language.oop5.iterations
     * @return  bool
     */
    public function isEnumerable() {
      return (
        $this->class->_reflect->implementsInterface('Iterator') || 
        $this->class->_reflect->implementsInterface('IteratorAggregate')
      );
    }

    /**
     * Returns the enumerator for this class or NULL if none exists.
     *
     * @see     php://language.oop5.iterations
     * @return  xp.compiler.types.Enumerator
     */
    public function getEnumerator() {
      if ($this->class->_reflect->implementsInterface('Iterator')) {
        $e= new xp·compiler·types·Enumerator();
        $e->key= new TypeName($it->getMethod('key')->getReturnTypeName());
        $e->value= new TypeName($it->getMethod('current')->getReturnTypeName());
        $e->holder= $this;  
        return $e;
      } else if ($this->class->_reflect->implementsInterface('IteratorAggregate')) {
        $it= $this->class->getMethod('getIterator')->getReturnTypeName();
        if (2 === sscanf($it, '%*[^<]<%[^>]>', $types)) {
          $components= explode(',', $types);
        } else {
          $components= array('var', 'var');
        }
        $e= new xp·compiler·types·Enumerator();
        $e->key= new TypeName(trim($components[0]));
        $e->value= new TypeName(trim($components[1]));
        $e->holder= $this; 
        return $e;
      }

      return NULL;
    }
    
    /**
     * Create a type name object from a type name string. Corrects old 
     * usages of the type name
     *
     * @param   string t
     * @return  xp.compiler.types.TypeName
     */
    protected function typeNameOf($t) {
      if ('mixed' === $t || '*' === $t || NULL === $t || 'resource' === $t) {
        return TypeName::$VAR;
      } else if (0 == strncmp($t, 'array', 5)) {
        return new TypeName('var[]');
      }
      return new TypeName($t);
    }

    /**
     * Returns whether a constructor exists
     *
     * @return  bool
     */
    public function hasConstructor() {
      return $this->class->hasConstructor();
    }
    
    /**
     * Returns the constructor
     *
     * @return  xp.compiler.types.Constructor
     */
    public function getConstructor() {
      if (!$this->class->hasConstructor()) return NULL;
      
      with ($constructor= $this->class->getConstructor()); {
        $c= new xp·compiler·types·Constructor();
        $c->modifiers= $constructor->getModifiers();
        $c->parameters= array();
        foreach ($constructor->getParameters() as $p) {
          $c->parameters[]= $this->typeNameOf($p->getTypeName());
        }
        $c->holder= $this;  
        return $c;
      }
    }

    /**
     * Returns a method by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasMethod($name) {
      return $this->class->hasMethod($name);
    }
    
    /**
     * Returns a method by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Method
     */
    public function getMethod($name) {
      if (!$this->class->hasMethod($name)) return NULL;

      with ($method= $this->class->getMethod($name)); {
        $m= new xp·compiler·types·Method();
        $m->name= $method->getName();
        $m->returns= $this->typeNameOf($method->getReturnTypeName());
        $m->modifiers= $method->getModifiers();
        $m->parameters= array();
        foreach ($method->getParameters() as $p) {
          $m->parameters[]= $this->typeNameOf($p->getTypeName());
        }
        $m->holder= $this;
        return $m;
      }
    }

    public static $ovl= array(
      '~'   => 'concat',
      '-'   => 'minus',
      '+'   => 'plus',
      '*'   => 'times',
      '/'   => 'div',
      '%'   => 'mod',
    );

    /**
     * Returns whether an operator by a given symbol exists
     *
     * @param   string symbol
     * @return  bool
     */
    public function hasOperator($symbol) {
      return $this->class->hasMethod('operator··'.self::$ovl[$symbol]);
    }
    
    /**
     * Returns an operator by a given name
     *
     * @param   string symbol
     * @return  xp.compiler.types.Operator
     */
    public function getOperator($symbol) {
      if (!$this->hasOperator($symbol)) return NULL;

      with ($method= $this->class->getMethod('operator··'.self::$ovl[$symbol])); {
        $m= new xp·compiler·types·Method();
        $m->name= $method->getName();
        $m->returns= $this->typeNameOf($method->getReturnTypeName());
        $m->modifiers= $method->getModifiers();
        $m->parameters= array();
        foreach ($method->getParameters() as $p) {
          $m->parameters[]= $this->typeNameOf($p->getTypeName());
        }
        $m->holder= $this;
        return $m;
      }
    }

    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasField($name) {
      return $this->class->hasField($name);
    }
    
    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Field
     */
    public function getField($name) {
      if (!$this->class->hasField($name)) return NULL;
      
      with ($field= $this->class->getField($name)); {
        $f= new xp·compiler·types·Field();
        $f->name= $field->getName();
        $f->modifiers= $field->getModifiers();
        $f->type= $this->typeNameOf($field->getType());
        $f->holder= $this;
        return $f;
      }
    }

    /**
     * Returns a property by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasProperty($name) {
      return FALSE;
    }
    
    /**
     * Returns a property by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Property
     */
    public function getProperty($name) {
      return NULL;
    }

    /**
     * Returns a constant by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasConstant($name) {
      return $this->class->hasConstant($name);
    }
    
    /**
     * Returns a constant by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Constant
     */
    public function getConstant($name) {
      if (!$this->class->hasConstant($name)) return NULL;
        
      with ($constant= $this->class->getConstant($name)); {
        $c= new xp·compiler·types·Constant();
        $c->name= $name;
        $c->type= new TypeName(Type::forName(gettype($constant))->getName());
        $c->value= $constant;
        $c->holder= $this;
        return $c;
      }
    }

    /**
     * Returns whether this class has an indexer
     *
     * @return  bool
     */
    public function hasIndexer() {
      return $this->class->_reflect->implementsInterface('ArrayAccess');
    }

    /**
     * Returns indexer
     *
     * @return  xp.compiler.types.Indexer
     */
    public function getIndexer() {
      if (!$this->class->_reflect->implementsInterface('ArrayAccess')) return NULL;

      with ($method= $this->class->getMethod('offsetGet')); {
        $i= new xp·compiler·types·Indexer();
        $i->type= $this->typeNameOf($method->getReturnTypeName());
        $i->parameter= $this->typeNameOf($method->getParameter(0)->getTypeName());
        $i->holder= $this;
        return $i;
      }
    }

    /**
     * Returns a lookup map of generic placeholders
     *
     * @return  [:int]
     */
    public function genericPlaceholders() {
      $lookup= array();
      foreach ($this->class->genericComponents() as $i => $name) {
        $lookup[$name]= $i;
      }
      return $lookup;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */    
    public function toString() {
      return $this->getClassName().'@('.$this->class->toString().')';
    }
  }
?>
