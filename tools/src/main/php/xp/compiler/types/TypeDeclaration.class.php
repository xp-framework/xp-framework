<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.compiler.types.Types', 
    'xp.compiler.ast.ParseTree',
    'xp.compiler.ast.ClassNode',
    'xp.compiler.ast.InterfaceNode',
    'xp.compiler.ast.EnumNode'
  );

  /**
   * Represents a declared type
   *
   * @test    xp://tests.types.TypeDeclarationTest
   */
  class TypeDeclaration extends Types {
    protected $tree= NULL;
    protected $parent= NULL;
    
    /**
     * Constructor
     *
     * @param   xp.compiler.ast.ParseTree tree
     * @param   xp.compiler.types.Types parent
     */
    public function __construct(ParseTree $tree, Types $parent= NULL) {
      $this->tree= $tree;
      $this->parent= $parent;
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
      $n= $this->tree->declaration->name->name;
      if ($this->tree->package) {
        $n= $this->tree->package->name.'.'.$n;
      }
      return $n;
    }

    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function literal() {
      return isset($this->tree->declaration->literal)
        ? $this->tree->declaration->literal 
        : $this->tree->declaration->name->name
      ;
    }

    /**
     * Returns literal for use in code
     *
     * @return  string
     */
    public function kind() {
      switch ($decl= $this->tree->declaration) {
        case $decl instanceof ClassNode: return parent::CLASS_KIND;
        case $decl instanceof InterfaceNode: return parent::INTERFACE_KIND;
        case $decl instanceof EnumNode: return parent::ENUM_KIND;
        default: return parent::UNKNOWN_KIND;
      }
    }

    /**
     * Checks whether a given type instance is a subclass of this class.
     *
     * @param   xp.compiler.types.Types
     * @return  bool
     */
    public function isSubclassOf(Types $t) {
      return $this->parent ? $this->parent->equals($t) || $this->parent->isSubclassOf($t): FALSE;
    }

    /**
     * Returns whether this type is enumerable (that is: usable in foreach)
     *
     * @return  bool
     */
    public function isEnumerable() {
      // TBI
      return FALSE;
    }

    /**
     * Returns the enumerator for this class or NULL if none exists.
     *
     * @see     php://language.oop5.iterations
     * @return  xp.compiler.types.Enumerator
     */
    public function getEnumerator() {
      // TBI
      return NULL;
    }

    /**
     * Returns whether a constructor exists
     *
     * @return  bool
     */
    public function hasConstructor() {
      foreach ($this->tree->declaration->body as $member) {
        if ($member instanceof ConstructorNode) return TRUE;
      }
      return $this->parent ? $this->parent->hasMethod($name) : FALSE;
    }

    /**
     * Returns the constructor
     *
     * @return  xp.compiler.types.Constructor
     */
    public function getConstructor() {
      foreach ($this->tree->declaration->body as $member) {
        if ($member instanceof ConstructorNode) {
          $c= new xp·compiler·types·Constructor();
          $c->modifiers= $member->modifiers;
          foreach ($member->parameters as $p) {
            $c->parameters[]= $p['type'];
          }
          $c->holder= $this;
          return $c;
        }
      }
      return $this->parent ? $this->parent->getMethod($name) : NULL;
    }
    
    /**
     * Returns a method by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasMethod($name) {
      foreach ($this->tree->declaration->body as $member) {
        if ($member instanceof MethodNode && $member->name === $name) return TRUE;
      }
      return $this->parent ? $this->parent->hasMethod($name) : FALSE;
    }
    
    /**
     * Returns a method by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Method
     */
    public function getMethod($name) {
      foreach ($this->tree->declaration->body as $member) {
        if ($member instanceof MethodNode && $member->name === $name) {
          $m= new xp·compiler·types·Method();
          $m->name= $member->name;
          $m->returns= $member->returns;
          $m->modifiers= $member->modifiers;
          foreach ((array)$member->parameters as $p) {
            $m->parameters[]= $p['type'];
          }
          $m->holder= $this;
          return $m;
        }
      }
      return $this->parent ? $this->parent->getMethod($name) : NULL;
    }

    /**
     * Returns whether an operator by a given symbol exists
     *
     * @param   string symbol
     * @return  bool
     */
    public function hasOperator($symbol) {
      foreach ($this->tree->declaration->body as $member) {
        if ($member instanceof OperatorNode && $member->symbol === $symbol) return TRUE;
      }
      return $this->parent ? $this->parent->hasOperator($name) : FALSE;
    }
    
    /**
     * Returns an operator by a given name
     *
     * @param   string symbol
     * @return  xp.compiler.types.Operator
     */
    public function getOperator($symbol) {
      foreach ($this->tree->declaration->body as $member) {
        if ($member instanceof OperatorNode && $member->symbol === $symbol) {
          $m= new xp·compiler·types·Method();
          $m->name= $member->symbol;
          $m->returns= $member->returns;
          $m->modifiers= $member->modifiers;
          foreach ($member->parameters as $p) {
            $m->parameters[]= $p->type;
          }
          $m->holder= $this;
          return $m;
        }
      }
      return $this->parent ? $this->parent->getOperator($name) : NULL;
    }

    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasField($name) {
      foreach ($this->tree->declaration->body as $member) {
        if (
          ($member instanceof FieldNode && $member->name === $name) ||
          ($member instanceof EnumMemberNode && $member->name === $name)
        ) return TRUE;
      }
      return $this->parent ? $this->parent->hasField($name) : FALSE;
    }
    
    /**
     * Returns a field by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Field
     */
    public function getField($name) {
      foreach ($this->tree->declaration->body as $member) {
        if ($member instanceof FieldNode && $member->name === $name) {
          $f= new xp·compiler·types·Field();
          $f->name= $member->name;
          $f->modifiers= $member->modifiers;
          $f->type= $member->type;
          $f->holder= $this;
          return $f;
        } else if ($member instanceof EnumMemberNode) {
          $f= new xp·compiler·types·Field();
          $f->name= $member->name;
          $f->modifiers= $member->modifiers;
          $f->type= $this->tree->declaration->name;
          $f->holder= $this;
          return $f;
        }
      }
      return $this->parent ? $this->parent->getField($name) : NULL;
    }

    /**
     * Returns a property by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasProperty($name) {
      foreach ($this->tree->declaration->body as $member) {
        if ($member instanceof PropertyNode && $member->name === $name) return TRUE;
      }
      return $this->parent ? $this->parent->hasProperty($name) : FALSE;
    }
    
    /**
     * Returns a property by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Property
     */
    public function getProperty($name) {
      foreach ($this->tree->declaration->body as $member) {
        if ($member instanceof PropertyNode && $member->name === $name) {
          $p= new xp·compiler·types·Property();
          $p->name= $member->name;
          $p->modifiers= $member->modifiers;
          $p->type= $member->type;
          $p->holder= $this;
          return $p;
        }
      }
      return $this->parent ? $this->parent->hasProperty($name) : FALSE;
    }

    /**
     * Returns a constant by a given name
     *
     * @param   string name
     * @return  bool
     */
    public function hasConstant($name) {
      foreach ($this->tree->declaration->body as $member) {
        if ($member instanceof ClassConstantNode && $member->name === $name) return TRUE;
      }
      return $this->parent ? $this->parent->hasConstant($name) : FALSE;
    }
    
    /**
     * Returns a constant by a given name
     *
     * @param   string name
     * @return  xp.compiler.types.Constant
     */
    public function getConstant($name) {
      foreach ($this->tree->declaration->body as $member) {
        if ($member instanceof ClassConstantNode && $member->name === $name) {
          $c= new xp·compiler·types·Constant();
          $c->name= $member->name;
          $c->type= $member->type;
          $c->value= cast($member->value, 'xp.compiler.ast.Resolveable')->resolve();
          $c->holder= $this;
          return $c;
        }
      }
      return $this->parent ? $this->parent->getConstant($name) : FALSE;
    }

    /**
     * Returns whether this class has an indexer
     *
     * @return  bool
     */
    public function hasIndexer() {
      foreach ($this->tree->declaration->body as $member) {
        if ($member instanceof IndexerNode) return TRUE;
      }
      return FALSE;
    }

    /**
     * Returns indexer
     *
     * @return  xp.compiler.types.Indexer
     */
    public function getIndexer() {
      foreach ($this->tree->declaration->body as $member) {
        if (!$member instanceof IndexerNode) continue;
        $i= new xp·compiler·types·Indexer();
        $i->type= $member->type;
        $i->parameter= $member->parameter['type'];
        $i->holder= $this;
        return $i;
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
      return $this->getClassName().'@('.$this->tree->declaration->name->toString().')';
    }
  }
?>
