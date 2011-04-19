<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.types.ArrayList');

  /**
   * ArrayList extension methods
   *
   * @see   xp://lang.types.ArrayList
   * @see   xp://net.xp_framework.unittest.core.extensions.ExtensionInvocationTest
   */
  class ArrayListExtensions extends Object {

    static function __import($scope) {
      xp::extensions(__CLASS__, $scope);
    }

    /**
     * ArrayList::map() extension
     *
     * @param   lang.types.ArrayList self
     * @return  lang.types.ArrayList mapped
     */
    public static function map(ArrayList $self, $block) {
      $mapped= ArrayList::newInstance($self->length);
      foreach ($self->values as $i => $value) {
        $mapped[$i]= $block($value);
      }
      return $mapped;
    }

    /**
     * ArrayList::sorted() extension
     *
     * @see     php://sort
     * @param   lang.types.ArrayList self
     * @param   int flags SORT_REGULAR, SORT_NUMERIC or SORT_STRING 
     * @return  lang.types.ArrayList sorted
     */
    public static function sorted(ArrayList $self, $flags= SORT_REGULAR) {
      $sorted= clone $self;
      sort($sorted->values, $flags);
      return $sorted;
    }
  }
?>
