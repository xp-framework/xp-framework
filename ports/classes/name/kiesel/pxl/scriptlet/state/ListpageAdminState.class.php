<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'name.kiesel.pxl.scriptlet.AbstractPxlState'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class ListpageAdminState extends AbstractPxlState {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct() {
    }  
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function requiresAuthentication() {
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function movePage($page_id, $direction) {
      $db= ConnectionManager::getInstance()->getByHost('pxl', 0);
      $transaction= $db->begin(new Transaction('changesequence'));
      
      // Fetch current sequence
      $q= current($db->select('sequence from page where page_id= %d', $page_id));
      $sequence= $q['sequence'];

      // Move out of the way
      $db->update('page set sequence= -1 where page_id= %d', $page_id);
      $cnt= $db->update('
        page 
        set 
          sequence= %d 
        where sequence= %d
        ',
        $sequence,
        $sequence+ $direction
      );
      
      if (0 === $cnt) {
        $tran->rollback();
        return FALSE;
      }

      $db->update('
        page 
        set 
          sequence= %d 
        where sequence= -1
        ',
        $sequence+ $direction
      );
      
      $transaction->commit();
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function deletePage($page_id) {
      $db= ConnectionManager::getInstance()->getByHost('pxl', 0);
      
      $tran->begin(new Transaction('delpage'));
      $myseq= current($db->select('sequence from page where page_id= %d', $page_id));
      $db->delete('from tag where page_id= %d', $page_id);
      $db->delete('from picture where page_id= %d', $page_id);
      $db->delete('from page where page_id= %d', $page_id);
      $db->update('page set sequence= sequence- 1 where sequence > %d', $myseq['sequence']);
      
      $tran->commit();
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function processCommands($request, $response, $context) {
      if (!$request->hasParam('action')) return;

      switch ($request->getParam('action')) {
        case 'move.up': return $this->movePage($request->getParam('page'), +1);
        case 'move.dn': return $this->movePage($request->getParam('page'), -1);
        case 'delete': return $this->deletePage($request->getParam('page'));
      }
    }
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function process($request, $response, $context) {
      parent::process($request, $response, $context);
      
      $this->processCommands($request, $response, $context);
      
      $offset= $request->getParam('offset', 0);
      $db= ConnectionManager::getInstance()->getByHost('pxl', 0);
      
      $q= $db->query('
        select
          page_id,
          title,
          author_id,
          sequence,
          cast(published, "date") as published
        from page
        where 1=1
          %c
        order by sequence desc
        ',
        ($offset ? $db->prepare('and sequence < %d', $offset) : '')
      );
      
      $pages= $response->addFormResult(new Node('pages'));
      while ($q && ($r= $q->next())) {
      
        // Further queries
        $comments= $db->select('
          comment_type_id,
          bz_id,
          count(*)
          from comment
          where page_id= %d
          group by
            comment_type_id,
            bz_id
          ',
          $r['page_id']
        );

        $n= $pages->addChild(new Node('page', NULL, array(
          'page_id' => $r['page_id'],
          'title'   => $r['title'],
          'sequence'  => $r['sequence']
        )));

        $n->addChild(Node::fromObject($r['published'], 'published'));
        $n->addChild(Node::fromArray($comments, 'comments'));
      }
    }
  }
?>
