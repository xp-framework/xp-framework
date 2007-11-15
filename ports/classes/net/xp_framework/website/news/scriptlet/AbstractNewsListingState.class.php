<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractNewsListingState.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  uses(
    'net.xp_framework.util.markup.FormresultHelper',
    'net.xp_framework.website.news.scriptlet.AbstractNewsState',
    'rdbms.ConnectionManager',
    'util.Date'
  );

  /**
   * Base class for all news listing states
   *
   * @purpose  Abstract base class
   */
  abstract class AbstractNewsListingState extends AbstractNewsState {
    const
      PAGER_LENGTH      = 10;

    /**
     * Retrieve entries
     *
     * @param   rdbms.DBConnection db
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @return  rdbms.ResultSet
     */
    abstract public function getEntries($db, $request);
    
    /**
     * Retrieve page offset
     *
     * @param   scriptlet.HttpScriptletRequest $request
     * @return  int
     * @throws  lang.IllegalStateException if query string is broken
     */
    public function getOffset($request) {
      if (0 == strlen($request->getQueryString())) return 0;
      if (1 != sscanf($request->getQueryString(), '%d', $offset)) throw new IllegalStateException('Query string broken: "'.$request->getQueryString().'"');
      return $offset;
    }
    
    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      $db= ConnectionManager::getInstance()->getByHost('news', 0);
      
      // Add all categories to the formresult
      $this->insertCategories(
        $this->getParentCategory($request),
        $response->addFormResult(new Node('categories'))
      );

      // Call the getEntries() method (which is overridden by subclasses
      // and returns the corresponding entries). For perfomance reasons, it
      // does a join on entries and categories (which have a 1:n
      // relationship, so the returned results are not unique)
      $q= $this->getEntries($db, $request);
      $cnt= 0;

      $n= $response->addFormResult(new Node('entries'));
      while ($record= $q->next()) {
        $cnt++;
        $e= $n->addChild(new Node('entry', NULL, array('id' => $record['id'], 'link' => $this->sanitizeHref($record['title']))));
        $e->addChild(new Node('title', $record['title']));
        $e->addChild(new Node('author', $record['author']));
        $e->addChild(new Node('extended_length', $record['extended_length']));
        $e->addChild(new Node('num_comments', $record['num_comments']));
        $e->addChild(Node::fromObject(new Date($record['timestamp']), 'date'));
        $e->addChild(FormresultHelper::markupNodeFor('body', $record['body']));
        
        $cnode= $e->addChild(new Node('categories'));
        foreach ($this->categoriesOfEntry($record['id']) as $row) {
          $cnode->addChild(new Node('category', $row['category_name'], array(
            'link'  => $this->sanitizeHref($row['category_name']),
            'id'    => $row['categoryid']
          )));
        }
      }
      
      // Add pager element
      $pager= array(
        'offset'  => $this->getOffset($request),
        'prev'    => max(0, $this->getOffset($request)- self::PAGER_LENGTH)
      );
      if ($cnt >= self::PAGER_LENGTH) {
        $pager['next']= $this->getOffset($request)+ self::PAGER_LENGTH;
      }
      $response->addFormResult(new Node('pager', NULL, $pager));
      
      return TRUE;
    }
  }
?>
