<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace util::invoke;

  ::uses('lang.types.ArrayList');

  /**
   * (Insert class' description here)
   *
   * @see      reference
   * @purpose  purpose
   */
  class Methods extends lang::Object {
    
    protected function __construct() {
    }
    
    public static function findAll() {
      return new self('0+');
    }

    public static function find() {
      return new self('0,1');
    }

    public static function get() {
      return new self('1');
    }

    public static function getAll() {
      return new self('1+');
    }
    
    public function annotatedWith($annnotation) {
      $this->filter= ::newinstance('lang.Object', array($annnotation), '{
        public function __construct($annotation) {
          $this->annotation= $annotation;
        }
        public function accept(Method $m) {
          return $m->hasAnnotation($this->annotation);
        }
      }');
      return $this;
    }
    
    public function accessible($modifiers) {
      return $this;
    }

    public function named($name) {
      return $this;
    }

    public function is($filters) {
      return $this;   // X, X or X
    }

    public function any($filters) {
      return $this;   // X and X
    }

    public function in( $class) {
      $filtered= array();
      foreach ($class->getMethods() as $method) {
        $this->filter->accept($method) && $filtered[]= $method;
      }
      return lang::types::ArrayList::newInstance($filtered);
    }
  }
?>
