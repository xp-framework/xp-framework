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
  interface Projection extends SQLRenderable {
    const AVG=  'avg(%s)';
    const SUM=  'sum(%s)';
    const MIN=  'min(%s)';
    const MAX=  'max(%s)';
    const PROP= '%s';
  }
?>
