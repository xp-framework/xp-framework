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
   * Encapsulates a htdig query.
   *
   * @see      http://htdig.org
   * @purpose  Wrap htdig query
   */
  class HtdigSearch extends Object {
    var
      $delimiter= '###\+\+\+###',
      $config=    NULL,    
      $params=    array(), 
      $words=     array(); 

    /**
     * Set Config
     *
     * @access  public
     * @param   &lang.Object config
     */
    function setConfig(&$config) {
      $this->config= &$config;
    }

    /**
     * Get Config
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getConfig() {
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
     * Invoke the search.
     *
     * @access  public
     * @return  &org.htdig.HtdigResultset
     * @throws  IOException in case the invocation of htdig failed
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
