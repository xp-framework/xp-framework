<?php
/* This class is part of the XP framework
 *
 * $Id: HtdigSearch.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace org::htdig;

  ::uses(
    'org.htdig.HtdigResultset',
    'org.htdig.HtdigEntry',
    'lang.Process'
  );

  // Defines for sorting methods
  define('SORT_SCORE',         'score');
  define('SORT_TIME',          'time');
  define('SORT_TITLE',         'title');
  define('SORT_REVSCORE',      'revscore');
  define('SORT_REVTIME',       'revtime');
  define('SORT_REVTITLE',      'revtitle');

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
   *   } if (catch('IOException', $e)) {
   *     $e->printStackTrace();
   *     exit(1);
   *   } if (catch('IllegalArgumentException', $e)) {
   *     $e->printStackTrace();
   *     exit(1);
   *   }
   *
   *   Console::writeLine($result->toString());
   * </code>
   *
   * The aforementioned requirements for the ht://dig setup consist of rules for
   * the output of htdig:
   * The header file containing the csv-definition looks like:
   * <pre>
   *   LOGICAL_WORDS:$(LOGICAL_WORDS)
   *   MATCHES:$(MATCHES)
   *   PAGES:$(PAGES)
   *   CSV:CURRENT|DOCID|NSTARS|SCORE|URL|TITLE|EXCERPT|METADESCRIPTION|MODIFIED|SIZE|HOPCOUNT|PERCENT          
   * </pre>
   *
   * The used template must use the following template-syntax (one line):
   * <pre>
   *   $(CURRENT)|$(DOCID)|$(NSTARS)|$(SCORE)|$%(URL)|$%(TITLE)|$%(EXCERPT)|
   *   $%(METADESCRIPTION)|$(MODIFIED)|$(SIZE)|$(HOPCOUNT)|$(PERCENT)
   * </pre>
   *
   * Note also that you can set query parameters which may or may not be overwriteable
   * by a client - this depends on the actual ht://dig - configuration.
   *
   * @see      http://htdig.org
   * @purpose  Wrap htdig query
   */
  class HtdigSearch extends lang::Object {
    public
      $config=      NULL,    
      $params=      array(), 
      $words=       array(),
      $excludes=    array(),
      $algorithms=  '',
      $sort=        SORT_SCORE,
      $method=      'boolean',
      $maxresults=  0;
    
    public
      $delimiter=   '|',
      $executable=  '';

    /**
     * Set Config
     *
     * @param   string config
     */
    public function setConfig($config) {
      $this->config= $config;
    }

    /**
     * Get Config
     *
     * @return  string
     */
    public function getConfig() {
      return $this->config;
    }

    /**
     * Set Params
     *
     * @param   mixed[] params
     */
    public function setParams($params) {
      $this->params= $params;
    }

    /**
     * Get Params
     *
     * @return  mixed[]
     */
    public function getParams() {
      return $this->params;
    }
    
    /**
     * Set a single param
     *
     * @param   string name
     * @param   string value
     */
    public function setParam($name, $value) {
      $this->params[$name]= $value;
    }
    
    /**
     * Get a single param
     *
     * @param   string name
     * @return  string value or NULL if isset
     */
    public function getParam($name) {
      return (isset($this->params[$name])
        ? $this->params[$name]
        : NULL
      );
    }    

    /**
     * Set Words
     *
     * @param   mixed[] words
     */
    public function setWords($words) {
      $this->words= $words;
    }

    /**
     * Get Words
     *
     * @return  mixed[]
     */
    public function getWords() {
      return $this->words;
    }

    /**
     * Set Excludes
     *
     * @param   mixed[] excludes
     */
    public function setExcludes($excludes) {
      $this->excludes= $excludes;
    }

    /**
     * Get Excludes
     *
     * @return  mixed[]
     */
    public function getExcludes() {
      return $this->excludes;
    }

    /**
     * Set Algorithm
     *
     * @param   string algorithm
     */
    public function setAlgorithms($algorithm) {
      $this->algorithms= $algorithm;
    }

    /**
     * Get Algorithm
     *
     * @return  string
     */
    public function getAlgorithms() {
      return $this->algorithms;
    }

    /**
     * Set Sort
     *
     * @param   mixed sort
     */
    public function setSort($sort) {
      $this->sort= $sort;
    }

    /**
     * Get Sort
     *
     * @return  mixed
     */
    public function getSort() {
      return $this->sort;
    }

    /**
     * Set Maxresults
     *
     * @param   int maxresults
     */
    public function setMaxresults($maxresults) {
      $this->maxresults= $maxresults;
    }

    /**
     * Get Maxresults
     *
     * @return  int
     */
    public function getMaxresults() {
      return $this->maxresults;
    }

    /**
     * Set Method
     *
     * @param   mixed method
     */
    public function setMethod($method) {
      $this->method= $method;
    }

    /**
     * Get Method
     *
     * @return  mixed
     */
    public function getMethod() {
      return $this->method;
    }

    /**
     * Build the query string for the search.
     *
     * @return  string query
     */
    protected function _getWordString() {
      $str= '';
      foreach ($this->getWords() as $w) { 
        if ($w{0} != '-') {
          $str.= ' AND '.$w;
        } else {
          $str.= ' NOT '.substr($w, 1);
        }
      }
      
      return substr ($str, 5);
    }
    
    /**
     * Build query string.
     *
     * @return  string query
     */
    protected function _getQuery() {
      $params= $this->getParams();

      // If excludes are given, add them to the query
      if (strlen ($this->getExcludes()))
        $params['exclude']= implode('|', $this->getExcludes());
      
      // Only overwrite algorithms, when they are set  
      if (strlen ($this->getAlgorithms()))
        $params['search_algorithm']= $this->getAlgorithms();

      // Set the search method (regular / boolean)
      $params['method']= $this->getMethod();

      // If maxresults is 0, use 1000 as matchesperpage. We'll only receive
      // the first page, so this replaces 'all results' which htdig does not support.
      $params['matchesperpage']= (empty($this->maxresults) 
        ? 1000 : 
        $this->maxresults
      );

      $params['sort']= $this->getSort();
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
     * @param   string executable
     */
    public function setExecutable($executable) {
      $this->executable= $executable;
    }

    /**
     * Get Executable
     *
     * @return  string
     */
    public function getExecutable() {
      return $this->executable;
    }

    /**
     * Invoke the search.
     *
     * @return  &org.htdig.HtdigResultset
     * @throws  io.IOException in case the invocation of htdig failed
     * @throws  lang.IllegalArgumentException in case search entry was invalid
     */
    public function invoke() {
      $cat= util::log::Logger::getInstance()->getCategory();

      try {
        $cmdline= sprintf('%s -v %s %s',
          $this->getExecutable(),
          strlen($this->getConfig()) ? '-c '.$this->getConfig() : '',
          "'".$this->_getQuery()."' 2>&1"
        );
        $p= new lang::Process($cmdline);

        // Read standard output
        $output= array();
        while (!$p->out->eof()) { $output[]= $p->out->readLine(); }
      } catch (io::IOException $e) {
        throw ($e);
      }
      
      $result= new HtdigResultset();
      $metaresult= array();
      $hasCsv= FALSE;

      try {
      
        // Parse metadata result (search result header)
        while (FALSE !== current($output) && !$hasCsv) {
          $meta= explode(':', trim(current($output)));

          // Check for header-data; the last line of header is the CSV definition
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
        $cnt= 0;
        while (FALSE !== current ($output)) {
        
          // Don't exceed maxresults
          if ($this->maxresults && $cnt > $this->maxresults)
            break;
            
          // Do not take empty lines into account
          if (current($output)) {
            $result->addResult(explode($this->delimiter, current($output)));
            $cnt++;
          }
          next ($output);
        }
      } catch (lang::IllegalArgumentException $e) {
        throw ($e);
      }

      return $result;
    }
  }
?>
