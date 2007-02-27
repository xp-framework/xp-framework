<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'rdbms.Criteria',
    'scriptlet.xml.workflow.facade.dataset.DataSetHandler'
  );
  
  /**
   * Handler for deleting DataSets
   *
   * @see      xp://scriptlet.xml.workflow.facade.DataSetFacade
   * @purpose  Generic Handler
   */
  class DeleteDataSetHandler extends DataSetHandler {

    /**
     * Return the wrapper for a given Peer instance
     *
     * @param   rdbms.Peer
     * @return  scriptlet.xml.workflow.facade.dataset.DataSetWrapper
     */
    protected function wrapperFor($peer) {
      return newinstance('DataSetWrapper', array($peer), '{
        protected function casterFor($fieldType) {
          return NULL;  // No value casting - the entry will be deleted anyway
        }
      }');
    }

    /**
     * Retrieve the entity by its primary
     *
     * @param   rdbms.Peer
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.Context context
     * @return  rdbms.DataSet
     */
    protected function getEntity($peer, $request, $context) {
      $criteria= new Criteria();
      foreach ($peer->primary as $field) {
        $criteria->add($field, $request->getParam($field), EQUAL);
      }

      return $peer->iteratorFor($criteria)->next();
    }
    
    /**
     * Handle the entity that has been worked on
     *
     * @param   rdbms.DataSet
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.Context context
     */
    protected function handleSubmittedEntity($entity, $request, $context) {
      $entity->delete();
    }
  }
?>
