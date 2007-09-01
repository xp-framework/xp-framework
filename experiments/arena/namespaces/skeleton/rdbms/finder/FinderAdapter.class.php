<?php
/* This class is part of the XP framework
 *
 * $Id: FinderAdapter.class.php 9347 2007-01-23 11:47:09Z friebe $ 
 */

  namespace rdbms::finder;

  uses('rdbms.finder.Finder');

  /**
   * Adapter that makes rdbms.Peer objects usable as finders.
   *
   * @see      xp://rdbms.Peer
   * @purpose  Finder / Peer Adapter
   */
  class FinderAdapter extends Finder {
    protected 
      $peer= NULL;

    /**
     * Constructor
     *
     * @param   rdbms.Peer peer
     */
    public function __construct($peer) {
      $this->peer= $peer;
    }

    /**
     * Retrieve this finder's peer object
     *
     * @return  rdbms.Peer
     */
    public function getPeer() {
      return $this->peer;
    }
  }
?>
