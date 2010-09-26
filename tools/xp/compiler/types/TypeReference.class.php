<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.types.Types', 'xp.compiler.types.TypeName');

  /**
   * A reference to a type
   *
   * @test    xp://net.xp_lang.tests.types.TypeReferenceTest
   */
  class TypeReference extends Types {
    protected $type= NULL;
    protected $kind= 0;
    
    /**
     * Constructor
     *
     * @param   xp.compiler.types.TypeName
     * @param   int kind
     */
    public function __construct(TypeName $type, $kind= parent::CLASS_KIND) {
      $this->type= $type;
      $this->kind= $kind;
    }

    /**
     * Returns parent type
     *
     * @return  xp.compiler.types.Types
     */
    public function parent() {
      return NULL;
    }
    
    /**
     * Returns name
     *
     * @return  string
     */
    public function name() {
      return $this->type->name;
    }

    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function literal() {
      $p= strrpos($this->type->name, '.');
      return FALSE === $p ? $this->type->name : substr($this->type->name, $p+ 1);
    }

    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function kind() {
      return $this->kind;
    }

    /**
     * Checks whether a given type instance is a subclass of this class.
     *
     * @param   xp.compiler.types.Types
     * @return  bool
     */
    public function isSubclassOf(Types $t) {
      return FALSE;
    }

    /**
     * Returns whether this type is enumerable (that is: usable in foreach)
     *
     * @return  bool
     */
    public function isEnumerable() {
      return $this->type->isArray() || $this->type->isMap();
    }

    /**
     * Returns the enumerator for this class or NULL if none exists.
     *
     * @see     php://language.oop5.iterations
     * @return  xp.compiler.types.Enumerator
     */
    public function getEnumerator() {
      if ($this->type->isArray()) {
        $e= new xp·compiler·types·Enumerator();
        $e->key= new TypeName('int');
        $e->value= $this->type->arrayComponentType();
        $e->holder= $this;  
        return $e;
      } else if ($this->type->isMap()) {
        $e= new xp·compiler·types·Enumerator();
        $e->key= new TypeName('string');
        $e->value= $this->type->mapComponentType();
        $e->holder= $this;  
        return $e;
      }

      return NULL;
    }

    /**
     * Returns whether this class has an indexer
     *
     * @return  bool
     */
    public function hasIndexer() {
      return $this->type->isArray() || $this->type->isMap();
    }

    /**
     * Returns indexer
     *
     * @return  xp.compiler.types.Indexer
     */
    public function getIndexer() {
      if ($this->type->isArray()) {
        $i= new xp·compiler·types·Indexer();
        $i->type= $this->type->arrayComponentType();
        $i->parameter= new Typename('int');
        $i->holder= $this;
        return $i;
      } else if ($this->type->isMap()) {
        $i= new xp·compiler·types·Indexer();
        $i->type= $this->type->mapComponentType();
        $i->parameter= new Typename('string');
        $i->holder= $this;
        return $i;
      }
      return NULL;
    }

    /**
     * Returns whether a constructor exists
     *
     * @return  bool
     */
    public function hasConstructor() {
      return TRUE;
    }

    /**
     * Returns the constructor
     *
     * @return  xp.compiler.types.Constructor
     */
    public function getConstructor() {
      $c= new xp·compiler·types·Constructor();
      $c->parameters= array();
      $c->holder= $this;
      return $c;
    }

    /**
     * Returns a method by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasMethod($name) {
      return TRUE;
    }
    
    /**
     * Returns a method by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Method
     */
    public function getMethod($name) {
      $m= new xp·compiler·types·Method();
      $m->name= $name;
      $m->returns= TypeName::$VAR;
      $m->parameters= array();
      $m->holder= $this;
      return $m;
    }

    /**
     * Returns whether an operator by a given symbol exists
     *
     * @param   string symbol
     * @return  bool
     */
    public function hasOperator($symbol) {
      return FALSE;
    }
    
    /**
     * Returns an operator by a given name
     *
     * @param   string symbol
     * @return  xp.compiler.types.Operator
     */
    public function getOperator($symbol) {
      return NULL;
    }

    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasField($name) {
      return TRUE;
    }
    
    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Field
     */
    public function getField($name) {
      $m= new xp·compiler·types·Field();
      $m->name= $name;
      $m->type= TypeName::$VAR;
      $m->holder= $this;
      return $m;
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
      return FALSE;
    }
    
    /**
     * Returns a constant by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Constant
     */
    public function getConstant($name) {
      return NULL;
    }

    /**
     * Returns a lookup map of generic placeholders
     *
     * @return  [:int]
     */
    public function genericPlaceholders() {
      return array();
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */    
    public function toString() {
      static $kinds= array(
        self::PRIMITIVE_KIND    => 'PRIMITIVE',
        self::CLASS_KIND        => 'CLASS',
        self::INTERFACE_KIND    => 'INTERFACE',
        self::ENUM_KIND         => 'ENUM',
        self::UNKNOWN_KIND      => 'UNKNOWN',
        self::PARTIAL_KIND      => 'PARTIAL'
      );
      return $this->getClassName().'<'.$kinds[$this->kind].'>@(*->'.$this->type->toString().')';
    }
  }
?>
