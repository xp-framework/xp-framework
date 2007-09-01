<?php
/* This class is part of the XP framework
 *
 * $Id: ViewEventState.class.php 6109 2005-11-12 12:41:58Z kiesel $ 
 */

  namespace de::uska::scriptlet::state;

  ::uses(
    'de.uska.scriptlet.state.UskaState',
    'de.uska.markup.FormresultHelper',
    'de.uska.db.Event',
    'de.uska.db.Player'
  );

  /**
   * View details for an event
   *
   * @purpose  View event
   */
  class ViewpointsEventState extends UskaState {
    
    /**
     * Process this state.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     * @return  boolean
     */
    public function process($request, $response, $context) {
      parent::process($request, $response, $context);
      
      $eventid= $request->getParam('event_id', 0);
      $teamid= $request->getParam('team_id', 0);
      
      // Bail out
      if (!$eventid && !$teamid) return TRUE;
      
      try {
        $eventid && $event= de::uska::db::Event::getByEvent_id($eventid);
        
        $query= $this->db->query('
          select
            p.player_id,
            p.firstname,
            p.lastname,
            sum(t.points) as points,
            count(*) as attendcount
          from
           player as p,
           event_points as t,
           event as e
          where t.player_id= p.player_id
            and t.event_id= e.event_id
            and e.event_type_id= 1      -- training
            %1$c
            %2$c
          group by
            p.player_id
          order by
            lastname, firstname
          ',
          ($eventid ? $this->db->prepare('and t.event_id= %d', $event->getEvent_id()) : ''),
          ($teamid ? $this->db->prepare('and e.team_id= %d', $teamid) : '')
        );
      } catch (rdbms::SQLException $e) {
        throw($e);
      }
      
      // Convert event object into array, so we can add it without
      // the description member (which needs markup processing)
      if ($event) {
        $eventarr= (array)$event;
        unset($eventarr['description']);

        $node= $response->addFormResult(::fromArray($eventarr, 'event'));
        $node->addChild(de::uska::markup::FormresultHelper::markupNodeFor('description', $event->getDescription()));
      }
      
      $n= $response->addFormResult(new ('attendeeinfo'));
      while ($query && $record= $query->next()) {
        $t= $n->addChild(new ('player', NULL, $record));
      }
    }
  }
?>
