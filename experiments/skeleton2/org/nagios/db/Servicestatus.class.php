<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.ConnectionManager');

  /**
   * Class wrapper for table servicestatus, database nagios
   * (Auto-generated on Tue, 25 Nov 2003 12:54:46 +0100 by alex)
   *
   * @purpose  Datasource accessor
   */
  class Servicestatus extends Object {
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
     * Constructor
     *
     * @access  public
     * @param   array record default array()
     */
    public function __construct($record= array()) {
      foreach ($record as $key => $val) {
        $this->{$key}= $val;
      }
    }

    /**
     * Gets an instance of this object by hostname
     *
     * @access  static
     * @param   string hostname
     * @return  &Servicestatus[] object
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  rdbms.ConnectionNotRegisteredException in case there is no suitable database connection available
     */
    public static function getByHost_name($host_name) {
      try {
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
      } catch (SQLException $e) {
        throw ($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by hostname and service
     *
     * @access  static
     * @param   string hostname
     * @param   string servicedescription
     * @return  &Servicestatus[] object
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  rdbms.ConnectionNotRegisteredException in case there is no suitable database connection available
     */
    public static function getByHostService($host_name, $service) {
      try {
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
      } catch (SQLException $e) {
        throw ($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object by service status
     *
     * @access  static
     * @param   string status
     * @return  &Servicestatus[] object
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  rdbms.ConnectionNotRegisteredException in case there is no suitable database connection available
     */
    public static function getByService_status($service_status) {
      try {
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
      } catch (SQLException $e) {
        throw ($e);
      }

      return $data;
    }

    /**
     * Gets an instance of this object with service status of not OK
     *
     * @access  static
     * @return  &Servicestatus[] object
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  rdbms.ConnectionNotRegisteredException in case there is no suitable database connection available
     */
    public static function getByNotOk() {
      try {
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
     * Retrieves service_description
     *
     * @access  public
     * @return  string
     */
    public function getService_description() {
      return $this->service_description;
    }
      
    /**
     * Sets service_description
     *
     * @access  public
     * @param   string service_description
     */
    public function setService_description($service_description) {
      $this->service_description= $service_description;
    }
      
    /**
     * Retrieves service_status
     *
     * @access  public
     * @return  string
     */
    public function getService_status() {
      return $this->service_status;
    }
      
    /**
     * Sets service_status
     *
     * @access  public
     * @param   string service_status
     */
    public function setService_status($service_status) {
      $this->service_status= $service_status;
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
     * Retrieves current_attempt
     *
     * @access  public
     * @return  int
     */
    public function getCurrent_attempt() {
      return $this->current_attempt;
    }
      
    /**
     * Sets current_attempt
     *
     * @access  public
     * @param   int current_attempt
     */
    public function setCurrent_attempt($current_attempt) {
      $this->current_attempt= $current_attempt;
    }
      
    /**
     * Retrieves max_attempts
     *
     * @access  public
     * @return  int
     */
    public function getMax_attempts() {
      return $this->max_attempts;
    }
      
    /**
     * Sets max_attempts
     *
     * @access  public
     * @param   int max_attempts
     */
    public function setMax_attempts($max_attempts) {
      $this->max_attempts= $max_attempts;
    }
      
    /**
     * Retrieves state_type
     *
     * @access  public
     * @return  string
     */
    public function getState_type() {
      return $this->state_type;
    }
      
    /**
     * Sets state_type
     *
     * @access  public
     * @param   string state_type
     */
    public function setState_type($state_type) {
      $this->state_type= $state_type;
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
     * Retrieves next_check
     *
     * @access  public
     * @return  util.Date
     */
    public function getNext_check() {
      return $this->next_check;
    }
      
    /**
     * Sets next_check
     *
     * @access  public
     * @param   util.Date next_check
     */
    public function setNext_check(Date $next_check) {
      $this->next_check= $next_check;
    }
      
    /**
     * Retrieves should_be_scheduled
     *
     * @access  public
     * @return  int
     */
    public function getShould_be_scheduled() {
      return $this->should_be_scheduled;
    }
      
    /**
     * Sets should_be_scheduled
     *
     * @access  public
     * @param   int should_be_scheduled
     */
    public function setShould_be_scheduled($should_be_scheduled) {
      $this->should_be_scheduled= $should_be_scheduled;
    }
      
    /**
     * Retrieves check_type
     *
     * @access  public
     * @return  string
     */
    public function getCheck_type() {
      return $this->check_type;
    }
      
    /**
     * Sets check_type
     *
     * @access  public
     * @param   string check_type
     */
    public function setCheck_type($check_type) {
      $this->check_type= $check_type;
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
     * Retrieves accept_passive_checks
     *
     * @access  public
     * @return  int
     */
    public function getAccept_passive_checks() {
      return $this->accept_passive_checks;
    }
      
    /**
     * Sets accept_passive_checks
     *
     * @access  public
     * @param   int accept_passive_checks
     */
    public function setAccept_passive_checks($accept_passive_checks) {
      $this->accept_passive_checks= $accept_passive_checks;
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
     * Retrieves last_hard_state
     *
     * @access  public
     * @return  string
     */
    public function getLast_hard_state() {
      return $this->last_hard_state;
    }
      
    /**
     * Sets last_hard_state
     *
     * @access  public
     * @param   string last_hard_state
     */
    public function setLast_hard_state($last_hard_state) {
      $this->last_hard_state= $last_hard_state;
    }
      
    /**
     * Retrieves time_ok
     *
     * @access  public
     * @return  int
     */
    public function getTime_ok() {
      return $this->time_ok;
    }
      
    /**
     * Sets time_ok
     *
     * @access  public
     * @param   int time_ok
     */
    public function setTime_ok($time_ok) {
      $this->time_ok= $time_ok;
    }
      
    /**
     * Retrieves time_warning
     *
     * @access  public
     * @return  int
     */
    public function getTime_warning() {
      return $this->time_warning;
    }
      
    /**
     * Sets time_warning
     *
     * @access  public
     * @param   int time_warning
     */
    public function setTime_warning($time_warning) {
      $this->time_warning= $time_warning;
    }
      
    /**
     * Retrieves time_unknown
     *
     * @access  public
     * @return  int
     */
    public function getTime_unknown() {
      return $this->time_unknown;
    }
      
    /**
     * Sets time_unknown
     *
     * @access  public
     * @param   int time_unknown
     */
    public function setTime_unknown($time_unknown) {
      $this->time_unknown= $time_unknown;
    }
      
    /**
     * Retrieves time_critical
     *
     * @access  public
     * @return  int
     */
    public function getTime_critical() {
      return $this->time_critical;
    }
      
    /**
     * Sets time_critical
     *
     * @access  public
     * @param   int time_critical
     */
    public function setTime_critical($time_critical) {
      $this->time_critical= $time_critical;
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
     * Retrieves latency
     *
     * @access  public
     * @return  int
     */
    public function getLatency() {
      return $this->latency;
    }
      
    /**
     * Sets latency
     *
     * @access  public
     * @param   int latency
     */
    public function setLatency($latency) {
      $this->latency= $latency;
    }
      
    /**
     * Retrieves execution_time
     *
     * @access  public
     * @return  int
     */
    public function getExecution_time() {
      return $this->execution_time;
    }
      
    /**
     * Sets execution_time
     *
     * @access  public
     * @param   int execution_time
     */
    public function setExecution_time($execution_time) {
      $this->execution_time= $execution_time;
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
     * Retrieves obsess_over_service
     *
     * @access  public
     * @return  int
     */
    public function getObsess_over_service() {
      return $this->obsess_over_service;
    }
      
    /**
     * Sets obsess_over_service
     *
     * @access  public
     * @param   int obsess_over_service
     */
    public function setObsess_over_service($obsess_over_service) {
      $this->obsess_over_service= $obsess_over_service;
    }
      
    /**
     * Update this object in the database
     *
     * @access  public
     * @return  boolean success
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  rdbms.ConnectionNotRegisteredException in case there is no suitable database connection available
     */
    public function update() {
      try {
        ConnectionManager::getInstance()->getByHost('nagios', 0)->update('
          nagios.servicestatus set
            service_description = %s,
            service_status = %s,
            last_update = %s,
            current_attempt = %d,
            max_attempts = %d,
            state_type = %s,
            last_check = %s,
            next_check = %s,
            should_be_scheduled = %d,
            check_type = %s,
            checks_enabled = %d,
            accept_passive_checks = %d,
            event_handler_enabled = %d,
            last_state_change = %s,
            problem_acknowledged = %d,
            last_hard_state = %s,
            time_ok = %d,
            time_warning = %d,
            time_unknown = %d,
            time_critical = %d,
            last_notification = %s,
            current_notification = %d,
            notifications_enabled = %d,
            latency = %d,
            execution_time = %d,
            plugin_output = %s,
            flap_detection_enabled = %d,
            is_flapping = %d,
            percent_state_change = %s,
            scheduled_downtime_depth = %d,
            failure_prediction_enabled = %d,
            process_performance_data = %d,
            obsess_over_service = %d
          where
            host_name = %s
          ',
          $this->service_description,
          $this->service_status,
          $this->last_update,
          $this->current_attempt,
          $this->max_attempts,
          $this->state_type,
          $this->last_check,
          $this->next_check,
          $this->should_be_scheduled,
          $this->check_type,
          $this->checks_enabled,
          $this->accept_passive_checks,
          $this->event_handler_enabled,
          $this->last_state_change,
          $this->problem_acknowledged,
          $this->last_hard_state,
          $this->time_ok,
          $this->time_warning,
          $this->time_unknown,
          $this->time_critical,
          $this->last_notification,
          $this->current_notification,
          $this->notifications_enabled,
          $this->latency,
          $this->execution_time,
          $this->plugin_output,
          $this->flap_detection_enabled,
          $this->is_flapping,
          $this->percent_state_change,
          $this->scheduled_downtime_depth,
          $this->failure_prediction_enabled,
          $this->process_performance_data,
          $this->obsess_over_service,
          $this->host_name
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
     * @throws  rdbms.ConnectionNotRegisteredException in case there is no suitable database connection available
     */
    public function insert() {
      try {
        ConnectionManager::getInstance()->getByHost('nagios', 0)->insert('
          nagios.servicestatus (
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
          ) values (
            %s, %s, %s, %s, %d, %d, %s, %s, %s, %d, %s, %d, %d, %d, %s, %d, %s, %d, %d, %d, %d, %s, %d, %d, %d, %d, %s, %d, %d, %s, %d, %d, %d, %d
          )',
          $this->host_name,
          $this->service_description,
          $this->service_status,
          $this->last_update,
          $this->current_attempt,
          $this->max_attempts,
          $this->state_type,
          $this->last_check,
          $this->next_check,
          $this->should_be_scheduled,
          $this->check_type,
          $this->checks_enabled,
          $this->accept_passive_checks,
          $this->event_handler_enabled,
          $this->last_state_change,
          $this->problem_acknowledged,
          $this->last_hard_state,
          $this->time_ok,
          $this->time_warning,
          $this->time_unknown,
          $this->time_critical,
          $this->last_notification,
          $this->current_notification,
          $this->notifications_enabled,
          $this->latency,
          $this->execution_time,
          $this->plugin_output,
          $this->flap_detection_enabled,
          $this->is_flapping,
          $this->percent_state_change,
          $this->scheduled_downtime_depth,
          $this->failure_prediction_enabled,
          $this->process_performance_data,
          $this->obsess_over_service
        );

      } catch (SQLException $e) {
        throw ($e);
      }

      return TRUE;
    }
    
  }
?>
