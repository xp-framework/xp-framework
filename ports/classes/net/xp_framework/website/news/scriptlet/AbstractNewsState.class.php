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
   * Base class for all news states
   *
   * @purpose  Abstract base class
   */
  abstract class AbstractNewsState extends AbstractState {
    const
      MASTER_CATEGORY   = 8;

    /**
     * Retrieve parent category's ID
     *
     * @return  int
     */
    protected function getParentCategory($request) {
      return ($request->getEnvValue('CATID')
        ? $request->getEnvValue('CATID')
        : 8
      );        
    }
        
    /**
     * Create "sanitized" href from the given string. Replaces characters not
     * suitable for use in a URL
     *
     * @param   string $name
     * @return  string
     */
    protected function sanitizeHref($name) {
      return preg_replace('#[^a-zA-Z0-9\-\._]#', '_', $name);
    }
    
    /**
     * Retrieve categories an entry is in
     *
     * @param   int $id
     * @return  mixed[]
     */
    final protected function categoriesOfEntry($id) {
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
     * Insert category tree below given node
     *
     * @param   int $current
     * @param   xml.Node $node
     */
    final protected function insertCategories($current, $node) {
      $q= ConnectionManager::getInstance()->getByHost('news', 0)->query('
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
        self::MASTER_CATEGORY
      );
      while ($record= $q->next()) {
        $n= $node->addChild(new Node('category', $record['category_name'], array(
          'id'        => $record['categoryid'],
          'parentid'  => $record['parentid'],
          'link'      => $this->sanitizeHref($record['category_name'])
        )));
        if ($current == $record['categoryid']) {
          $n->setAttribute('current-category', 'true');
        }
      }
    }
  }
?>
