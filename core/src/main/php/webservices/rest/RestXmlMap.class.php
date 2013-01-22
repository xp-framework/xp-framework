<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.Node');

  /**
   * Wraps an xml.Node into an array-acessible form
   *
   */
  class RestXmlMap extends Object implements IteratorAggregate, ArrayAccess {
    protected $node= NULL;

    protected static $iterate= NULL;

    static function __static() {
      self::$iterate= newinstance('Iterator', array(), '{
        private $i= 0, $c;
        private function value($n) {
          if (!$n->hasChildren()) return $n->getContent();
          $names= array();
          foreach ($n->getChildren() as $c) {
            $names[$c->getName()]= TRUE;
          }
          $result= array();
          if (sizeof($names) > 1) foreach ($n->getChildren() as $c) {
            $result[$c->getName()]= $this->value($c);
          } else foreach ($n->getChildren() as $c) {
            $result[]= $this->value($c);
          }

          return $result;
        }
        public function on($c) { $self= new self(); $self->c= $c; return $self; }
        public function current() { return $this->value($this->c[$this->i]); }
        public function key() { return $this->c[$this->i]->getName(); }
        public function next() { $this->i++; }
        public function rewind() { $this->i= 0; }
        public function valid() { return $this->i < sizeof($this->c); }
      }');
    }
    
    /**
     * Creates a new RestXmlMap instance
     *
     * @param   xml.Node node
     */
    public function __construct(Node $node) {
      $this->node= $node;
    }
    
    /**
     * Returns an iterator for use in foreach()
     *
     * @see     php://language.oop5.iterations
     * @return  php.Iterator
     */
    public function getIterator() {
      return self::$iterate->on($this->node->getChildren());
    }

    /**
     * = list[] overloading
     *
     * @param   string offset
     * @return  var
     */
    public function offsetGet($offset) {
      foreach ($this->node->getChildren() as $child) {
        if ($child->getName() === $offset) return $child->getContent();
      }
      return NULL;
    }

    /**
     * list[]= overloading
     *
     * @param   string offset
     * @param   var value
     */
    public function offsetSet($offset, $value) {
      throw new IllegalAccessException('Read-only');
    }

    /**
     * isset() overloading
     *
     * @param   int offset
     * @return  bool
     */
    public function offsetExists($offset) {
      foreach ($this->node->getChildren() as $child) {
        if ($child->getName() === $offset) return TRUE;
      }
      return FALSE;
    }

    /**
     * unset() overloading
     *
     * @param   int offset
     */
    public function offsetUnset($offset) {
      throw new IllegalAccessException('Read-only');
    }
  }
?>
