<?php
/* This class is part of the XP framework
 *
 * $Id: ViewNewsState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::scriptlet::state;

  ::uses(
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
  class ViewNewsState extends scriptlet::xml::workflow::AbstractState {

    /**
     * Process this state.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      $cm= rdbms::ConnectionManager::getInstance();
      
      // Fetch entry
      try {
        $db= $cm->getByHost('news', 0);
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
          $request->getQueryString()
        );
      } catch (rdbms::SQLException $e) {
        throw($e);
      }
      
      // Check if we found an entry
      if (!($record= $q->next())) {
        $response->addFormError('entry', 'notfound', '*', $request->getQueryString());
        return;
      }
      
      // Add entry to the formresult
      with ($entry= $response->addFormResult(new ('entry'))); {
        $entry->setAttribute('id', $record['id']);
        $entry->addChild(new ('title', $record['title']));
        $entry->addChild(new ('author', $record['author']));
        $entry->addChild(::fromObject(new util::Date($record['timestamp']), 'date'));
        $entry->addChild(net::xp_framework::util::markup::FormresultHelper::markupNodeFor('body', $record['body']));
        $entry->addChild(net::xp_framework::util::markup::FormresultHelper::markupNodeFor('extended', $record['extended']));
        
        // Fetch comments
        try {
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
        } catch (rdbms::SQLException $e) {
          $cat->error($e);
          throw($e);
        }
      
        // Add comments to the entry node
        $comments= $entry->addChild(new ('comments'));
        while ($record= $q->next()) {
          with ($comment= $comments->addChild(new ('comment'))); {
            $comment->setAttribute('id', $record['id']);
            $comment->addChild(new ('title', $record['title']));
            $comment->addChild(new ('author', $record['author']));
            $comment->addChild(new ('email', $record['email']));
            $comment->addChild(new ('url', $record['url']));
            $comment->addChild(::fromObject(new util::Date($record['timestamp']), 'date'));
            $comment->addChild(net::xp_framework::util::markup::FormresultHelper::markupNodeFor('body', $record['body']));
          }
        }
      }
    }
  }
?>
