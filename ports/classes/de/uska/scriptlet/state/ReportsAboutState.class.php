<?php
/* This class is part of the XP framework
 *
 * $Id: NewsState.class.php 4958 2005-04-09 19:58:22Z kiesel $ 
 */

  uses('de.uska.scriptlet.AbstractNewsListingState');

  /**
   * Display reports of games
   *
   * @purpose  Display reports.
   */
  class ReportsAboutState extends AbstractNewsListingState {
  
    /**
     * Retrieve parent category's ID
     *
     * @access  public
     * @return  int
     */
    function getParentCategory() {
      return 2;
    }

    /*
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
