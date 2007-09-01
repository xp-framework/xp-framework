<?php
/* This class is part of the XP framework
 *
 * $Id: ResourcesState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::scriptlet::state;

  ::uses('net.xp_framework.scriptlet.AbstractNewsListingState');

  /**
   * Handles /xml/news
   *
   * @purpose  State
   */
  class ResourcesState extends net::xp_framework::scriptlet::AbstractNewsListingState {

    /**
     * Retrieve parent category's ID
     *
     * @return  int
     */
    public function getParentCategory() {
      return 5;
    }

    /**
     * Retrieve entries
     *
     * @param   &rdbms.DBConnection db
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @return  &rdbms.ResultSet
     */
    public function getEntries($db, $request) { 
      return $db->query('
        select 
          entry.id as id,
          entry.title as title,
          entry.body as body,
          entry.author as author,
          entry.timestamp as timestamp,
          length(entry.extended) as extended_length,
          category.categoryid as category_id,
          category.category_name as category,
          (select count(*) from serendipity_comments c where c.entry_id = entry.id) as num_comments
        from
          serendipity_entries entry,
          serendipity_entrycat matrix,
          serendipity_category category
        where
          (category.parentid = %1$d or category.categoryid = %1$d)
          and entry.isdraft = "false"
          and entry.id = matrix.entryid
          and matrix.categoryid = category.categoryid
        order by
          timestamp desc
        limit 20',
        $this->getParentCategory()
      );
    }
  }
?>
