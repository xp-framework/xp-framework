<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('rdbms.SQLRenderable');

  /**
   * belongs to the Criterion API
   *
   */
  interface Projection extends SQLRenderable{
     const AVG=  'avg(%c)';
     const SUM=  'sum(%c)';
     const MIN=  'min(%c)';
     const MAX=  'max(%c)';
     const PROP= '%c';
  }
?>
