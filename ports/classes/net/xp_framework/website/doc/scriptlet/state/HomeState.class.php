<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.website.doc.scriptlet.state.AbstractDocState');

  /**
   * Handles /xml/home
   *
   * @purpose  State
   */
  class HomeState extends AbstractDocState {

    /**
     * Returns which entry to display
     *
     * @param   string base
     * @param   string topic
     * @return  string entry name
     */
    protected function entryFor($base, $topic) {
      return 'home';
    }
  }
?>
