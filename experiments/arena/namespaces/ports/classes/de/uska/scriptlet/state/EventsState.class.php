<?php
/* This class is part of the XP framework
 *
 * $Id: EventsState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::uska::scriptlet::state;

  ::uses(
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
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    public function process($request, $response, $context) {
      // FIXME: Put into ini-file?
      static $types= array(
        'training'    => 1,
        'tournament'  => 2,
        'misc'        => 3,
        'enbw'        => 4
      );
      parent::process($request, $response, $context);
      
      try {
        $team= FALSE;
        $type= FALSE;
        $all=  FALSE;
        $year= $month= $day= FALSE;
        with ($env= $request->getQueryString()); {
          if (strlen($env)) @list($type, $all, $team, $year, $month, $day)= explode(',', $env);
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
            %c
            %c
            %c
          order by e.target_date asc',
          ($team ? $this->db->prepare('and e.team_id= %d', $team) : ''),
          ($type && isset($types[$type]) ? $this->db->prepare('and e.event_type_id= %d', $types[$type]) : ''),
          ($all  ? '' : $this->db->prepare('and e.target_date > now()')),
          ($year ? $this->db->prepare('and year(e.target_date)= %d', $year) : ''),
          ($month ? $this->db->prepare('and month(e.target_date)= %d', $month) : ''),
          ($day  ? $this->db->prepare('and day(e.target_date)= %d', $day) : '')
        );
      } catch (rdbms::SQLException $e) {
        throw($e);
      }
      
      $events= $response->addFormResult(new ('events', NULL, array(
        'team'  => intval($team),
        'type'  => ($type ? $type : '0'),
        'all'   => intval($all),
        'year'  => intval($year),
        'month' => intval($month),
        'day'   => intval($day)
      )));
      while ($q && $record= $q->next()) {
        $description= $record['description'];
        unset($record['description']);
        
        $n= $events->addChild(::fromArray($record, 'event'));
        $n->addChild(de::uska::markup::FormresultHelper::markupNodeFor('description', $description));
      }
      
      // Create context date
      $date= NULL;
      if ($year && $month) {
        $date= util::Date::fromString(sprintf('%d-%d-%d',
          $year,
          $month,
          $day ? $day : 1
        ));
      }
      
      $this->insertEventCalendar($request, $response, $team, $date);
    }
  }
?>
