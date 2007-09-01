<?php
/* This class is part of the XP framework
 *
 * $Id: NewsState.class.php 5294 2005-07-03 14:16:33Z kiesel $ 
 */

  namespace de::uska::scriptlet::state;

  ::uses('de.uska.scriptlet.AbstractNewsListingState');

  /**
   * Display news on news state.
   *
   * @purpose  Display news.
   */
  class OrganizationState extends de::uska::scriptlet::AbstractNewsListingState {
  
    /**
     * Retrieve parent category's ID
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @return  int
     */
    public function getParentCategory($request) {
      switch ($request->getQueryString()) {
        case 'application': return 3;
        case 'imprint': return 5;
        case 'contact': return 6;
        default: return 4;
      }
    }

    /*
     * Retrieve entries
     *
     * @access  protected
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
          category.categoryid = %1$d
          and entry.isdraft = "false"
          and entry.id = matrix.entryid
          and matrix.categoryid = category.categoryid
        order by
          timestamp desc
        limit 20',
        $this->getParentCategory($request)
      );
    }
  }
