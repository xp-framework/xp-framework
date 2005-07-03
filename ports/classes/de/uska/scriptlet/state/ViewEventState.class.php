<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'de.uska.scriptlet.state.UskaState',
    'de.uska.db.Event',
    'de.uska.db.Player'
  );

  /**
   * View details for an event
   *
   * @purpose  View event
   */
  class ViewEventState extends UskaState {
    
    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     * @return  boolean
     */
    function process(&$request, &$response, &$context) {
      parent::process($request, $response, $context);
      
      $eventid= intval($request->getEnvValue('QUERY_STRING'));
      if (!$eventid) return FALSE;
      
      try(); {
        $event= &Event::getByEvent_id($eventid);
        
        $event && $query= &$this->db->query('
          select
            p.player_id,
            p.firstname,
            p.lastname,
            p.player_type_id,
            p.created_by,
            a.offers_seats,
            a.needs_driver,
            a.attend
          from
            event as e,
            player as p left outer join event_attendee as a on p.player_id= a.player_id and a.event_id= e.event_id
          where p.player_type_id= 1
            and p.team_id= e.team_id
            and e.event_id= %1$d
          
          union select
            p.player_id,
            p.firstname,
            p.lastname,
            p.player_type_id,
            p.created_by,
            a.offers_seats,
            a.needs_driver,
            a.attend
          from
            event_attendee as a,
            player as p
          where p.player_id= a.player_id
            and p.player_type_id= 2
            and a.event_id= %1$d
          ',
          $event->getEvent_id()
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      $node= &$response->addFormResult(Node::fromObject($event, 'event'));
      $n= &$node->addChild(new Node('attendeeinfo'));
      while ($query && $record= &$query->next()) {
        $t= &$n->addChild(new Node('player', NULL, $record));
        
        // For guests, select creator
        if (2 == $record['player_type_id']) {
          $creator= &Player::getByPlayer_id($record['created_by']);
          $t->addChild(Node::fromObject($creator, 'creator'));
        }
      }
    }
  }
?>
