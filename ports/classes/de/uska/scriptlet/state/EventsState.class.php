<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'de.uska.scriptlet.state.UskaState',
    'de.uska.markup.FormresultHelper'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class EventsState extends UskaState {
  
    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    function process(&$request, &$response, &$context) {
      // FIXME: Put into ini-file?
      static $types= array(
        'training'    => 1,
        'tournament'  => 2,
        'misc'        => 3,
        'enbw'        => 4
      );
      parent::process($request, $response, $context);
      
      try(); {
        $team= FALSE;
        $type= FALSE;
        $all=  FALSE;
        with ($env= $request->getQueryString()); {
          if (strlen($env)) @list($type, $all, $team)= explode(',', $env);
        }
        
        $q= $this->db->query('
          select
            e.event_id,
            e.team_id,
            t.name as teamname,
            e.name,
            e.description,
            e.target_date,
            e.deadline,
            e.max_attendees,
            e.req_attendees,
            e.allow_guests,
            e.event_type_id,
            e.lastchange,
            e.changedby
          from
            event as e,
            team as t
          where t.team_id= e.team_id
            %c
            %c
            %c
          order by e.target_date asc',
          ($team ? $this->db->prepare('and e.team_id= %d', $team) : ''),
          ($type ? $this->db->prepare('and e.event_type_id= %d', $types[$type]) : ''),
          ($all  ? '' : $this->db->prepare('and e.target_date > now()'))
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      $events= &$response->addFormResult(new Node('events'));
      while ($q && $record= $q->next()) {
        $description= $record['description'];
        unset($record['description']);
        
        $n= &$events->addChild(Node::fromArray($record, 'event'));
        $n->addChild(FormresultHelper::markupNodeFor('description', $description));
      }
      
      $this->insertEventCalendar($request, $response, $team);
    }
  }
?>
