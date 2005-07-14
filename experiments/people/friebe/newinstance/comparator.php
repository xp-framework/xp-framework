<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  
  // {{{ &lang.Object newinstance(string class, string bytes)
  //     Instance creation "expression"
  function &newinstance($class, $bytes) {
    static $i= 0;

    if (!class_exists($class)) {
      xp::error(xp::stringOf(new Error('Class "'.$class.'" does not exist')));
      // Bails
    }

    $name= $class.'·'.++$i;
    xp::registry('class.'.strtolower($name), $name);
    
    $c= $class;
    while ($c= get_parent_class($c)) {
      if ('interface' != $c) continue;
      
      // It's an interface
      eval('class '.$name.' extends Object '.$bytes);
      implements($name.'.class.php', $class);
      return new $name();
    }
    
    // It's a class
    eval('class '.$name.' extends '.$class.' '.$bytes);
    return new $name();
  }
  // }}}

  // {{{ Comparator
  //     Interface
  class Comparator extends Interface {
    function compare($a, $b) { }
  }
  // }}}

  // {{{ Listing
  //     Demo class
  class Listing extends Object {
    var
      $values= array();
    
    function sort(&$c) {
      usort($this->values, array(&$c, 'compare'));
    }
  }
  // }}}
  
  // {{{ main
  $listing= &new Listing();
  $listing->values= array(1, 2, 10, 5);

  echo 'before: ', xp::stringOf($listing->values), "\n";

  $listing->sort(newinstance('Comparator', '{
    function compare($a, $b) {
      return strnatcmp($a, $b);
    }
  }'));

  echo 'after: ', xp::stringOf($listing->values), "\n";
  // }}}
?>
