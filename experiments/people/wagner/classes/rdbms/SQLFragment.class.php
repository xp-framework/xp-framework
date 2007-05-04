<?php
/* This class is part of the XP framework
 *
 * $Id: Criterion.class.php 9172 2007-01-08 11:43:06Z friebe $ 
 */

  uses('rdbms.SQLRenderable');

  /**
   * Represents a query fragment to be used in a Criteria query
   *
   * @see      xp://rdbms.Criteria#add
   * @purpose  Interface
   */
  interface SQLFragment extends SQLRenderable  {}
?>
