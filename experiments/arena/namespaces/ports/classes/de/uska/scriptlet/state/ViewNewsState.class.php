<?php
/* This class is part of the XP framework
 *
 * $Id: NewsState.class.php 4958 2005-04-09 19:58:22Z kiesel $ 
 */

  namespace de::uska::scriptlet::state;

  ::uses('de.uska.scriptlet.state.UskaState');

  /**
   * Display a news entry.
   *
   * @purpose  Display news.
   */
  class ViewNewsState extends UskaState {
  
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
        $db= $cm->getByHost('uskanews', 0);
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
        
        $parser= new xml::parser::XMLParser();
        try {
          $body= nl2br(str_replace('&', '&amp;', $record['body']));
          if ($parser->parse('<body>'.$body.'</body>')) {
            $entry->addChild(new ('body', new ($body)));
          }
          
          $extended= nl2br(str_replace('&', '&amp;', $record['extended']));
          if ($parser->parse('<body>'.$extended.'</body>')) {
            $entry->addChild(new ('extended', new ($extended)));
          }
        } catch ( $e) {
          $response->addFormError('text.xml.XMLParser', 'XMLFormatException', 'body', $e->getMessage());
        }
      }
    }
  }
?>
