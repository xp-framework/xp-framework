<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('rdbms.SQLRenderable');

  /**
   * interface for all Prjections - 
   * Projections are built with thee static factory class rdbms.criterion.Projections
   * 
   * @see xp://rdbms.criterion.Projections
   */
  interface Projection extends SQLRenderable {
    const AVG=  'avg(%s)';
    const SUM=  'sum(%s)';
    const MIN=  'min(%s)';
    const MAX=  'max(%s)';
    const PROP= '%s';
  }
?>
