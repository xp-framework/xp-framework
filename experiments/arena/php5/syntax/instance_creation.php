<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  interface Comparator {
    public function compare($a, $b);
  }
  
  class ArrayList {
    public
      $elements = array();

    public function sort(Comparator $c) {
      usort($this->elements, array($c, 'compare'));
    }
  }
  
  class Test {
  function sort($list) {
  eval('
  $list->sort(new Comparator() {
    public function compare($a, $b) {
      return strnatcmp($a, $b);
    }
  });
  ');
  }}

  
  // {{{ main
  $list= new ArrayList();
  $list->elements[]= 1;
  $list->elements[]= 10;
  $list->elements[]= 8;
  $list->elements[]= 9;

  echo 'Before: ', implode(', ', $list->elements), "\n";
  
  $l= new Test();
  $l->sort($this);
  echo 'After : ', implode(', ', $list->elements), "\n";
  // }}}
?>
