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
    public function getParentCategory() {
      return 0;
    }
    
    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {

      // Retrieve date information
      $contextDate= $this->getContextMonth($request);
      $month= $response->addFormResult(new Node('month', NULL, array(
        'num'   => $contextDate->getMonth(),    // Month number, e.g. 4 = April
        'year'  => $contextDate->getYear(),     // Year
        'days'  => $contextDate->toString('t'), // Number of days in the given month
        'start' => (date('w', mktime(            // Week day of the 1st of the given month
          0, 0, 0, $contextDate->getMonth(), 1, $contextDate->getYear()
        )) + 6) % 7
      )));

      $db= ConnectionManager::getInstance()->getByHost('news', 0);

      // Add all categories to the formresult
      $n= $response->addFormResult(new Node('categories'));
      $q= $db->query(
        'select categoryid, category_name from serendipity_category where parentid= %d',
      $this->getParentCategory()
      );
      while ($record= $q->next()) {
        $n->addChild(new Node('category', $record['category_name'], array(
          'id' => $record['categoryid']
        )));
      }

      // Fill in all days for which an entry exists
      $q= $db->query('
          select 
            dayofmonth(from_unixtime(entry.timestamp)) as day, 
            count(*) as numentries
          from 
            serendipity_entries entry 
          where 
            year(from_unixtime(entry.timestamp)) = %d 
            and month(from_unixtime(entry.timestamp)) = %d 
          group by day',
      $contextDate->getYear(),
      $contextDate->getMonth()
      );
      while ($record= $q->next()) {
        $month->addChild(new Node('entries', $record['numentries'], array(
            'day' => $record['day']
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
          $entry[$record['id']]= $n->addChild(new Node('entry', NULL, array('id' => $record['id'])));
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
      
      return TRUE;
    }
  }
?>
