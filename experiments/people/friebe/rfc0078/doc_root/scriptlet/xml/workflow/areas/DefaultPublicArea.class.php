<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.routing.ClassRouter',
    'scriptlet.xml.workflow.areas.Area'
  );


  /**
   * Default public area implementation
   *
   * @see      xp://scriptlet.xml.workflow.areas.Area
   * @purpose  Default area implementation
   */
  class DefaultPublicArea extends Object implements Area {

    /**
     * Create the router object. Returns a ClassRouter in this implementation.
     *
     * @return  scriptlet.xml.workflow.routing.Router
     */
    public function getRouter() {
      return new ClassRouter();
    }
  }
?>
