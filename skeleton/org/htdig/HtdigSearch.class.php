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
      $delimiter=   '###\+\+\+###',
      $config=      NULL,    
      $params=      array(), 
      $words=       array(),
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
     * Build the query string for the search.
     *
     * @access  public
     * @return  string query
     */
    function getWordString() {
      $str= '';
      foreach ($words as $w) { 
        if ($w{0} != '-') {
          $str.= ' AND '.$w;
        } else {
          $str.= ' AND NOT '.substr($w, 1);
        }
      }
      
      return substr ($str, 4);
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
      try(); {
        $p= &new Process(sprintf('%s -v %s %s',
          $this->getExecutable(),
          strlen($this->getConfiguration()) ? '-c '.$this->getConfiguration() : '',
          "'".$this->getWords()."'"
        ));

        // Read all errors
        $errs= array();
        while ($l= $p->err->readLine()) { $errs[]= $l; }

        // Read standard output
        $output= array();
        while ($l= $p->out->readLine()) { $output[]= $l; }
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }
      
      $result= &new HtdigResultset();
      $metaresult= array();
      $csvdef= NULL;
      
      try(); {
      
        // Parse metadata result
        while (current($output) && !$csvdef) {
          $meta= explode(':', trim(current($output)));

          if ('CVS' != trim($meta[0])) {
            $metaresult[trim(strtolower($meta[0]))]= trim($meta[1]);
          } else {
            $csvdef= explode($this->delimiter, current($output));
            $result->setCsv($csvdef);
          }

          next($output);
        }

        $result->setMetaresult($metaresult);

        // Now parse resultset
        while (current ($output)) {
          $result->addResult(explode($this->delimiter, current($output)));
        }
      } if (catch ('IllegalArgumentException', $e)) {
        return throw ($e);
      }

      return $result;
    }
  }
?>
