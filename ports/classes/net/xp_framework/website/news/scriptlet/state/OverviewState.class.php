<?php
/* This class is part of the XP framework
 *
 * $Id: NewsState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  uses('net.xp_framework.website.news.scriptlet.state.BycategoryState');

  /**
   * Handles /xml/news
   *
   * @purpose  State
   */
  class OverviewState extends BycategoryState {

    /**
     * Retrieve parent category's ID
     *
     * @return  int
     */
    public function getParentCategory($request) {
      return 8;
    }
  }
?>
