<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.Handler',
    'scriptlet.xml.workflow.facade.dataset.DataSetWrapper'
  );
  
  /**
   * Handler for DataSet modifications (new, edit, delete).
   *
   * @see      xp://scriptlet.xml.workflow.facade.DataSetFacade
   * @purpose  Base class
   */
  abstract class DataSetHandler extends Handler {

    /**
     * Constructor
     *
     * @param   rdbms.Peer peer
     */
    public function __construct($peer) {
      parent::__construct();
      $this->setWrapper($this->wrapperFor($peer));
    }

    /**
     * Get identifier.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.Context context
     * @return  string
     */
    public function identifierFor($request, $context) {
      return get_class($this).'#'.$request->getQueryString();
    }
    
    /**
     * Return the wrapper for a given Peer instance
     *
     * @param   rdbms.Peer
     * @return  scriptlet.xml.workflow.facade.dataset.DataSetWrapper
     */
    protected function wrapperFor($peer) {
      return new DataSetWrapper($peer);
    }
    
    /**
     * Retrieve the entity to work on
     *
     * @param   rdbms.Peer
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.Context context
     * @return  rdbms.DataSet
     */
    protected abstract function getEntity($peer, $request, $context);

    /**
     * Handle the entity that has been worked on
     *
     * @param   rdbms.DataSet
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.Context context
     */
    protected abstract function handleSubmittedEntity($entity, $request, $context);
    
    /**
     * Setup the wrapper. Calls the implementing class' getEntity() method
     * to retrieve the DataSet instance to work on.
     *
     * The following exceptions from the getEntity() are handled and added as
     * formresult error
     * <pre>
     *   Exception                      Error type
     *   ------------------------------ --------------------------------
     *   SQLStatementFailedException    sql+stmt:[ERROR-CODE]
     *   SQLException                   sql+generic:[EXCEPTION-NAME]
     *   NoSuchElementException         notfound
     *   IllegalAccessException         access
     * </pre>
     * The field is set to '*' and the exception message is passed as detail.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.Context context
     * @return  bool
     */
    public function setup($request, $context) {
      $peer= $this->wrapper->getPeer();

      try {
        $entity= $this->getEntity($peer, $request, $context);
      } catch (SQLStatementFailedException $e) {
        return $this->addError('sql+stmt:'.$e->getErrorcode(), '*', $e->getMessage());
      } catch (SQLException $e) {
        return $this->addError('sql+generic:'.$e->getClassName(), '*', $e->getMessage());
      } catch (NoSuchElementException $e) {
        return $this->addError('notfound', '*', $e->getMessage());
      } catch (IllegalAccessException $e) {
        return $this->addError('access', '*', $e->getMessage());
      }
      
      // Set formvalues
      foreach (array_keys($peer->types) as $field) {
        $this->setFormValue($field, $entity->get($field));
      }
      
      // Save for usage in handleSubmittedData()
      $this->setValue('entity', $entity);
      return TRUE;
    }
    
    /**
     * Handle submitted data.
     *
     * The following exceptions from the getEntity() are handled and added as
     * formresult error
     * <pre>
     *   Exception                      Error type
     *   ------------------------------ --------------------------------
     *   SQLStatementFailedException    sql+stmt:[ERROR-CODE]
     *   SQLException                   sql+generic:[EXCEPTION-NAME]
     * </pre>
     * The field is set to '*' and the exception message is passed as detail.
     *
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @param   scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    public function handleSubmittedData($request, $context) {

      // Modify the entity. If an error occurs, add an error to this handler.
      try {
        $this->handleSubmittedEntity($this->getValue('entity'), $request, $context);
      } catch (SQLStatementFailedException $e) {
        return $this->addError('sql+stmt:'.$e->getErrorcode(), '*', $e->getMessage());
      } catch (SQLException $e) {
        return $this->addError('sql+generic:'.$e->getClassName(), '*', $e->getMessage());
      }
      
      return TRUE;
    }
  }
?>
