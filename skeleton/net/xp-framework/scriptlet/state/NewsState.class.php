<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp-framework.scriptlet.AbstractNewsListingState');

  /**
   * Handles /xml/news
   *
   * @purpose  State
   */
  class NewsState extends AbstractNewsListingState {

    /**
     * Retrieve entries
     *
     * @access  protected
     * @param   &rdbms.DBConnection db
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @return  &rdbms.ResultSet
     */
    function &getEntries(&$db, &$request) { 
      return $db->query('
        select 
          entry.id as id,
          entry.title as title,
          entry.body as body,
          entry.author as author,
          entry.timestamp as timestamp,
          length(entry.extended) as extended_length,
          category.categoryid as category_id,
          category.category_name as category
        from
          serendipity_entries entry,
          serendipity_entrycat matrix,
          serendipity_category category
        where
          isdraft = "false"
          and entry.id = matrix.entryid
          and matrix.categoryid = category.categoryid
        order by
          timestamp desc
        limit 20
      ');
    }
  }
?>
