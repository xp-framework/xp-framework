<?php
/* This class is part of the XP framework
 *
 * $Id: SimpyClient.class.php 8975 2006-12-27 18:06:40Z friebe $ 
 */

  namespace com::simpy;

  ::uses(
    'peer.http.BasicAuthorization',
    'peer.http.HttpUtil'
  );

  /**
   * Simpy is a social bookmark manager
   *
   * Example:
   * <code>
   *   $s= &new SimpyClient('username', 'password');
   *   try(); {
   *     $links= &$s->getLinks();
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit;
   *   }
   *   var_dump($links);
   * </code>
   *
   * @purpose Provide an API to Simpy
   * @see http://www.simpy.com/simpy/service/api/rest/
   */
  class SimpyClient extends lang::Object {
    public
      $username= '',
      $password= '';

    /**
     * Constructor
     *
     * @param   string username from simpy
     * @param   string password from simpy
     */
    public function __construct(
      $username,
      $password
    ) {
      $this->username= $username;
      $this->password= $password;
    }

    /**
     * Do Simpy-Request
     *
     * @param   string url of simpy-request
     * @param   array param of simply-request
     * @return  string
     */
    protected function _doRequest($url, $param= array()) {
      try {
        $buf= peer::http::HttpUtil::get(
          new ($url),
          $param,
          array(new peer::http::BasicAuthorization($this->username, $this->password))
        );
      } catch ( $e) {
        throw($e);
        exit(-1);
      }
      return $buf;    
    }

    /**
     * Get list of users most popular tags and counts
     *
     * @return  string
     */  
    public function getTags() {
      return $this->_doRequest(
        'http://www.simpy.com/simpy/api/rest/GetTags.do'
      );
    }

    /**
     * Get list of users links and counts
     *
     * @param   string q query-string for notes
     * @param   &util.Date date date only show links on this date
     * @param   &util.Date afterDate show links after this date
     * @param   &util.Date beforeDate show links before this date
     * @return  string
     */  
    public function getLinks($q= '', $date, $afterDate, $beforeDate) {
      return $this->_doRequest(
        'http://www.simpy.com/simpy/api/rest/GetLinks.do', 
        array(
          'q'         => $q,
          'date'      => is_object($date) ? $date->format('%Y-%m-%d') : '',
          'afterDate' => is_object($afterDate) ? $afterDate->format('%Y-%m-%d') : '',
          'beforeDate'=> is_object($beforeDate) ? $beforeDate->format('%Y-%m-%d') : ''
        ));
    }

    /**
     * Get list of users topics, meta-data, number of new links since last login
     *
     * @return  string
     */  
    public function getTopics() {
      return $this->_doRequest(
        'http://www.simpy.com/simpy/api/rest/GetTopics.do'
      );
    }

    /**
     * Get list of users notes in reverse chronological order by add date or by rank
     *
     * @param   string q query-string for notes
     * @return  string
     */  
    public function getNotes($q= '') {
      return $this->_doRequest(
        'http://www.simpy.com/simpy/api/rest/GetNotes.do', 
        array('q' => $q)
      );
    }
  
  }
?>
