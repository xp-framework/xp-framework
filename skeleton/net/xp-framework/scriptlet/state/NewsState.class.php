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
          length(entry.extended) as extended_length
        from
          serendipity_entries entry
        where
          isdraft = "false"
        order by
          timestamp desc
        limit 20
      ');
    }

  }
?>
