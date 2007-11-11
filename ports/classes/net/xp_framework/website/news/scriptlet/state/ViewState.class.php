<?php
/* This class is part of the XP framework
 *
 * $Id: ViewNewsState.class.php 8971 2006-12-27 15:27:10 +0000 (Mi, 27 Dez 2006) friebe $ 
 */

  uses(
    'net.xp_framework.util.markup.FormresultHelper',
    'scriptlet.xml.workflow.AbstractState',
    'rdbms.ConnectionManager',
    'util.Date'
  );

  /**
   * Handles /xml/news/view
   *
   * @purpose  State
   */
  class ViewState extends AbstractState {

    protected function getArticleId($request) {
      return $request->getEnvValue('ARTID');
    }
  
    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      // Fetch entry
      $db= ConnectionManager::getInstance()->getByHost('news', 0);
      $q= $db->query('
        select 
          entry.id as id,
          entry.title as title,
          entry.body as body,
          entry.extended as extended,
          entry.author as author,
          entry.timestamp as timestamp
        from
          serendipity_entries entry
        where
          entry.id= %d
          and isdraft = "false"
        ',
        $this->getArticleId($request)
      );
      
      // Check if we found an entry
      if (!($record= $q->next())) {
        $response->addFormError('entry', 'notfound', '*', $this->getArticleId($request));
        return;
      }
      
      // Add entry to the formresult
      with ($entry= $response->addFormResult(new Node('entry'))); {
        $entry->setAttribute('id', $record['id']);
        $entry->addChild(new Node('title', $record['title']));
        $entry->addChild(new Node('author', $record['author']));
        $entry->addChild(Node::fromObject(new Date($record['timestamp']), 'date'));
        $entry->addChild(FormresultHelper::markupNodeFor('body', $record['body']));
        $entry->addChild(FormresultHelper::markupNodeFor('extended', $record['extended']));
        
        // Fetch comments
        $q= $db->query('
          select 
            comment.id as id,
            comment.title as title,
            comment.author as author,
            comment.email as email,
            comment.url as url,
            comment.body as body,
            comment.timestamp as timestamp
          from
            serendipity_comments comment
          where
            entry_id= %d
          ',
          $entry->getAttribute('id')
        );
      
        // Add comments to the entry node
        $comments= $entry->addChild(new Node('comments'));
        while ($record= $q->next()) {
          with ($comment= $comments->addChild(new Node('comment'))); {
            $comment->setAttribute('id', $record['id']);
            $comment->addChild(new Node('title', $record['title']));
            $comment->addChild(new Node('author', $record['author']));
            $comment->addChild(new Node('email', $record['email']));
            $comment->addChild(new Node('url', $record['url']));
            $comment->addChild(Node::fromObject(new Date($record['timestamp']), 'date'));
            $comment->addChild(FormresultHelper::markupNodeFor('body', $record['body']));
          }
        }
      }
      
      // Find categories this entry is in
      $q= $db->query('
        select
          c.categoryid,
          c.category_name
        from
      	  serendipity_entrycat as se,
      	  serendipity_category as c
      	where se.entryid= %d
      	  and se.categoryid= c.categoryid
      	',
      	$entry->getAttribute('id')
      );
      
      $catnode= $entry->addChild(new Node('categories'));
      while ($q && $r= $q->next()) {
      	$catnode->addChild(new Node('category', NULL, array(
      	  'id'   => $r['categoryid'],
      	  'name' => $r['category_name'])
        ));
      }
    }
  }
?>
