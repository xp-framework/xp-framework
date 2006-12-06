<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'de.uska.scriptlet.state.UskaState',
    'de.uska.markup.FormresultHelper',
    'de.uska.db.Event',
    'de.uska.db.Player',
    'util.DateUtil'
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
    public function process(&$request, &$response, &$context) {
      parent::process($request, $response, $context);
      
      $eventid= intval($request->getQueryString());
      if (!$eventid) return FALSE;
      
      try {
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
            and p.bz_id= 20000
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
            and a.attend= 1
            
          order by
            attend desc, player_type_id, lastname, firstname
          ',
          $event->getEvent_id()
        );
      } catch (SQLException $e) {
        throw($e);
      }
      
      // Convert event object into array, so we can add it without
      // the description member (which needs markup processing)
      $eventarr= (array)$event;
      unset($eventarr['description']);
      $deadline= &$event->getDeadline();
      $target= &$event->getTarget_date();
      $attendeesCount= 0;
      
      $n= new Node('attendeeinfo');
      while ($query && $record= &$query->next()) {
        $t= &$n->addChild(new Node('player', NULL, $record));
        
        // For guests, select creator
        if (2 == $record['player_type_id']) {
          $creator= &Player::getByPlayer_id($record['created_by']);
          $t->addChild(Node::fromObject($creator, 'creator'));
        }
        
        // Count attendees
        if ($record['attend']) $attendeesCount++;
      }
      
      // Check whether this event is still subscribeable
      $eventarr['subscribeable']= (int)
        ((!$deadline || $deadline->isAfter(Date::now())) && 
        $target->isAfter(Date::now()) &&
        (!$event->getMax_Attendees() || $attendeesCount < $event->getMax_attendees())
      );
      
      $node= &$response->addFormResult(Node::fromArray($eventarr, 'event'));
      $node->addChild(FormresultHelper::markupNodeFor('description', $event->getDescription()));
      $node->addChild($n);
    }
  }
?>
