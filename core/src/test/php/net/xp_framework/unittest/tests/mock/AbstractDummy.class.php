<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses();

/**
 * A abstract dummy class for testing.
 *
 * @purpose Unit tests
 */
 abstract class AbstractDummy extends Object {

    public function concreteMethod() {
      return 'concreteMethod';
    }

    public abstract function abstractMethod();
    
    public function equals($cmp) { //from Generic
      return true;
    }

  }

?>
