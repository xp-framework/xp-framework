<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table servicestatus, database nagios
   * (Auto-generated on Tue, 25 Nov 2003 12:54:46 +0100 by alex)
   *
   * @purpose  Datasource accessor
   */
  class Servicestatus extends DataSet {
    public
      $host_name= '',
      $service_description= '',
      $service_status= '',
      $last_update= NULL,
      $current_attempt= 0,
      $max_attempts= 0,
      $state_type= '',
      $last_check= NULL,
      $next_check= NULL,
      $should_be_scheduled= 0,
      $check_type= '',
      $checks_enabled= 0,
      $accept_passive_checks= 0,
      $event_handler_enabled= 0,
      $last_state_change= NULL,
      $problem_acknowledged= 0,
      $last_hard_state= '',
      $time_ok= 0,
      $time_warning= 0,
      $time_unknown= 0,
      $time_critical= 0,
      $last_notification= NULL,
      $current_notification= 0,
      $notifications_enabled= 0,
      $latency= 0,
      $execution_time= 0,
      $plugin_output= '',
      $flap_detection_enabled= 0,
      $is_flapping= 0,
      $percent_state_change= '',
      $scheduled_downtime_depth= 0,
      $failure_prediction_enabled= 0,
      $process_performance_data= 0,
      $obsess_over_service= 0;

    /**
     * Gets an instance of this object by hostname
     *
     * @param   string hostname
     * @return  org.nagios.db.Servicestatus[] object
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    public function getByHost_name($host_name) {
      $q= ConnectionManager::getInstance()->getByHost('nagios', 0)->query('
        select
          host_name,
          service_description,
          service_status,
          last_update,
          current_attempt,
          max_attempts,
          state_type,
          last_check,
          next_check,
          should_be_scheduled,
          check_type,
          checks_enabled,
          accept_passive_checks,
          event_handler_enabled,
          last_state_change,
          problem_acknowledged,
          last_hard_state,
          time_ok,
          time_warning,
          time_unknown,
          time_critical,
          last_notification,
          current_notification,
          notifications_enabled,
          latency,
          execution_time,
          plugin_output,
          flap_detection_enabled,
          is_flapping,
          percent_state_change,
          scheduled_downtime_depth,
          failure_prediction_enabled,
          process_performance_data,
          obsess_over_service
        from
          nagios.servicestatus
        where host_name= %s',
        $host_name);

      $data= array();
      if ($q) while ($r= $q->next()) {
        $data[]= new Servicestatus($r);
      }
      return $data;
    }

    /**
     * Gets an instance of this object by hostname and service
     *
     * @param   string hostname
     * @param   string servicedescription
     * @return  org.nagios.db.Servicestatus[] object
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    public function getByHostService($host_name, $service) {
      $q= ConnectionManager::getInstance()->getByHost('nagios', 0)->query('
        select
          host_name,
          service_description,
          service_status,
          last_update,
          current_attempt,
          max_attempts,
          state_type,
          last_check,
          next_check,
          should_be_scheduled,
          check_type,
          checks_enabled,
          accept_passive_checks,
          event_handler_enabled,
          last_state_change,
          problem_acknowledged,
          last_hard_state,
          time_ok,
          time_warning,
          time_unknown,
          time_critical,
          last_notification,
          current_notification,
          notifications_enabled,
          latency,
          execution_time,
          plugin_output,
          flap_detection_enabled,
          is_flapping,
          percent_state_change,
          scheduled_downtime_depth,
          failure_prediction_enabled,
          process_performance_data,
          obsess_over_service
        from
          nagios.servicestatus
        where host_name= %s
          and service_description= %s',
        $host_name,
        $service);

      $data= array();
      if ($q) while ($r= $q->next()) {
        $data[]= new Servicestatus($r);
      }
      return $data;
    }

    /**
     * Gets an instance of this object by service status
     *
     * @param   string status
     * @return  org.nagios.db.Servicestatus[] object
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    public function getByService_status($service_status) {
      $q= ConnectionManager::getInstance()->getByHost('nagios', 0)->query('
        select
          host_name,
          service_description,
          service_status,
          last_update,
          current_attempt,
          max_attempts,
          state_type,
          last_check,
          next_check,
          should_be_scheduled,
          check_type,
          checks_enabled,
          accept_passive_checks,
          event_handler_enabled,
          last_state_change,
          problem_acknowledged,
          last_hard_state,
          time_ok,
          time_warning,
          time_unknown,
          time_critical,
          last_notification,
          current_notification,
          notifications_enabled,
          latency,
          execution_time,
          plugin_output,
          flap_detection_enabled,
          is_flapping,
          percent_state_change,
          scheduled_downtime_depth,
          failure_prediction_enabled,
          process_performance_data,
          obsess_over_service
        from
          nagios.servicestatus
        where service_status= %s',
        $service_status);

      $data= array();
      if ($q) while ($r= $q->next()) {
        $data[]= new Servicestatus($r);
      }
      return $data;
    }

    /**
     * Gets an instance of this object with service status of not OK
     *
     * @return  org.nagios.db.Servicestatus[] object
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    public function getByNotOk() {
      $q= ConnectionManager::getInstance()->getByHost('nagios', 0)->query('
        select
          host_name,
          service_description,
          service_status,
          last_update,
          current_attempt,
          max_attempts,
          state_type,
          last_check,
          next_check,
          should_be_scheduled,
          check_type,
          checks_enabled,
          accept_passive_checks,
          event_handler_enabled,
          last_state_change,
          problem_acknowledged,
          last_hard_state,
          time_ok,
          time_warning,
          time_unknown,
          time_critical,
          last_notification,
          current_notification,
          notifications_enabled,
          latency,
          execution_time,
          plugin_output,
          flap_detection_enabled,
          is_flapping,
          percent_state_change,
          scheduled_downtime_depth,
          failure_prediction_enabled,
          process_performance_data,
          obsess_over_service
        from
          nagios.servicestatus
        where service_status != %s',
        'OK');
        
      $data= array();
      if ($q) while ($r= $q->next()) {
        $data[]= new Servicestatus($r);
      }
    }

    /**
     * Retrieves host_name
     *
     * @return  string
     */
    public function getHost_name() {
      return $this->host_name;
    }
      
    /**
     * Sets host_name
     *
     * @param   string host_name
     * @return  string previous value
     */
    public function setHost_name($host_name) {
      return $this->_change('host_name', $host_name, '%s');
    }
      
    /**
     * Retrieves service_description
     *
     * @return  string
     */
    public function getService_description() {
      return $this->service_description;
    }
      
    /**
     * Sets service_description
     *
     * @param   string service_description
     * @return  string previous value
     */
    public function setService_description($service_description) {
      return $this->_change('service_description', $service_description, '%s');
    }
      
    /**
     * Retrieves service_status
     *
     * @return  string
     */
    public function getService_status() {
      return $this->service_status;
    }
      
    /**
     * Sets service_status
     *
     * @param   string service_status
     * @return  string previous value
     */
    public function setService_status($service_status) {
      return $this->_change('service_status', $service_status, '%s');
    }
      
    /**
     * Retrieves last_update
     *
     * @return  util.Date
     */
    public function getLast_update() {
      return $this->last_update;
    }
      
    /**
     * Sets last_update
     *
     * @param   util.Date last_update
     * @return  util.Date previous value
     */
    public function setLast_update($last_update) {
      return $this->_change('last_update', $last_update, '%s');
    }
      
    /**
     * Retrieves current_attempt
     *
     * @return  int
     */
    public function getCurrent_attempt() {
      return $this->current_attempt;
    }
      
    /**
     * Sets current_attempt
     *
     * @param   int current_attempt
     * @return  int previous value
     */
    public function setCurrent_attempt($current_attempt) {
      return $this->_change('current_attempt', $current_attempt, '%d');
    }
      
    /**
     * Retrieves max_attempts
     *
     * @return  int
     */
    public function getMax_attempts() {
      return $this->max_attempts;
    }
      
    /**
     * Sets max_attempts
     *
     * @param   int max_attempts
     * @return  int previous value
     */
    public function setMax_attempts($max_attempts) {
      return $this->_change('max_attempts', $max_attempts, '%d');
    }
      
    /**
     * Retrieves state_type
     *
     * @return  string
     */
    public function getState_type() {
      return $this->state_type;
    }
      
    /**
     * Sets state_type
     *
     * @param   string state_type
     * @return  string previous value
     */
    public function setState_type($state_type) {
      return $this->_change('state_type', $state_type, '%s');
    }
      
    /**
     * Retrieves last_check
     *
     * @return  util.Date
     */
    public function getLast_check() {
      return $this->last_check;
    }
      
    /**
     * Sets last_check
     *
     * @param   util.Date last_check
     * @return  util.Date previous value
     */
    public function setLast_check($last_check) {
      return $this->_change('last_check', $last_check, '%s');
    }
      
    /**
     * Retrieves next_check
     *
     * @return  util.Date
     */
    public function getNext_check() {
      return $this->next_check;
    }
      
    /**
     * Sets next_check
     *
     * @param   util.Date next_check
     * @return  util.Date previous value
     */
    public function setNext_check($next_check) {
      return $this->_change('next_check', $next_check, '%s');
    }
      
    /**
     * Retrieves should_be_scheduled
     *
     * @return  int
     */
    public function getShould_be_scheduled() {
      return $this->should_be_scheduled;
    }
      
    /**
     * Sets should_be_scheduled
     *
     * @param   int should_be_scheduled
     * @return  int previous value
     */
    public function setShould_be_scheduled($should_be_scheduled) {
      return $this->_change('should_be_scheduled', $should_be_scheduled, '%d');
    }
      
    /**
     * Retrieves check_type
     *
     * @return  string
     */
    public function getCheck_type() {
      return $this->check_type;
    }
      
    /**
     * Sets check_type
     *
     * @param   string check_type
     * @return  string previous value
     */
    public function setCheck_type($check_type) {
      return $this->_change('check_type', $check_type, '%s');
    }
      
    /**
     * Retrieves checks_enabled
     *
     * @return  int
     */
    public function getChecks_enabled() {
      return $this->checks_enabled;
    }
      
    /**
     * Sets checks_enabled
     *
     * @param   int checks_enabled
     * @return  int previous value
     */
    public function setChecks_enabled($checks_enabled) {
      return $this->_change('checks_enabled', $checks_enabled, '%d');
    }
      
    /**
     * Retrieves accept_passive_checks
     *
     * @return  int
     */
    public function getAccept_passive_checks() {
      return $this->accept_passive_checks;
    }
      
    /**
     * Sets accept_passive_checks
     *
     * @param   int accept_passive_checks
     * @return  int previous value
     */
    public function setAccept_passive_checks($accept_passive_checks) {
      return $this->_change('accept_passive_checks', $accept_passive_checks, '%d');
    }
      
    /**
     * Retrieves event_handler_enabled
     *
     * @return  int
     */
    public function getEvent_handler_enabled() {
      return $this->event_handler_enabled;
    }
      
    /**
     * Sets event_handler_enabled
     *
     * @param   int event_handler_enabled
     * @return  int previous value
     */
    public function setEvent_handler_enabled($event_handler_enabled) {
      return $this->_change('event_handler_enabled', $event_handler_enabled, '%d');
    }
      
    /**
     * Retrieves last_state_change
     *
     * @return  util.Date
     */
    public function getLast_state_change() {
      return $this->last_state_change;
    }
      
    /**
     * Sets last_state_change
     *
     * @param   util.Date last_state_change
     * @return  util.Date previous value
     */
    public function setLast_state_change($last_state_change) {
      return $this->_change('last_state_change', $last_state_change, '%s');
    }
      
    /**
     * Retrieves problem_acknowledged
     *
     * @return  int
     */
    public function getProblem_acknowledged() {
      return $this->problem_acknowledged;
    }
      
    /**
     * Sets problem_acknowledged
     *
     * @param   int problem_acknowledged
     * @return  int previous value
     */
    public function setProblem_acknowledged($problem_acknowledged) {
      return $this->_change('problem_acknowledged', $problem_acknowledged, '%d');
    }
      
    /**
     * Retrieves last_hard_state
     *
     * @return  string
     */
    public function getLast_hard_state() {
      return $this->last_hard_state;
    }
      
    /**
     * Sets last_hard_state
     *
     * @param   string last_hard_state
     * @return  string previous value
     */
    public function setLast_hard_state($last_hard_state) {
      return $this->_change('last_hard_state', $last_hard_state, '%s');
    }
      
    /**
     * Retrieves time_ok
     *
     * @return  int
     */
    public function getTime_ok() {
      return $this->time_ok;
    }
      
    /**
     * Sets time_ok
     *
     * @param   int time_ok
     * @return  int previous value
     */
    public function setTime_ok($time_ok) {
      return $this->_change('time_ok', $time_ok, '%d');
    }
      
    /**
     * Retrieves time_warning
     *
     * @return  int
     */
    public function getTime_warning() {
      return $this->time_warning;
    }
      
    /**
     * Sets time_warning
     *
     * @param   int time_warning
     * @return  int previous value
     */
    public function setTime_warning($time_warning) {
      return $this->_change('time_warning', $time_warning, '%d');
    }
      
    /**
     * Retrieves time_unknown
     *
     * @return  int
     */
    public function getTime_unknown() {
      return $this->time_unknown;
    }
      
    /**
     * Sets time_unknown
     *
     * @param   int time_unknown
     * @return  int previous value
     */
    public function setTime_unknown($time_unknown) {
      return $this->_change('time_unknown', $time_unknown, '%d');
    }
      
    /**
     * Retrieves time_critical
     *
     * @return  int
     */
    public function getTime_critical() {
      return $this->time_critical;
    }
      
    /**
     * Sets time_critical
     *
     * @param   int time_critical
     * @return  int previous value
     */
    public function setTime_critical($time_critical) {
      return $this->_change('time_critical', $time_critical, '%d');
    }
      
    /**
     * Retrieves last_notification
     *
     * @return  util.Date
     */
    public function getLast_notification() {
      return $this->last_notification;
    }
      
    /**
     * Sets last_notification
     *
     * @param   util.Date last_notification
     * @return  util.Date previous value
     */
    public function setLast_notification($last_notification) {
      return $this->_change('last_notification', $last_notification, '%s');
    }
      
    /**
     * Retrieves current_notification
     *
     * @return  int
     */
    public function getCurrent_notification() {
      return $this->current_notification;
    }
      
    /**
     * Sets current_notification
     *
     * @param   int current_notification
     * @return  int previous value
     */
    public function setCurrent_notification($current_notification) {
      return $this->_change('current_notification', $current_notification, '%d');
    }
      
    /**
     * Retrieves notifications_enabled
     *
     * @return  int
     */
    public function getNotifications_enabled() {
      return $this->notifications_enabled;
    }
      
    /**
     * Sets notifications_enabled
     *
     * @param   int notifications_enabled
     * @return  int previous value
     */
    public function setNotifications_enabled($notifications_enabled) {
      return $this->_change('notifications_enabled', $notifications_enabled, '%d');
    }
      
    /**
     * Retrieves latency
     *
     * @return  int
     */
    public function getLatency() {
      return $this->latency;
    }
      
    /**
     * Sets latency
     *
     * @param   int latency
     * @return  int previous value
     */
    public function setLatency($latency) {
      return $this->_change('latency', $latency, '%d');
    }
      
    /**
     * Retrieves execution_time
     *
     * @return  int
     */
    public function getExecution_time() {
      return $this->execution_time;
    }
      
    /**
     * Sets execution_time
     *
     * @param   int execution_time
     * @return  int previous value
     */
    public function setExecution_time($execution_time) {
      return $this->_change('execution_time', $execution_time, '%d');
    }
      
    /**
     * Retrieves plugin_output
     *
     * @return  string
     */
    public function getPlugin_output() {
      return $this->plugin_output;
    }
      
    /**
     * Sets plugin_output
     *
     * @param   string plugin_output
     * @return  string previous value
     */
    public function setPlugin_output($plugin_output) {
      return $this->_change('plugin_output', $plugin_output, '%s');
    }
      
    /**
     * Retrieves flap_detection_enabled
     *
     * @return  int
     */
    public function getFlap_detection_enabled() {
      return $this->flap_detection_enabled;
    }
      
    /**
     * Sets flap_detection_enabled
     *
     * @param   int flap_detection_enabled
     * @return  int previous value
     */
    public function setFlap_detection_enabled($flap_detection_enabled) {
      return $this->_change('flap_detection_enabled', $flap_detection_enabled, '%d');
    }
      
    /**
     * Retrieves is_flapping
     *
     * @return  int
     */
    public function getIs_flapping() {
      return $this->is_flapping;
    }
      
    /**
     * Sets is_flapping
     *
     * @param   int is_flapping
     * @return  int previous value
     */
    public function setIs_flapping($is_flapping) {
      return $this->_change('is_flapping', $is_flapping, '%d');
    }
      
    /**
     * Retrieves percent_state_change
     *
     * @return  string
     */
    public function getPercent_state_change() {
      return $this->percent_state_change;
    }
      
    /**
     * Sets percent_state_change
     *
     * @param   string percent_state_change
     * @return  string previous value
     */
    public function setPercent_state_change($percent_state_change) {
      return $this->_change('percent_state_change', $percent_state_change, '%s');
    }
      
    /**
     * Retrieves scheduled_downtime_depth
     *
     * @return  int
     */
    public function getScheduled_downtime_depth() {
      return $this->scheduled_downtime_depth;
    }
      
    /**
     * Sets scheduled_downtime_depth
     *
     * @param   int scheduled_downtime_depth
     * @return  int previous value
     */
    public function setScheduled_downtime_depth($scheduled_downtime_depth) {
      return $this->_change('scheduled_downtime_depth', $scheduled_downtime_depth, '%d');
    }
      
    /**
     * Retrieves failure_prediction_enabled
     *
     * @return  int
     */
    public function getFailure_prediction_enabled() {
      return $this->failure_prediction_enabled;
    }
      
    /**
     * Sets failure_prediction_enabled
     *
     * @param   int failure_prediction_enabled
     * @return  int previous value
     */
    public function setFailure_prediction_enabled($failure_prediction_enabled) {
      return $this->_change('failure_prediction_enabled', $failure_prediction_enabled, '%d');
    }
      
    /**
     * Retrieves process_performance_data
     *
     * @return  int
     */
    public function getProcess_performance_data() {
      return $this->process_performance_data;
    }
      
    /**
     * Sets process_performance_data
     *
     * @param   int process_performance_data
     * @return  int previous value
     */
    public function setProcess_performance_data($process_performance_data) {
      return $this->_change('process_performance_data', $process_performance_data, '%d');
    }
      
    /**
     * Retrieves obsess_over_service
     *
     * @return  int
     */
    public function getObsess_over_service() {
      return $this->obsess_over_service;
    }
      
    /**
     * Sets obsess_over_service
     *
     * @param   int obsess_over_service
     * @return  int previous value
     */
    public function setObsess_over_service($obsess_over_service) {
      return $this->_change('obsess_over_service', $obsess_over_service, '%d');
    }
      
    /**
     * Update this object in the database
     *
     * @return  boolean success
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    public function update() {
      $cm= ConnectionManager::getInstance();  
      $db= $cm->getByHost('nagios', 0);
      $db->update(
        'nagios.servicestatus set %c where ',
        $this->_updated($db),
        $this->obsess_over_service
      );

      return TRUE;
    }
    
    /**
     * Write this object to the database
     *
     * @return  boolean success
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  lang.IllegalAccessException in case there is no suitable database connection available
     */
    public function insert() {
      $cm= ConnectionManager::getInstance();  
      $db= $cm->getByHost('nagios', 0);
      $db->insert('nagios.servicestatus (%c)', $this->_inserted($db));

      return TRUE;
    }
    
  }
?>
