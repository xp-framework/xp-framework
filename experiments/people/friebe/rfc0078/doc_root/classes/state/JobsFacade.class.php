<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.facade.DataSetFacade',
    'classes.db.JobFinder'
  );

  /**
   * Job editor
   *
   * @purpose  Facade
   */
  #[@facade(datasource= 'classes.db.Job')]
  class JobsFacade extends DataSetFacade {
  
    protected function getFinder() {
      return new JobFinder();
    }
  }
?>
