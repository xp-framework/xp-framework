<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.planet-xp.scriptlet.AbstractEntryState'
  );

  /**
   * Handles /xml/home
   *
   * @purpose  State
   */
  class HomeState extends AbstractEntryState {

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
          i.feeditem_id,
          i.title,
          i.content,
          i.link,
          i.author,
          i.published,
          f.feed_id,
          f.title as feedtitle,
          f.link as feedlink,
          f.description,
          f.author as feedauthor,
          a.author_to as author_translated
        from
          syndicate.feed f,
          syndicate.syndicate_feed_matrix sfm,
          syndicate.feeditem i left outer join syndicate.authormapping a
            on i.feed_id= a.feed_id
            and i.author= a.author_from
        where f.feed_id= i.feed_id
          and f.feed_id= sfm.feed_id
          and sfm.syndicate_id= %d
          and f.bz_id <= 20000
        order by published desc
        limit %d,%d',
        $this->getSyndicate_id($request->getProduct()),
        $request->getParam('offset', 0),
        10
      );
    }

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    function process(&$request, &$response) {
      parent::process($request, $response);
    
      $response->addFormResult(new Node('offset', $request->getParam('offset', 0)));
    }
  }
?>
