<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.facade.DataSetFacade');

  /**
   * Job editor
   *
   * @purpose  Facade
   */
  #[@facade(datasource= 'classes.db.Job')]
  class JobsFacade extends DataSetFacade {
  }
?>
