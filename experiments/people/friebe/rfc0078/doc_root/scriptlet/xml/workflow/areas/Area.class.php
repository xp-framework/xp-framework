<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * An Area groups States, which are deferred from this area via a 
   * router.
   *
   * Examples:
   * <pre>
   * - http://example.com/public/view
   *   Maps to the public area, which uses a class router, dispatching
   *   this request to the ViewState.
   * </pre>
   *
   * @see      xp://scriptlet.xml.workflow.routing.Router
   * @see      xp://scriptlet.xml.workflow.State
   * @purpose  Interface
   */
  interface Area {
  
    /**
     * Gets the router to be used in this area.
     *
     * @return  scriptlet.xml.workflow.routing.Router
     */
    public function getRouter();
  
  }
?>
