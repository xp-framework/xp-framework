<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.types.Types');

  /**
   * Represents a compiled type
   *
   * @test    xp://net.xp_lang.tests.types.CompiledTypeTest
   */
  class CompiledType extends Types {
    public $name= NULL;
    public $parent= NULL;
    public $literal= NULL;
    public $kind= NULL;
    public $indexer= NULL;
    public $constructor= NULL;
    public $methods= array();
    public $fields= array();
    public $operators= array();
    public $constants= array();
    public $properties= array();
    public $generics= NULL;

    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name= '') {
      $this->name= $name;
    }
    
    /**
     * Returns parent type
     *
     * @return  xp.compiler.types.Types
     */
    public function parent() {
      return $this->parent;
    }
    
    /**
     * Returns name
     *
     * @return  string
     */
    public function name() {
      return $this->name;
    }

    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function literal() {
      return $this->literal;
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
      return $this->parent !== NULL && ($this->parent->equals($t) || $this->parent->isSubclassOf($t));
    }

    /**
     * Returns whether this type is enumerable (that is: usable in foreach)
     *
     * @see     php://language.oop5.iterations
     * @return  bool
     */
    public function isEnumerable() {
      return FALSE; // TBI
    }

    /**
     * Returns the enumerator for this class or NULL if none exists.
     *
     * @see     php://language.oop5.iterations
     * @return  xp.compiler.types.Enumerator
     */
    public function getEnumerator() {
      return NULL;  // TBI
    }
    
    /**
     * Returns whether a constructor exists
     *
     * @return  bool
     */
    public function hasConstructor() {
      return NULL !== $this->constructor;
    }
    
    /**
     * Returns the constructor
     *
     * @return  xp.compiler.types.Constructor
     */
    public function getConstructor() {
      return $this->constructor;
    }

    /**
     * Adds a method
     *
     * @param   xp.compiler.types.Method method
     * @return  xp.compiler.types.Method the added method
     */
    public function addMethod(xp·compiler·types·Method $method) {
      $method->holder= $this;
      $this->methods[$method->name]= $method;
      return $method;
    }

    /**
     * Returns a method by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasMethod($name) {
      return isset($this->methods[$name]);
    }

    /**
     * Returns a method by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Method
     */
    public function getMethod($name) {
      return isset($this->methods[$name]) ? $this->methods[$name] : NULL;
    }

    /**
     * Adds an operator
     *
     * @param   xp.compiler.types.Operator operator
     * @return  xp.compiler.types.Operator the added operator
     */
    public function addOperator(xp·compiler·types·Operator $operator) {
      $operator->holder= $this;
      $this->operators[$operator->symbol]= $operator;
      return $operator;
    }

    /**
     * Returns whether an operator by a given symbol exists
     *
     * @param   string symbol
     * @return  bool
     */
    public function hasOperator($symbol) {
      return isset($this->operators[$symbol]);
    }
    
    /**
     * Returns an operator by a given name
     *
     * @param   string symbol
     * @return  xp.compiler.types.Operator
     */
    public function getOperator($symbol) {
      return isset($this->operators[$symbol]) ? $this->operators[$symbol] : NULL;
    }

    /**
     * Adds a field
     *
     * @param   xp.compiler.types.Field field
     * @return  xp.compiler.types.Field the added field
     */
    public function addField(xp·compiler·types·Field $field) {
      $field->holder= $this;
      $this->fields[$field->name]= $field;
      return $field;
    }

    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasField($name) {
      return isset($this->fields[$name]);
    }
    
    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Field
     */
    public function getField($name) {
      return isset($this->fields[$name]) ? $this->fields[$name] : NULL;
    }

    /**
     * Adds a property
     *
     * @param   xp.compiler.types.Property property
     * @return  xp.compiler.types.Property the added property
     */
    public function addProperty(xp·compiler·types·Property $property) {
      $property->holder= $this;
      $this->properties[$property->name]= $property;
      return $property;
    }

    /**
     * Returns a property by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasProperty($name) {
      return isset($this->properties[$name]);
    }
    
    /**
     * Returns a property by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Property
     */
    public function getProperty($name) {
      return isset($this->properties[$name]) ? $this->properties[$name] : NULL;
    }
    
    /**
     * Adds a constant
     *
     * @param   xp.compiler.types.Constant constant
     * @return  xp.compiler.types.Constant the added constant
     */
    public function addConstant(xp·compiler·types·Constant $constant) {
      $constant->holder= $this;
      $this->constants[$constant->name]= $constant;
      return $constant;
    }

    /**
     * Returns a constant by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasConstant($name) {
      return isset($this->constants[$name]); 
    }
    
    /**
     * Returns a constant by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Constant
     */
    public function getConstant($name) {
      return isset($this->constants[$name]) ? $this->constants[$name] : NULL;
    }

    /**
     * Returns whether this class has an indexer
     *
     * @return  bool
     */
    public function hasIndexer() {
      return NULL !== $this->indexer;
    }

    /**
     * Returns indexer
     *
     * @return  xp.compiler.types.Indexer
     */
    public function getIndexer() {
      return $this->indexer;
    }

    /**
     * Returns a lookup map of generic placeholders
     *
     * @return  [:int]
     */
    public function genericPlaceholders() {
      return $this->generics;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */    
    public function toString() {
      $s= $this->getClassName().'<'.$this->name.">@{\n";
      if ($this->constructor) {
        $s.= '  '.$this->constructor->toString()."\n";
      }
      foreach ($this->constants as $constant) {
        $s.= '  '.$constant->toString()."\n";
      }
      foreach ($this->fields as $field) {
        $s.= '  '.$field->toString()."\n";
      }
      foreach ($this->properties as $property) {
        $s.= '  '.$property->toString()."\n";
      }
      if ($this->indexer) {
        $s.= '  '.$this->indexer->toString()."\n";
      }
      foreach ($this->methods as $method) {
        $s.= '  '.$method->toString()."\n";
      }
      foreach ($this->operators as $operator) {
        $s.= '  '.$operator->toString()."\n";
      }
      return $s.'}';
    }
  }
?>
