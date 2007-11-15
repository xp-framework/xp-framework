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
    const
      MASTER_CATEGORY   = 8,
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
      return self::MASTER_CATEGORY;
    }
    
    public function getOffset($request) {
      if (0 == strlen($request->getQueryString())) return 0;
      if (1 != sscanf($request->getQueryString(), '%d', $offset)) throw new IllegalStateException('Query string broken: "'.$request->getQueryString().'"');
      return $offset;
    }
    
    protected function sanitizeHref($name) {
      return preg_replace('#[^a-zA-Z0-9\-\._]#', '_', $name);
    }
    
    protected function categoriesOfEntry($id) {
      return ConnectionManager::getInstance()->getByHost('news', 0)->select('
          c.categoryid,
          c.category_name,
          c.parentid
        from
          serendipity_entrycat matrix,
          serendipity_category c
        where c.categoryid= matrix.categoryid
          and matrix.entryid= %d
        ',
        $id
      );
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
      $q= $db->query('
        select 
          c.categoryid, 
          c.parentid,
          c.category_name 
         from 
           serendipity_category as p,
           serendipity_category as c
         where p.categoryid= %d
           and c.category_left >= p.category_left
           and c.category_right <= p.category_right
        ',
        8
      );
      while ($record= $q->next()) {
        $n->addChild(new Node('category', $record['category_name'], array(
          'id'        => $record['categoryid'],
          'parentid'  => $record['parentid'],
          'link'      => $this->sanitizeHref($record['category_name'])
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
