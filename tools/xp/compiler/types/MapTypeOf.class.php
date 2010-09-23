<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.types.Types');

  /**
   * Represents a map type
   *
   * @test  xp://net.xp_lang.tests.types.MapTypeOfTest
   */
  class MapTypeOf extends Types {
    protected $component= NULL;
    
    /**
     * Constructor
     *
     * @param   xp.compiler.types.Types key
     * @param   xp.compiler.types.Types value
     */
    public function __construct(Types $component) {
      $this->component= $component;
    }
    
    /**
     * Returns name
     *
     * @return  string
     */
    public function name() {
      return '[:'.$this->component->name().']';
    }

    /**
     * Returns parent type
     *
     * @return  xp.compiler.types.Types
     */
    public function parent() {
      return new self($this->component->parent());
    }

    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function literal() {
      return 'array';
    }

    /**
     * Returns type kind (one of the *_KIND constants).
     *
     * @return  string
     */
    public function kind() {
      return $this->component->kind();
    }

    /**
     * Checks whether a given type instance is a subclass of this class.
     *
     * @param   xp.compiler.types.Types
     * @return  bool
     */
    public function isSubclassOf(Types $t) {
      return $t instanceof self && $this->component->isSubclassOf($t->component);
    }

    /**
     * Returns whether this type is enumerable (that is: usable in foreach)
     *
     * @return  bool
     */
    public function isEnumerable() {
      return TRUE;
    }

    /**
     * Returns the enumerator for this class or NULL if none exists.
     *
     * @see     php://language.oop5.iterations
     * @return  xp.compiler.types.Enumerator
     */
    public function getEnumerator() {
      $e= new xp·compiler·types·Enumerator();
      $e->key= new TypeName('string');
      $e->value= new TypeName($this->component->name());
      return $e;
    }

    /**
     * Returns whether a constructor exists
     *
     * @return  bool
     */
    public function hasConstructor() {
      return FALSE;
    }

    /**
     * Returns the constructor
     *
     * @return  xp.compiler.types.Constructor
     */
    public function getConstructor() {
      return NULL;
    }

    /**
     * Returns whether a method with a given name exists
     *
     * @param   string name
     * @return  bool
     */
    public function hasMethod($name) {
      return FALSE;
    }

    /**
     * Returns a method by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Method
     */
    public function getMethod($name) {
      return NULL;
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
      return FALSE;
    }
    
    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Field
     */
    public function getField($name) {
      return NULL;
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
     * Returns whether this class has an indexer
     *
     * @return  bool
     */
    public function hasIndexer() {
      return TRUE;
    }

    /**
     * Returns indexer
     *
     * @return  xp.compiler.types.Indexer
     */
    public function getIndexer() {
      $i= new xp·compiler·types·Indexer();
      $i->parameter= new TypeName('string');
      $i->type= new TypeName($this->component->name());
      return $i;
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
      return sprintf(
        '%s@([:%s]>)',
        $this->getClassName(),
        $this->component->name()
      );
    }
  }
?>
