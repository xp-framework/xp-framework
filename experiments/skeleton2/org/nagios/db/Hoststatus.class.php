<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.ConnectionManager');

  /**
   * Class wrapper for table hoststatus, database nagios
   * (Auto-generated on Tue, 25 Nov 2003 12:50:31 +0100 by alex)
   *
   * @purpose  Datasource accessor
   */
  class Hoststatus extends Object {
    public
      $host_name= '',
      $host_status= '',
      $last_update= NULL,
      $last_check= NULL,
      $last_state_change= NULL,
      $problem_acknowledged= 0,
      $time_up= 0,
      $time_down= 0,
      $time_unreachable= 0,
      $last_notification= NULL,
      $current_notification= 0,
      $notifications_enabled= 0,
      $event_handler_enabled= 0,
      $checks_enabled= 0,
      $plugin_output= '',
      $flap_detection_enabled= 0,
      $is_flapping= 0,
      $percent_state_change= '',
      $scheduled_downtime_depth= 0,
      $failure_prediction_enabled= 0,
      $process_performance_data= 0;

    /**
     * Gets the service status by hostname
     *
     * @access  public
     * @param   string hostname
     * @return  &Hoststatus[] object
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    public function getByHost_name($host_name) {
      $cm= ConnectionManager::getInstance();  
      if (FALSE === ($db= $cm->getByHost('nagios', 0))) {
        throw (new IllegalAccessException('No connection to "nagios" available'));
      }

      try {
        $q= $db->query ('
          select
            host_name,
            host_status,
            last_update,
            last_check,
            last_state_change,
            problem_acknowledged,
            time_up,
            time_down,
            time_unreachable,
            last_notification,
            current_notification,
            notifications_enabled,
            event_handler_enabled,
            checks_enabled,
            plugin_output,
            flap_detection_enabled,
            is_flapping,
            percent_state_change,
            scheduled_downtime_depth,
            failure_prediction_enabled,
            process_performance_data
          from
            nagios.hoststatus
          where
            host_name= %s',
          $host_name);

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= new Hoststatus($r);
        }
      } catch (SQLException $e) {
        throw ($e);
      }

      return $data;
    }

    /**
     * Gets the service status by hostname
     *
     * @access  public
     * @param   string hoststatus
     * @return  &Hoststatus[] object
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    public function getByHost_status($host_status) {
      $cm= ConnectionManager::getInstance();  
      if (FALSE === ($db= $cm->getByHost('nagios', 0))) {
        throw (new IllegalAccessException('No connection to "nagios" available'));
      }

      try {
        $q= $db->query ('
          select
            host_name,
            host_status,
            last_update,
            last_check,
            last_state_change,
            problem_acknowledged,
            time_up,
            time_down,
            time_unreachable,
            last_notification,
            current_notification,
            notifications_enabled,
            event_handler_enabled,
            checks_enabled,
            plugin_output,
            flap_detection_enabled,
            is_flapping,
            percent_state_change,
            scheduled_downtime_depth,
            failure_prediction_enabled,
            process_performance_data
          from
            nagios.hoststatus
          where
            host_status= %s',
          $host_status);

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= new Hoststatus($r);
        }
      } catch (SQLException $e) {
        throw ($e);
      }

      return $data;
    }
    
    /**
     * Gets the service status by hostname
     *
     * @access  public
     * @return  &Hoststatus[] object
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    public function getByNotUp() {
      $cm= ConnectionManager::getInstance();  
      if (FALSE === ($db= $cm->getByHost('nagios', 0))) {
        throw (new IllegalAccessException('No connection to "nagios" available'));
      }

      try {
        $q= $db->query ('
          select
            host_name,
            host_status,
            last_update,
            last_check,
            last_state_change,
            problem_acknowledged,
            time_up,
            time_down,
            time_unreachable,
            last_notification,
            current_notification,
            notifications_enabled,
            event_handler_enabled,
            checks_enabled,
            plugin_output,
            flap_detection_enabled,
            is_flapping,
            percent_state_change,
            scheduled_downtime_depth,
            failure_prediction_enabled,
            process_performance_data
          from
            nagios.hoststatus
          where
            host_status != %s',
          'UP');

        $data= array();
        if ($q) while ($r= $q->next()) {
          $data[]= new Hoststatus($r);
        }
      } catch (SQLException $e) {
        throw ($e);
      }

      return $data;
    }
    
    /**
     * Retrieves host_name
     *
     * @access  public
     * @return  string
     */
    public function getHost_name() {
      return $this->host_name;
    }
      
    /**
     * Sets host_name
     *
     * @access  public
     * @param   string host_name
     */
    public function setHost_name($host_name) {
      $this->host_name= $host_name;
    }
      
    /**
     * Retrieves host_status
     *
     * @access  public
     * @return  string
     */
    public function getHost_status() {
      return $this->host_status;
    }
      
    /**
     * Sets host_status
     *
     * @access  public
     * @param   string host_status
     */
    public function setHost_status($host_status) {
      $this->host_status= $host_status;
    }
      
    /**
     * Retrieves last_update
     *
     * @access  public
     * @return  util.Date
     */
    public function getLast_update() {
      return $this->last_update;
    }
      
    /**
     * Sets last_update
     *
     * @access  public
     * @param   util.Date last_update
     */
    public function setLast_update(Date $last_update) {
      $this->last_update= $last_update;
    }
      
    /**
     * Retrieves last_check
     *
     * @access  public
     * @return  util.Date
     */
    public function getLast_check() {
      return $this->last_check;
    }
      
    /**
     * Sets last_check
     *
     * @access  public
     * @param   util.Date last_check
     */
    public function setLast_check(Date $last_check) {
      $this->last_check= $last_check;
    }
      
    /**
     * Retrieves last_state_change
     *
     * @access  public
     * @return  util.Date
     */
    public function getLast_state_change() {
      return $this->last_state_change;
    }
      
    /**
     * Sets last_state_change
     *
     * @access  public
     * @param   util.Date last_state_change
     */
    public function setLast_state_change(Date $last_state_change) {
      $this->last_state_change= $last_state_change;
    }
      
    /**
     * Retrieves problem_acknowledged
     *
     * @access  public
     * @return  int
     */
    public function getProblem_acknowledged() {
      return $this->problem_acknowledged;
    }
      
    /**
     * Sets problem_acknowledged
     *
     * @access  public
     * @param   int problem_acknowledged
     */
    public function setProblem_acknowledged($problem_acknowledged) {
      $this->problem_acknowledged= $problem_acknowledged;
    }
      
    /**
     * Retrieves time_up
     *
     * @access  public
     * @return  int
     */
    public function getTime_up() {
      return $this->time_up;
    }
      
    /**
     * Sets time_up
     *
     * @access  public
     * @param   int time_up
     */
    public function setTime_up($time_up) {
      $this->time_up= $time_up;
    }
      
    /**
     * Retrieves time_down
     *
     * @access  public
     * @return  int
     */
    public function getTime_down() {
      return $this->time_down;
    }
      
    /**
     * Sets time_down
     *
     * @access  public
     * @param   int time_down
     */
    public function setTime_down($time_down) {
      $this->time_down= $time_down;
    }
      
    /**
     * Retrieves time_unreachable
     *
     * @access  public
     * @return  int
     */
    public function getTime_unreachable() {
      return $this->time_unreachable;
    }
      
    /**
     * Sets time_unreachable
     *
     * @access  public
     * @param   int time_unreachable
     */
    public function setTime_unreachable($time_unreachable) {
      $this->time_unreachable= $time_unreachable;
    }
      
    /**
     * Retrieves last_notification
     *
     * @access  public
     * @return  util.Date
     */
    public function getLast_notification() {
      return $this->last_notification;
    }
      
    /**
     * Sets last_notification
     *
     * @access  public
     * @param   util.Date last_notification
     */
    public function setLast_notification(Date $last_notification) {
      $this->last_notification= $last_notification;
    }
      
    /**
     * Retrieves current_notification
     *
     * @access  public
     * @return  int
     */
    public function getCurrent_notification() {
      return $this->current_notification;
    }
      
    /**
     * Sets current_notification
     *
     * @access  public
     * @param   int current_notification
     */
    public function setCurrent_notification($current_notification) {
      $this->current_notification= $current_notification;
    }
      
    /**
     * Retrieves notifications_enabled
     *
     * @access  public
     * @return  int
     */
    public function getNotifications_enabled() {
      return $this->notifications_enabled;
    }
      
    /**
     * Sets notifications_enabled
     *
     * @access  public
     * @param   int notifications_enabled
     */
    public function setNotifications_enabled($notifications_enabled) {
      $this->notifications_enabled= $notifications_enabled;
    }
      
    /**
     * Retrieves event_handler_enabled
     *
     * @access  public
     * @return  int
     */
    public function getEvent_handler_enabled() {
      return $this->event_handler_enabled;
    }
      
    /**
     * Sets event_handler_enabled
     *
     * @access  public
     * @param   int event_handler_enabled
     */
    public function setEvent_handler_enabled($event_handler_enabled) {
      $this->event_handler_enabled= $event_handler_enabled;
    }
      
    /**
     * Retrieves checks_enabled
     *
     * @access  public
     * @return  int
     */
    public function getChecks_enabled() {
      return $this->checks_enabled;
    }
      
    /**
     * Sets checks_enabled
     *
     * @access  public
     * @param   int checks_enabled
     */
    public function setChecks_enabled($checks_enabled) {
      $this->checks_enabled= $checks_enabled;
    }
      
    /**
     * Retrieves plugin_output
     *
     * @access  public
     * @return  string
     */
    public function getPlugin_output() {
      return $this->plugin_output;
    }
      
    /**
     * Sets plugin_output
     *
     * @access  public
     * @param   string plugin_output
     */
    public function setPlugin_output($plugin_output) {
      $this->plugin_output= $plugin_output;
    }
      
    /**
     * Retrieves flap_detection_enabled
     *
     * @access  public
     * @return  int
     */
    public function getFlap_detection_enabled() {
      return $this->flap_detection_enabled;
    }
      
    /**
     * Sets flap_detection_enabled
     *
     * @access  public
     * @param   int flap_detection_enabled
     */
    public function setFlap_detection_enabled($flap_detection_enabled) {
      $this->flap_detection_enabled= $flap_detection_enabled;
    }
      
    /**
     * Retrieves is_flapping
     *
     * @access  public
     * @return  int
     */
    public function getIs_flapping() {
      return $this->is_flapping;
    }
      
    /**
     * Sets is_flapping
     *
     * @access  public
     * @param   int is_flapping
     */
    public function setIs_flapping($is_flapping) {
      $this->is_flapping= $is_flapping;
    }
      
    /**
     * Retrieves percent_state_change
     *
     * @access  public
     * @return  string
     */
    public function getPercent_state_change() {
      return $this->percent_state_change;
    }
      
    /**
     * Sets percent_state_change
     *
     * @access  public
     * @param   string percent_state_change
     */
    public function setPercent_state_change($percent_state_change) {
      $this->percent_state_change= $percent_state_change;
    }
      
    /**
     * Retrieves scheduled_downtime_depth
     *
     * @access  public
     * @return  int
     */
    public function getScheduled_downtime_depth() {
      return $this->scheduled_downtime_depth;
    }
      
    /**
     * Sets scheduled_downtime_depth
     *
     * @access  public
     * @param   int scheduled_downtime_depth
     */
    public function setScheduled_downtime_depth($scheduled_downtime_depth) {
      $this->scheduled_downtime_depth= $scheduled_downtime_depth;
    }
      
    /**
     * Retrieves failure_prediction_enabled
     *
     * @access  public
     * @return  int
     */
    public function getFailure_prediction_enabled() {
      return $this->failure_prediction_enabled;
    }
      
    /**
     * Sets failure_prediction_enabled
     *
     * @access  public
     * @param   int failure_prediction_enabled
     */
    public function setFailure_prediction_enabled($failure_prediction_enabled) {
      $this->failure_prediction_enabled= $failure_prediction_enabled;
    }
      
    /**
     * Retrieves process_performance_data
     *
     * @access  public
     * @return  int
     */
    public function getProcess_performance_data() {
      return $this->process_performance_data;
    }
      
    /**
     * Sets process_performance_data
     *
     * @access  public
     * @param   int process_performance_data
     */
    public function setProcess_performance_data($process_performance_data) {
      $this->process_performance_data= $process_performance_data;
    }
      
    /**
     * Update this object in the database
     *
     * @access  public
     * @return  boolean success
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    public function update() {
      $cm= ConnectionManager::getInstance();  
      if (FALSE === ($db= $cm->getByHost('nagios', 0))) {
        throw (new IllegalAccessException('No connection to "nagios" available'));
      }

      try {
        $db->update('
          nagios.hoststatus set
            host_name = %s,
            host_status = %s,
            last_update = %s,
            last_check = %s,
            last_state_change = %s,
            problem_acknowledged = %d,
            time_up = %d,
            time_down = %d,
            time_unreachable = %d,
            last_notification = %s,
            current_notification = %d,
            notifications_enabled = %d,
            event_handler_enabled = %d,
            checks_enabled = %d,
            plugin_output = %s,
            flap_detection_enabled = %d,
            is_flapping = %d,
            percent_state_change = %s,
            scheduled_downtime_depth = %d,
            failure_prediction_enabled = %d,
            process_performance_data = %d
          where
            
          ',
          $this->host_name,
          $this->host_status,
          $this->last_update,
          $this->last_check,
          $this->last_state_change,
          $this->problem_acknowledged,
          $this->time_up,
          $this->time_down,
          $this->time_unreachable,
          $this->last_notification,
          $this->current_notification,
          $this->notifications_enabled,
          $this->event_handler_enabled,
          $this->checks_enabled,
          $this->plugin_output,
          $this->flap_detection_enabled,
          $this->is_flapping,
          $this->percent_state_change,
          $this->scheduled_downtime_depth,
          $this->failure_prediction_enabled,
          $this->process_performance_data
        );
      } catch (SQLException $e) {
        throw ($e);
      }

      return TRUE;
    }
    
    /**
     * Write this object to the database
     *
     * @access  public
     * @return  boolean success
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    public function insert() {
      $cm= ConnectionManager::getInstance();  
      if (FALSE === ($db= $cm->getByHost('nagios', 0))) {
        throw (new IllegalAccessException('No connection to "nagios" available'));
      }

      try {
        $db->insert('
          nagios.hoststatus (
            host_name,
            host_status,
            last_update,
            last_check,
            last_state_change,
            problem_acknowledged,
            time_up,
            time_down,
            time_unreachable,
            last_notification,
            current_notification,
            notifications_enabled,
            event_handler_enabled,
            checks_enabled,
            plugin_output,
            flap_detection_enabled,
            is_flapping,
            percent_state_change,
            scheduled_downtime_depth,
            failure_prediction_enabled,
            process_performance_data
          ) values (
            %s, %s, %s, %s, %s, %d, %d, %d, %d, %s, %d, %d, %d, %d, %s, %d, %d, %s, %d, %d, %d
          )',
          $this->host_name,
          $this->host_status,
          $this->last_update,
          $this->last_check,
          $this->last_state_change,
          $this->problem_acknowledged,
          $this->time_up,
          $this->time_down,
          $this->time_unreachable,
          $this->last_notification,
          $this->current_notification,
          $this->notifications_enabled,
          $this->event_handler_enabled,
          $this->checks_enabled,
          $this->plugin_output,
          $this->flap_detection_enabled,
          $this->is_flapping,
          $this->percent_state_change,
          $this->scheduled_downtime_depth,
          $this->failure_prediction_enabled,
          $this->process_performance_data
        );

      } catch (SQLException $e) {
        throw ($e);
      }

      return TRUE;
    }
    
  }
?>
