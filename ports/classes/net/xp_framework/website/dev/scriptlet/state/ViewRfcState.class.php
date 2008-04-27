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
   * Handles /xml/rfc/view
   *
   * @purpose  State
   */
  class ViewRfcState extends AbstractState {
  
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

      sscanf($request->getQueryString(), '%04d', $id);
      if (!($rfc= Rfc::getByRfc_id($id))) {
        throw new HttpScriptletException('RFC "'.$id.'" not found', HTTP_NOT_FOUND);
      }
    
      // Add RFC details
      $n= $response->addFormresult(new Node('rfc', NULL, array('number' => sprintf('%04d', $rfc->getRfc_id()))));
      $n->addChild(Node::fromObject($rfc->getCreated_at(), 'created'));
      $n->addChild(new Node('title', $rfc->getTitle()));
      $n->addChild(new Node('status', $rfc->getStatus(), array('id' => $bz[$rfc->getBz_id()])));
      $n->addChild(Node::fromObject($rfc->getAuthor(), 'author'));
      foreach ($rfc->getContributorRfcList() as $contributor) {
        $n->addChild(Node::fromObject($contributor->getPerson(), 'contributor'));
      }

      // Add content
      $builder= new MarkupBuilder();
      $markup= '<p>'.$builder->markupFor($rfc->getContent()).'</p>';
      $n->addChild(new Node('content', new PCData($markup)));
    }
  }
?>
