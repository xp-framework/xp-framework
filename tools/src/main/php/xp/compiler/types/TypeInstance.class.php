<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.types.Types');

  /**
   * Represents a type instance
   *
   * @test    xp://tests.types.TypeInstanceTest
   */
  class TypeInstance extends Types {
    protected $declaration= NULL;
    protected static $base= NULL;
    
    static function __static() {
      self::$base= XPClass::forName('lang.Object');
    }
    
    /**
     * Constructor
     *
     * @param   xp.compiler.types.Types declaration
     */
    public function __construct(Types $declaration) {
      $this->declaration= $declaration;
    }

    /**
     * Returns name
     *
     * @return  string
     */
    public function name() {
      return $this->declaration->name();
    }

    /**
     * Returns parent type
     *
     * @return  xp.compiler.types.Types
     */
    public function parent() {
      return $this->declaration->parent();
    }

    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function literal() {
      return $this->declaration->literal();
    }

    /**
     * Returns type kind (one of the *_KIND constants).
     *
     * @return  string
     */
    public function kind() {
      return $this->declaration->kind();
    }

    /**
     * Checks whether a given type instance is a subclass of this class.
     *
     * @param   xp.compiler.types.Types
     * @return  bool
     */
    public function isSubclassOf(Types $t) {
      return $this->declaration->isSubclassOf($t);
    }

    /**
     * Returns whether this type is enumerable (that is: usable in foreach)
     *
     * @return  bool
     */
    public function isEnumerable() {
      return $this->declaration->isEnumerable();
    }

    /**
     * Returns the enumerator for this class or NULL if none exists.
     *
     * @see     php://language.oop5.iterations
     * @return  xp.compiler.types.Enumerator
     */
    public function getEnumerator() {
      return $this->declaration->getEnumerator();
    }

    /**
     * Returns whether a constructor exists
     *
     * @return  bool
     */
    public function hasConstructor() {
      return $this->declaration->hasConstructor();
    }

    /**
     * Returns the constructor
     *
     * @return  xp.compiler.types.Constructor
     */
    public function getConstructor() {
      return $this->declaration->getConstructor();
    }

    /**
     * Returns whether a method with a given name exists
     *
     * @param   string name
     * @return  bool
     */
    public function hasMethod($name) {
      if ($this->declaration->hasMethod($name)) {
        return TRUE;
      } else if (Types::INTERFACE_KIND === $this->kind()) {
        return self::$base->hasMethod($name);
      }
      return FALSE;
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
     * Returns a method by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Method
     */
    public function getMethod($name) {
      if (NULL !== ($method= $this->declaration->getMethod($name))) {
        return $method;
      } else if (Types::INTERFACE_KIND === $this->kind() && self::$base->hasMethod($name)) {
        with ($method= self::$base->getMethod($name)); {
          $m= new xp·compiler·types·Method();
          $m->name= $method->getName();
          $m->returns= $this->typeNameOf($method->getReturnTypeName());
          $m->modifiers= $method->getModifiers();
          $m->parameters= array();
          foreach ($method->getParameters() as $p) {
            $m->parameters[]= $this->typeNameOf($p->getTypeName());
          }
        }
        $m->holder= $this;
        return $m;
      }
      return NULL;
    }

    /**
     * Returns whether an operator by a given symbol exists
     *
     * @param   string symbol
     * @return  bool
     */
    public function hasOperator($symbol) {
      return $this->declaration->hasOperator($symbol);
    }
    
    /**
     * Returns an operator by a given name
     *
     * @param   string symbol
     * @return  xp.compiler.types.Operator
     */
    public function getOperator($symbol) {
      return $this->declaration->getOperator($symbol);
    }

    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasField($name) {
      return $this->declaration->hasField($name);
    }
    
    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Field
     */
    public function getField($name) {
      return $this->declaration->getField($name);
    }

    /**
     * Returns a property by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasProperty($name) {
      return $this->declaration->hasProperty($name);
    }
    
    /**
     * Returns a property by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Property
     */
    public function getProperty($name) {
      return $this->declaration->getProperty($name);
    }

    /**
     * Returns a constant by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasConstant($name) {
      return $this->declaration->hasConstant($name);
    }
    
    /**
     * Returns a constant by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Constant
     */
    public function getConstant($name) {
      return $this->declaration->getConstant($name);
    }

    /**
     * Returns whether this class has an indexer
     *
     * @return  bool
     */
    public function hasIndexer() {
      return $this->declaration->hasIndexer();
    }

    /**
     * Returns indexer
     *
     * @return  xp.compiler.types.Indexer
     */
    public function getIndexer() {
      return $this->declaration->getIndexer();
    }

    /**
     * Returns a lookup map of generic placeholders
     *
     * @return  [:int]
     */
    public function genericPlaceholders() {
      return $this->declaration->genericPlaceholders();
    }
  }
?>
