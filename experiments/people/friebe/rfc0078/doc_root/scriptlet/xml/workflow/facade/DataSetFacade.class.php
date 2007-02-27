<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.finder.FinderAdapter', 
    'scriptlet.xml.workflow.facade.Facade',
    'scriptlet.xml.workflow.AbstractState',
    'scriptlet.xml.workflow.facade.dataset.EditDataSetHandler',
    'scriptlet.xml.workflow.facade.dataset.CreateDataSetHandler',
    'scriptlet.xml.workflow.facade.dataset.DeleteDataSetHandler'
  );

  /**
   * This facade works with rdbms.DataSet subclasses. This class forms 
   * the base for facade subclasses.
   *
   * Example of the most simple use-case:
   * <code>
   *  #[@dataset('net.xp_framework.db.caffeine.News')]
   *  class NewsManagementFacade extends DataSetFacade {
   *  
   *  }
   * </code>
   *
   * @purpose  Facade implementation
   */
  abstract class DataSetFacade extends AbstractState implements Facade {

    /**
     * Helper method
     *
     * @return  rdbms.Peer
     */
    protected function getPeer() {
      try {
        return XPClass::forName($this->getClass()->getAnnotation('facade', 'datasource'))->getMethod('getPeer')->invoke(NULL);
      } catch (ElementNotFoundException $e) {
        throw new HttpScriptletException(sprintf(
          'Class %s does not have a facade.datasource annotation',
          $this->getClassName()
        ));
      }
    }
    
    /**
     * Helper method
     *
     * @param   rdbms.Peer peer
     * @param   rdbms.DataSet entity
     * @return  xml.Node
     */
    protected function marshalEntity($peer, $entity) {
      $e= new Node('entity');

      // Primary Key
      $id= '';
      foreach ($peer->primary as $field) {
        $id= '&'.$field.'='.$entity->get($field);
      }
      $e->setAttribute('id', substr($id, 1));

      // Fields
      foreach (array_keys($peer->types) as $field) {
        $value= $entity->get($field);

        $f= $e->addChild(new Node('field', NULL, array(
          'name'      => $field,
          'xsi:type'  => 'xsd:'.gettype($value)
        )));

        if (is_scalar($value)) {
          $f->setContent($value);
        } else if (is_null($value)) {
          // Intentionally empty
        } else if (is_array($value)) {
          $f->children= Node::fromArray($value)->children;
        } else if (is_object($value)) {
          $f->children= Node::fromObject($value)->children;
        }
      }
      
      return $e;
    }
    
    protected function getFinder() {
      return new FinderAdapter($this->getPeer());
    }
    
    /**
     * List operation
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     * @param   scriptlet.xml.workflow.Context context
     */
    public function doList($request, $response, $context, $arg) {
      $this->setup($request, $response, $context);
      
      $finder= $this->getFinder();
      $peer= $finder->getPeer();
      $n= $response->addFormResult(new Node('collection', NULL, array(
        'class' => $peer->identifier
      )));
      
      // Field information
      $r= $n->addChild(new Node('fields'));
      foreach (array_keys($peer->types) as $field) {
        $r->addChild(new Node('field', $field));
      }
      
      // Further list methods
      $a= $n->addChild(new Node('alternatives'));
      foreach ($finder->collectionMethods() as $m) {
        $a->addChild(new Node('method', $m->getName()));
      }

      // Entity list
      for (
        $iterator= $finder->iterate($finder->method($arg)->invoke($request->getQueryString())); 
        $iterator->hasNext(); 
      ) {
        $n->addChild($this->marshalEntity($peer, $iterator->next()));
      }
    }

    /**
     * Edit operation
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     * @param   scriptlet.xml.workflow.Context context
     */
    public function doEdit($request, $response, $context, $arg) {
      $this->addHandler(new EditDataSetHandler($this->getPeer()));
      $this->setup($request, $response, $context);
    }

    /**
     * New operation
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     * @param   scriptlet.xml.workflow.Context context
     */
    public function doNew($request, $response, $context, $arg) {
      $this->addHandler(new CreateDataSetHandler($this->getPeer()));
      $this->setup($request, $response, $context);
    }

    /**
     * Delete operation
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     * @param   scriptlet.xml.workflow.Context context
     */
    public function doDelete($request, $response, $context, $arg) {
      $this->addHandler(new DeleteDataSetHandler($this->getPeer()));
      $this->setup($request, $response, $context);
    }
  }
?>
