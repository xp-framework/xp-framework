<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp-framework.util.markup.FormresultHelper',
    'scriptlet.xml.workflow.AbstractState',
    'rdbms.ConnectionManager',
    'util.Date'
  );

  /**
   * Handles /xml/news/view
   *
   * @purpose  State
   */
  class ViewNewsState extends AbstractState {

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    function process(&$request, &$response) {
      $cm= &ConnectionManager::getInstance();
      
      // Fetch entry
      try(); {
        $db= &$cm->getByHost('news', 0);
        $q= &$db->query('
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
          $request->getQueryString()
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      // Check if we found an entry
      if (!($record= $q->next())) {
        $response->addFormError('entry', 'notfound', '*', $request->getQueryString());
        return;
      }
      
      // Add entry to the formresult
      with ($entry= &$response->addFormResult(new Node('entry'))); {
        $entry->setAttribute('id', $record['id']);
        $entry->addChild(new Node('title', $record['title']));
        $entry->addChild(new Node('author', $record['author']));
        $entry->addChild(Node::fromObject(new Date($record['timestamp']), 'date'));
        $entry->addChild(FormresultHelper::markupNodeFor('body', $record['body']));
        $entry->addChild(FormresultHelper::markupNodeFor('extended', $record['extended']));
        
        // Fetch comments
        try(); {
          $q= &$db->query('
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
        } if (catch('SQLException', $e)) {
          $cat->error($e);
          return throw($e);
        }
      
        // Add comments to the entry node
        $comments= &$entry->addChild(new Node('comments'));
        while ($record= $q->next()) {
          with ($comment= &$comments->addChild(new Node('comment'))); {
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
    }
  }
?>
