<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractNewsListingState.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  uses(
    'net.xp_framework.util.markup.FormresultHelper',
    'scriptlet.xml.workflow.AbstractState',
    'rdbms.ConnectionManager',
    'util.Date'
  );

  /**
   * Base class for all news listing states
   *
   * @purpose  Abstract base class
   */
  abstract class AbstractNewsListingState extends AbstractState {

    /**
     * Retrieve entries
     *
     * @param   rdbms.DBConnection db
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @return  rdbms.ResultSet
     */
    abstract public function getEntries($db, $request);
    
    /**
     * Return date
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @return  util.Date
     */
    public function getContextMonth($request) {
      return Date::now();
    }
    
    /**
     * Retrieve parent category's ID
     *
     * @return  int
     */
    public function getParentCategory($request) {
      return 0;
    }
    
    public function getOffset($request) {
      if (2 != sscanf($request->getQueryString(), '%d,%d', $category, $offset)) return 0;
      return $offset;
    }
    
    protected function sanitizeHref($name) {
      return preg_replace('#[^a-zA-Z0-9\-\._]#', '_', $name);
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
      $n= $response->addFormResult(new Node('categories'));
      $q= $db->query(
        'select categoryid, category_name from serendipity_category where parentid= %d',
        $this->getParentCategory($request)
      );
      while ($record= $q->next()) {
        $n->addChild(new Node('category', $record['category_name'], array(
          'id'   => $record['categoryid'],
          'link' => $this->sanitizeHref($record['category_name'])
        )));
      }
      
      $self= $db->query('select categoryid, category_name from serendipity_category where categoryid= %d', $this->getParentCategory($request));
      if (($record= $self->next())) {
        $response->addFormResult(new Node('current-category', $record['category_name'], array(
          'id' => $record['categoryid'],
          'link'  => $this->sanitizeHref($record['category_name'])
        )));
      }
            

      // Call the getEntries() method (which is overridden by subclasses
      // and returns the corresponding entries). For perfomance reasons, it
      // does a join on entries and categories (which have a 1:n
      // relationship, so the returned results are not unique)
      $q= $this->getEntries($db, $request);

      $n= $response->addFormResult(new Node('entries'));
      while ($record= $q->next()) {
        if (!isset($entry[$record['id']])) {
          $entry[$record['id']]= $n->addChild(new Node('entry', NULL, array('id' => $record['id'], 'link' => $this->sanitizeHref($record['title']))));
          $entry[$record['id']]->addChild(new Node('title', $record['title']));
          $entry[$record['id']]->addChild(new Node('author', $record['author']));
          $entry[$record['id']]->addChild(new Node('extended_length', $record['extended_length']));
          $entry[$record['id']]->addChild(new Node('num_comments', $record['num_comments']));
          $entry[$record['id']]->addChild(Node::fromObject(new Date($record['timestamp']), 'date'));
          $entry[$record['id']]->addChild(FormresultHelper::markupNodeFor('body', $record['body']));
        }
        
        // Add categories
        $entry[$record['id']]->addChild(new Node(
          'category', 
          $record['category'], 
          array('id' => $record['category_id'])
        ));
      }
      
      // Add pager element
      $p= $response->addFormResult(new Node('pager', NULL, array(
        'offset'  => $this->getOffset($request),
        'next'    => $this->getOffset($request)+ 10,
        'prev'	  => max(0, $this->getOffset($request)- 10)
      )));
      
      return TRUE;
    }
  }
?>
