<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.AbstractState');

  /**
   * Handles /xml/home
   *
   * @purpose  State
   */
  class AbstractEntryState extends AbstractState {
  
    /**
     * Retrieve entries
     *
     * @model   abstract
     * @access  protected
     * @param   &rdbms.DBConnection db
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @return  &rdbms.ResultSet
     */
    function &getEntries(&$db, &$request) {}
    
    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    function process(&$request, &$response) {

      // Retrieve date information
      $contextDate= &Date::now();
      $month= &$response->addFormResult(new Node('month', NULL, array(
        'num'   => $contextDate->getMonth(),    // Month number, e.g. 4 = April
        'year'  => $contextDate->getYear(),     // Year
        'days'  => $contextDate->toString('t'), // Number of days in the given month
        'start' => (date('w', mktime(            // Week day of the 1st of the given month
          0, 0, 0, $contextDate->getMonth(), 1, $contextDate->getYear()
        )) + 6) % 7
      )));

      $cm= &ConnectionManager::getInstance();
      try(); {
        $db= &$cm->getByHost('syndicate', 0);
        
        // Fill in all days for which an entry exists
        $q= &$db->query('
          select 
            dayofmonth(fi.published) as day, 
            count(*) as numentries
          from 
            syndicate.feeditem fi,
            syndicate.syndicate_feed_matrix sfm
          where 
            year(fi.published) = %d 
            and month(fi.published) = %d 
            and fi.feed_id= sfm.feed_id
            and sfm.syndicate_id= %d
          group by day',
          $contextDate->getYear(),
          $contextDate->getMonth(),
          $this->getSyndicate_id($request->getProduct())
        );
        while ($record= $q->next()) {
          $month->addChild(new Node('entries', $record['numentries'], array(
            'day' => $record['day']
          )));
        }
        
        // Call the getEntries() method (which is overridden by subclasses
        // and returns the corresponding entries).
        $q= &$this->getEntries($db, $request);
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      $n= &$response->addFormResult(new Node('syndicate'));
      while ($record= $q->next()) {
        $author= (!empty($record['author_translated'])
          ? $record['author_translated']
          : (!empty($record['feedauthor'])
            ? $record['feedauthor']
            : $record['author']
        ));
      
        with ($item= &$n->addChild(new Node('item', NULL, array(
          'feeditem_id' => $record['feeditem_id'],
          'title'       => $record['title'],
          'link'        => $record['link'],
          'author'      => $author
        )))); {
          $item->addChild(new Node('content', new PCData($record['content'])));
          $item->addChild(new Node('feed', NULL, array(
            'feed_id'     => $record['feed_id'],
            'title'       => $record['feedtitle'],
            'link'        => $record['feedlink'],
            'description' => $record['description']
          )));
          $item->addChild(Node::fromObject($record['published'], 'published'));
        }
      }
      
      // Add all available feeds
      try(); {
        $q= &$db->query('
          select
            f.feed_id,
            f.title,
            f.link
          from
            syndicate.feed f,
            syndicate.syndicate_feed_matrix sfm
          where f.feed_id= sfm.feed_id
            and sfm.syndicate_id= %d
            and bz_id <= 20000',
          $this->getSyndicate_id($request->getProduct())
        );
        
        $c= &$response->addFormResult(new Node('feeds'));
        while ($q && $record= $q->next()) {
          $c->addChild(new Node('feed', NULL, array(
            'feed_id'   => $record['feed_id'],
            'title'     => $record['title'],
            'link'      => $record['link']
          )));
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      return TRUE;
    }

    /**
     * Return the syndicate_id for the given product.
     *
     * @access  public
     * @param   string product
     * @return  int id
     */
    function getSyndicate_id($product) {
      static $id= NULL;
      
      if (NULL !== $id) return $id;
      
      $pm= &PropertyManager::getInstance();
      $prop= &$pm->getProperties('products');
      
      $id= $prop->readString($product, 'syndicate');
      return $id;
    }
  }
