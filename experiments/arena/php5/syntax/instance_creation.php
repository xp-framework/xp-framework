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
  
  class NatcaseSorter {
    public function sort(ArrayList $list, $order) {
      $list->sort(new Comparator($order) {
        protected
          $order = 0;

        public function __construct($order) {
          $this->order= $order;
        }
        public function compare($a, $b) {
          return $this->order * strnatcmp($a, $b);
        }
      });
    }
  }
  
  // {{{ main
  $list= new ArrayList();
  $list->elements[]= 1;
  $list->elements[]= 10;
  $list->elements[]= 8;
  $list->elements[]= 9;

  echo 'Before: ', implode(', ', $list->elements), "\n";
  
  NatcaseSorter::sort($list, -1);
  echo 'After : ', implode(', ', $list->elements), "\n";
  
  NatcaseSorter::sort($list, +1);
  echo 'After : ', implode(', ', $list->elements), "\n";
  // }}}
?>
