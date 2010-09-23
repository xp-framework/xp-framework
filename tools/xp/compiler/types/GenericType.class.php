<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.types.Types');

  /**
   * Represents a generic type instance
   *
   * @test  xp://net.xp_lang.tests.types.GenericTypeTest
   */
  class GenericType extends Types {
    protected $definition= NULL;
    protected $components= array();
    protected $placeholders= array();
    
    /**
     * Constructor
     *
     * @param   xp.compiler.types.Types definition
     * @param   xp.compiler.types.TypeName[] components
     */
    public function __construct(Types $definition, array $components) {
      $this->definition= $definition;
      $this->components= $components;
      $this->placeholders= $this->definition->genericPlaceholders();
    }
    
    /**
     * Rewrite a placeholder for a real type
     *
     * Given the definition with Map<K, V> and components being [string, Object]
     * the rewriting should yield the following:
     * <pre>
     *   int        => int                    # Not a placeholder, leave as-is
     *   K          => string                 # Simple type
     *   V          => Object                 # Simple type
     *   K[]        => string[]               # Array type
     *   [:K]       => [:string]              # Map type
     *   List<K>    => List<string>           # Generic type
     *   Map<K, V>  => Map<string, Object>    # Generic type
     * </pre>
     *
     * @param   xp.compiler.types.TypeName
     * @return  xp.compiler.types.TypeName
     */
    public function rewrite(TypeName $type) {
      if ($type->isArray()) {
        return new TypeName($this->rewrite($type->arrayComponentType())->name.'[]');
      } else if ($type->isMap()) {
        return new TypeName('[:'.$this->rewrite($type->mapComponentType())->name.']');
      } else if ($type->isGeneric()) {
        return new TypeName($type->name, $this->rewriteAll($type->components));
      } else if (isset($this->placeholders[$type->name])) {
        return $this->components[$this->placeholders[$type->name]];
      } else {
        return $type;
      }
    }

    /**
     * Rewrite all placeholders inside an array for their real types
     *
     * @param   xp.compiler.types.TypeName[]
     * @return  xp.compiler.types.TypeName[]
     */
    protected function rewriteAll(array $types) {
      $result= array();
      foreach ($types as $type) {
        $result[]= $type ? $this->rewrite($type) : NULL;
      }
      return $result;
    }

    /**
     * Returns name
     *
     * @return  string
     */
    public function name() {
      return $this->definition->name();
    }

    /**
     * Returns parent type
     *
     * @return  xp.compiler.types.Types
     */
    public function parent() {
      return $this->definition->parent();
    }

    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function literal() {
      return $this->definition->literal();
    }

    /**
     * Returns type kind (one of the *_KIND constants).
     *
     * @return  string
     */
    public function kind() {
      return $this->definition->kind();
    }

    /**
     * Checks whether a given type instance is a subclass of this class.
     *
     * @param   xp.compiler.types.Types
     * @return  bool
     */
    public function isSubclassOf(Types $t) {
      return $this->definition->isSubclassOf($t);
    }

    /**
     * Returns whether this type is enumerable (that is: usable in foreach)
     *
     * @return  bool
     */
    public function isEnumerable() {
      return $this->definition->isEnumerable();
    }

    /**
     * Returns the enumerator for this class or NULL if none exists.
     *
     * @see     php://language.oop5.iterations
     * @return  xp.compiler.types.Enumerator
     */
    public function getEnumerator() {
      return $this->definition->getEnumerator();
    }

    /**
     * Returns whether a constructor exists
     *
     * @return  bool
     */
    public function hasConstructor() {
      return $this->definition->hasConstructor();
    }

    /**
     * Returns the constructor
     *
     * @return  xp.compiler.types.Constructor
     */
    public function getConstructor() {
      return $this->definition->getConstructor();
    }

    /**
     * Returns whether a method with a given name exists
     *
     * @param   string name
     * @return  bool
     */
    public function hasMethod($name) {
      return $this->definition->hasMethod($name);
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
      if (NULL !== ($method= $this->definition->getMethod($name))) {
        $method->returns= $method->returns ? $this->rewrite($method->returns) : NULL;
        $method->parameters= $this->rewriteAll($method->parameters);
        return $method;
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
      return $this->definition->hasOperator($symbol);
    }
    
    /**
     * Returns an operator by a given name
     *
     * @param   string symbol
     * @return  xp.compiler.types.Operator
     */
    public function getOperator($symbol) {
      return $this->definition->getOperator($symbol);
    }

    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasField($name) {
      return $this->definition->hasField($name);
    }
    
    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Field
     */
    public function getField($name) {
      return $this->definition->getField($name);
    }

    /**
     * Returns a property by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasProperty($name) {
      return $this->definition->hasProperty($name);
    }
    
    /**
     * Returns a property by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Property
     */
    public function getProperty($name) {
      return $this->definition->getProperty($name);
    }

    /**
     * Returns a constant by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasConstant($name) {
      return $this->definition->hasConstant($name);
    }
    
    /**
     * Returns a constant by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Constant
     */
    public function getConstant($name) {
      return $this->definition->getConstant($name);
    }

    /**
     * Returns whether this class has an indexer
     *
     * @return  bool
     */
    public function hasIndexer() {
      return $this->definition->hasIndexer();
    }

    /**
     * Returns indexer
     *
     * @return  xp.compiler.types.Indexer
     */
    public function getIndexer() {
      if (NULL !== ($indexer= $this->definition->getIndexer())) {
        $indexer->type= $this->rewrite($indexer->type);
        $indexer->parameter= $this->rewrite($indexer->parameter);
        return $indexer;
      }
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
      return sprintf(
        '%s@(%s<%s>)',
        $this->getClassName(),
        $this->definition->toString(),
        implode(', ', array_map(array('xp', 'stringOf'), $this->components))
      );
    }
  }
?>
