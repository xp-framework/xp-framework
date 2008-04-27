<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'text.doclet.markup.MarkupBuilder',
    'net.xp_framework.db.caffeine.Rfc'
  );

  /**
   * Handles /xml/rfc/list
   *
   * @purpose  State
   */
  class ListRfcState extends AbstractState {
  
    /**
     * Filter RFCs by status
     *
     * @param   string arg
     * @return  rdbms.Criteria
     */
    public function byStatus($arg) {
      static $bz= array(
        'draft'       => 500,
        'discussion'  => 10000,
        'implemented' => 20000,
        'rejected'    => 30000,
        'obsoleted'   => 30001
      );
      return create(new Criteria())->add('bz_id', $bz[$arg], EQUAL);
    }
  
    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      static $bz= array(
        500   => 'draft',
        10000 => 'discussion',
        20000 => 'implemented',
        30000 => 'rejected',
        30001 => 'obsoleted'
      );

      // Select RFCs based on criteria
      sscanf($request->getQueryString(), '%[^.].%[^,],page%d', $criteria, $filter, $page);
      $i= Rfc::getPeer()->iteratorFor($this->getClass()->getMethod('by'.$criteria)
        ->invoke($this, array($filter))
        ->addOrderBy('rfc_id', DESC)
      );
    
      $l= $response->addFormresult(new Node('list'));
      $l->setAttribute('page', (int)$page);
      $l->setAttribute('criteria', $criteria);
      $l->setAttribute('filter', $filter);

      $builder= new MarkupBuilder();
      $count= 0;
      $page*= 10;
      while ($i->hasNext()) {
        $count++;
        with ($rfc= $i->next()); {
          if ($count <= $page || $count > $page + 10) continue;

          $n= $l->addChild(new Node('rfc', NULL, array('number' => sprintf('%04d', $rfc->getRfc_id()))));
          $n->addChild(Node::fromObject($rfc->getCreated_at(), 'created'));
          $n->addChild(new Node('title', $rfc->getTitle()));
          $n->addChild(new Node('status', $rfc->getStatus(), array('id' => $bz[$rfc->getBz_id()])));
          $n->addChild(Node::fromObject($rfc->getAuthor(), 'author'));

          $markup= '<p>'.$builder->markupFor($rfc->getContent()).'</p>';
          $n->addChild(new Node('content', new PCData($markup)));
        }
      }
      
      $l->setAttribute('count', $count);
    }
  }
?>
