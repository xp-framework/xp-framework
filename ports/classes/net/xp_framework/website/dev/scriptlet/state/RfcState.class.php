<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'text.doclet.markup.MarkupBuilder',
    'net.xp_framework.db.caffeine.Rfc',
    'net.xp_framework.db.caffeine.RfcStatus'
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

      // Select all RFCs currently in discussion and all drafts
      $i= Rfc::getPeer()->iteratorFor(create(new Criteria())
        ->add('bz_id', array(RfcStatus::$draft->id(), RfcStatus::$discussion->id()), IN)
        ->addOrderBy('rfc_id', DESC)
      );
    
      $builder= new MarkupBuilder();
      $l= $response->addFormresult(new Node('list'));

      $count= 0;
      while ($i->hasNext()) {
        with ($rfc= $i->next()); {
          $n= $l->addChild(new Node('rfc', NULL, array('number' => sprintf('%04d', $rfc->getRfc_id()))));
          $n->addChild(Node::fromObject($rfc->getCreated_at(), 'created'));
          $n->addChild(new Node('title', $rfc->getTitle()));
          $n->addChild(new Node('status', $rfc->getStatus(), array('id' => RfcStatus::forId($rfc->getBz_id())->name())));
          $n->addChild(Node::fromObject($rfc->getAuthor(), 'author'));
          $n->addChild(new Node('scope', new PCData('<p>'.$builder->markupFor($rfc->getScope()).'</p>')));
        }
        
        // Only newest five
        if (++$count > 4) break;
      }
    }
  }
?>
