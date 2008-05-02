<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.website.news.scriptlet.AbstractNewsState');

  /**
   * Handles /xml/home
   *
   * @purpose  State
   */
  class HomeState extends AbstractNewsState {

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {

      // Blog entries
      $conn= ConnectionManager::getInstance()->getByHost('news', 0);
      $n= $response->addFormResult(new Node('blog'));
      $q= $conn->query('
        select distinct 
          entry.id as id,
          entry.title as title,
          entry.timestamp as date
        from
          serendipity_entries entry,
          serendipity_entrycat matrix,
          serendipity_category category
        where
          (category.parentid = %1$d or category.categoryid = %1$d)
          and entry.id = matrix.entryid
          and matrix.categoryid = category.categoryid
          and entry.isdraft = "false"
        order by
          timestamp desc
        limit 7',
        self::MASTER_CATEGORY
      );

      while ($record= $q->next()) {
        $e= $n->addChild(new Node('entry', NULL, array('id' => $record['id'], 'link' => $this->sanitizeHref($record['title']))));
        $e->addChild(new Node('title', $record['title']));
        $e->addChild(Node::fromObject(new Date($record['timestamp']), 'date'));
        
        foreach ($this->categoriesOfEntry($record['id']) as $row) {
          $e->addChild(new Node('category', $row['category_name'], array(
            'link'  => $this->sanitizeHref($row['category_name']),
            'id'    => $row['categoryid']
          )));
        }
      }
    }
  }
?>
