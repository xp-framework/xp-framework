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
   * Handles /xml/rfc
   *
   * @purpose  State
   */
  class RfcState extends AbstractState {
  
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

      // Select all RFCs currently in discussion 
      $i= Rfc::getPeer()->iteratorFor(create(new Criteria())
        ->add('bz_id', 10000, EQUAL)
        ->addOrderBy('rfc_id', DESC)
      );
    
      $builder= new MarkupBuilder();
      $l= $response->addFormresult(new Node('list'));

      while ($i->hasNext()) {
        with ($rfc= $i->next()); {
          $n= $l->addChild(new Node('rfc', NULL, array('number' => sprintf('%04d', $rfc->getRfc_id()))));
          $n->addChild(Node::fromObject($rfc->getCreated_at(), 'created'));
          $n->addChild(new Node('title', $rfc->getTitle()));
          $n->addChild(new Node('status', $rfc->getStatus(), array('id' => $bz[$rfc->getBz_id()])));
          $n->addChild(Node::fromObject($rfc->getAuthor(), 'author'));

          $markup= '<p>'.$builder->markupFor($rfc->getContent()).'</p>';
          $n->addChild(new Node('content', new PCData($markup)));
        }
      }
    }
  }
?>
