<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.htdig.HtdigResultset',
    'org.htdig.HtdigEntry',
    'lang.Process'
  );

  /**
   * Encapsulates a htdig query. This class needs a working 
   * htdig installation on the executing host.
   *
   * Special requirements need to be posed upon the configuration
   * which are supposed to work with this class: it must use a 
   * configuration that does not create HTML output but instead
   * produces some sort of CSV output. The delimiter must match
   * the one set in this class.
   *
   * <code>
   *   try(); {
   *     $search= &new HtdigSearch();
   *     $search->setConfig('/path/to/htdig-configuration');
   *     $search->setExecutable('/usr/local/bin/htdig');
   *     $search->setWords(array('foo', '-bar'));
   *     $resultset= &$search->invoke();
   *   } if (catch ('IOException', $e)) {
   *     $e->printStackTrace();
   *     exit(1);
   *   } if (catch ('IllegalArgumentException', $e)) {
   *     $e->printStackTrace();
   *     exit(1);
   *   }
   *
   *   Console::writeLine('Search metadata: ', $resultset->getMetaresult());
   *   foreach ($resultset->getResults() as $entry) {
   *     Console::writeLine($entry->toString());
   *   }
   *
   * @see      http://htdig.org
   * @purpose  Wrap htdig query
   */
  class HtdigSearch extends Object {
    var
      $config=      NULL,    
      $params=      array(), 
      $words=       array(),
      $excludes=    array();
    
    var
      $delimiter=   '###+++###',
      $executable=  '';

    /**
     * Set Config
     *
     * @access  public
     * @param   string config
     */
    function setConfig($config) {
      $this->config= $config;
    }

    /**
     * Get Config
     *
     * @access  public
     * @return  string
     */
    function getConfig() {
      return $this->config;
    }

    /**
     * Set Params
     *
     * @access  public
     * @param   mixed[] params
     */
    function setParams($params) {
      $this->params= $params;
    }

    /**
     * Get Params
     *
     * @access  public
     * @return  mixed[]
     */
    function getParams() {
      return $this->params;
    }
    
    /**
     * Set a single param
     *
     * @access  public
     * @param   string name
     * @param   string value
     */
    function setParam($name, $value) {
      $this->params[$name]= $value;
    }
    
    /**
     * Get a single param
     *
     * @access  public
     * @param   string name
     * @return  string value or NULL if isset
     */
    function getParam($name) {
      return (isset($this->params[$name])
        ? $this->params[$name]
        : NULL
      );
    }    

    /**
     * Set Words
     *
     * @access  public
     * @param   mixed[] words
     */
    function setWords($words) {
      $this->words= $words;
    }

    /**
     * Get Words
     *
     * @access  public
     * @return  mixed[]
     */
    function getWords() {
      return $this->words;
    }

    /**
     * Set Excludes
     *
     * @access  public
     * @param   mixed[] excludes
     */
    function setExcludes($excludes) {
      $this->excludes= $excludes;
    }

    /**
     * Get Excludes
     *
     * @access  public
     * @return  mixed[]
     */
    function getExcludes() {
      return $this->excludes;
    }

    /**
     * Build the query string for the search.
     *
     * @access  protected
     * @return  string query
     */
    function _getWordString() {
      $str= '';
      foreach ($this->getWords() as $w) { 
        if ($w{0} != '-') {
          $str.= ' AND '.$w;
        } else {
          $str.= ' AND NOT '.substr($w, 1);
        }
      }
      
      return substr ($str, 5);
    }
    
    /**
     * Build query string.
     *
     * @access  protected
     * @return  string query
     */
    function _getQuery() {
      $params= $this->getParams();
      $params['exclude']= $this->getExcludes();
      $params['words']= $this->_getWordString();
      
      $str= '';
      foreach (array_keys($params) as $key) {
        if (strlen($str)) $str.= '&';
        $str.= $key.'='.urlencode($params[$key]);
      }
      
      return $str;
    }

    /**
     * Set Executable
     *
     * @access  public
     * @param   string executable
     */
    function setExecutable($executable) {
      $this->executable= $executable;
    }

    /**
     * Get Executable
     *
     * @access  public
     * @return  string
     */
    function getExecutable() {
      return $this->executable;
    }

    /**
     * Invoke the search.
     *
     * @access  public
     * @return  &org.htdig.HtdigResultset
     * @throws  lang.IOException in case the invocation of htdig failed
     * @throws  lang.IllegalArgumentException in case search entry was invalid
     */
    function &invoke() {
      $log= &Logger::getInstance();
      $cat= &$log->getCategory();

      try(); {
        $cmdline= sprintf('%s -v %s %s',
          $this->getExecutable(),
          strlen($this->getConfig()) ? '-c '.$this->getConfig() : '',
          "'".$this->_getQuery()."' 2>&1"
        );
        $p= &new Process($cmdline);

        // Read standard output
        $output= array();
        while (!$p->out->eof()) { $output[]= $p->out->readLine(); }
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }
      
      $result= &new HtdigResultset();
      $metaresult= array();
      $hasCsv= FALSE;

      try(); {
      
        // Parse metadata result
        while (FALSE !== current($output) && !$hasCsv) {
          $meta= explode(':', trim(current($output)));

          if ('CSV' != trim($meta[0])) {
            $metaresult[trim(strtolower($meta[0]))]= trim($meta[1]);
          } else {
            $result->setCsvdef(explode($this->delimiter, substr(current($output), 4)));
            $hasCsv= TRUE;
          }

          next($output);
        }

        $result->setMetaresult($metaresult);

        // Now parse resultset
        while (FALSE !== current ($output)) {
        
          // Do not take empty lines into account
          if (current($output)) {
            $result->addResult(explode($this->delimiter, current($output)));
          }
          next ($output);
        }
      } if (catch ('IllegalArgumentException', $e)) {
        return throw ($e);
      }

      return $result;
    }
  }
?>
