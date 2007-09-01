<?php
/* This class is part of the XP framework
 *
 * $Id: ViewEventState.class.php 10655 2007-06-23 15:37:59Z kiesel $ 
 */

  namespace de::uska::scriptlet::state;

  ::uses(
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
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     * @return  boolean
     */
    public function process($request, $response, $context) {
      parent::process($request, $response, $context);
      
      $event= de::uska::db::Event::getByEvent_id(intval($request->getQueryString()));
      if (!$event) return FALSE;

      $event && $query= $this->db->query('
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
      
      // Convert event object into array, so we can add it without
      // the description member (which needs markup processing)
      $eventarr= (array)get_object_vars($event);
      unset($eventarr['description']);
      
      $attendeesCount= 0;
      $n= new ('attendeeinfo');
      while ($query && $record= $query->next()) {
        $t= $n->addChild(new ('player', NULL, $record));
        
        // For guests, select creator
        if (2 == $record['player_type_id']) {
          $t->addChild(::fromObject(de::uska::db::Player::getByPlayer_id($record['created_by']), 'creator'));
        }
        
        // Count attendees
        if ($record['attend']) $attendeesCount++;
      }
      
      // Check whether this event is still subscribeable
      $eventarr['subscribeable']= (int)
        ((!$event->getDeadline() || $event->getDeadline()->isAfter(util::Date::now())) && 
        $event->getTarget_date()->isAfter(util::Date::now()) &&
        (!$event->getMax_Attendees() || $attendeesCount < $event->getMax_attendees())
      );
      
      $node= $response->addFormResult(::fromArray($eventarr, 'event'));
      $node->addChild(de::uska::markup::FormresultHelper::markupNodeFor('description', $event->getDescription()));
      $node->addChild($n);
    }
  }
?>
