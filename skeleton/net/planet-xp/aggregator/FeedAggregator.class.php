<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Base class for feed aggregators.
   *
   * @see      xp://net.planet-xp.Feed
   * @purpose  Base class
   */
  class FeedAggregator extends Object {

    /**
     * Aggregate a feed.
     *
     * @model   static
     * @access  public
     * @param   int feed_id
     * @param   string url
     * @return  &TBI
     */
    function &fetch($feed_id, $url, &$lastmodified) { }
  }
?>
