<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.Socket',
    'org.dict.DictDefinitionEntry',
    'util.log.Traceable'
  );

  define('DICT_STRATEGY_SUBSTRING', 'substring');
  define('DICT_STRATEGY_EXACT',     'exact');
  define('DICT_STRATEGY_PREFIX',    'prefix');
  define('DICT_STRATEGY_SUFFIX',    'suffix');
  define('DICT_STRATEGY_RE',        're');
  define('DICT_STRATEGY_REGEXP',    'regexp');
  define('DICT_STRATEGY_SOUNDEX',   'soundex');
  define('DICT_STRATEGY_LEV',       'lev');
  
  /**
   * The Dictionary Server Protocol (DICT) is a TCP transaction based 
   * query/response protocol that allows a client to access dictionary 
   * definitions from a set of natural language dictionary databases. 
   *
   * <code>
   *   $dc= new DictClient();
   *
   *   $dc->connect('dict.org', 2628);
   *   $definition= $dc->getDefinition('Dictionary', '*');
   *   $dc->close();
   *
   *   var_dump($definition);
   * </code>
   *
   * @see      http://www.dict.org/links.html
   * @see      http://luetzschena-stahmeln.de/dictd/index.php
   * @see      http://www.freedict.de/
   * @see      rfc://2229
   * @test     xp://net.xp_framework.unittest.peer.DictTest
   * @purpose  Implement DICT
   */
  class DictClient extends Object implements Traceable {
    public
      $info  = '',
      $cat   = NULL;
      
    public
      $_sock = NULL;
      
    /**
     * Constructor
     *
     * @param   string info default ''
     */
    public function __construct($info= '') {
      $this->info= $info;
    }

    /**
     * Private helper method
     *
     * @param   string fmt or FALSE to indicate not to write any data
     * @param   string* args arguments for sprintf-string fmt
     * @param   mixed expect int for one possible returncode, int[] for multiple or FALSE to indicate not to read any data
     * @return  string buf
     * @throws  io.IOException
     * @throws  lang.IllegalStateException in case of not being connected
     */
    protected function _sockcmd() {
      if (NULL === $this->_sock) {
        throw(new IllegalStateException('Not connected'));
      }
      
      // Arguments
      $args= func_get_args();
      $expect= (array)$args[sizeof($args)- 1];
      
      try {
        if (FALSE !== $args[0]) {
          $cmd= vsprintf($args[0], array_slice($args, 1, -1));
          $this->_sock->write($cmd."\n");
          if (FALSE === $expect[0]) return '';
        }

        // Read
        $buf= substr($this->_sock->read(), 0, -2);
      } catch (IOException $e) {
        throw($e);
      }
      
      // Got expected data?
      $code= substr($buf, 0, 3);
      if (!in_array($code, $expect)) {
        throw(new FormatException(
          'Expected '.implode(' or ', $expect).', have '.$code.' ["'.$buf.'"] '.
          '- command was '.$cmd
        ));
      }
      
      return $buf;
    }
    
    /**
     * Connect to a DICT server
     *
     * Example of C/S communication:
     * <pre>
     * <<< 220 miranda.org dictd 1.7.0/rf on Linux 2.2.19 <auth.mime> <1110507.2399.1042576
     * </pre>
     *
     * <pre>
     * >>> CLIENT netcat
     * <<< 250 ok
     * </pre>
     *
     * @param   string server
     * @param   int port default 2628
     * @return  bool success
     * @throws  io.IOException
     */
    public function connect($server, $port= 2628) {
      $this->_sock= new Socket($server, $port);
      try {
        $this->_sock->connect();
        $this->_sockcmd(FALSE, 220); 
      } catch (Exception $e) {
        throw($e);
      }
      
      return TRUE;
    }

    /**
     * Disconnect
     *
     * Example of C/S communication:
     * <pre>
     * >>> QUIT
     * <<< 221 bye [d/m/c = 0/0/0; 87.000r 0.000u 0.000s]
     * </pre>
     *
     * @return  bool success
     * @throws  io.IOException
     */
    public function close() {
      if (NULL === $this->_sock) return;
      try {
        $this->_sockcmd('QUIT', 221); 
        $this->_sock->close();
      } catch (Exception $e) {
        throw($e);
      }
      
      $this->_sock= NULL;
      return TRUE;      
    }
  
    /**
     * Get a definition fo a word
     *
     * Example of C/S communication:
     * <pre>
     * >>> DEFINE * 'database'
     * <<< 150 2 definitions retrieved
     * <<< 151 "database" wn "WordNet (r) 1.7"
     * <<< database
     * <<<      n : an organized body of related information
     * <<< .
     * <<< 151 "database" foldoc "The Free On-line Dictionary of Computing (09 FEB 02)"
     * <<< database
     * <<< 
     * <<<    1. <database> One or more large structured sets of persistent
     * <<< [...]
     * <<< .
     * <<< 250 ok [d/m/c = 2/0/112; 0.000r 0.000u 0.000s]
     * </pre>
     *
     * <pre>
     * >>> define * 'Deutsch' 
     * <<< 552 no match [d/m/c = 0/0/107; 0.000r 0.000u 0.000s]
     * </pre>
     *
     * @param   string word
     * @param   strind db default '*'
     * @return  org.dict.DictDefinitionEntry[]
     * @throws  io.IOException
     */
    public function getDefinition($word, $db= '*') {
      $def= array();
      try {
        $ret= $this->_sockcmd('DEFINE %s \'%s\'', $db, $word, array(150, 552));
        if ('150' == substr($ret, 0, 3)) {

          while (
            ($ret= $this->_sockcmd(FALSE, array(151, 250))) &&
            ('250' != substr($ret, 0, 3))
          ) {
          
            // Read until we find with a "." on a line with itself
            $definition= '';
            while ($buf= $this->_sock->read()) {
              if ('.' == $buf{0}) break;
              $definition.= $buf;
            }
            
            $def[]= new DictDefinitionEntry(substr($ret, 4), $definition);
          }
        }
      } catch (Exception $e) {
        throw($e);
      }
      
      return $def;    
    }
    
    /**
     * Get words
     *
     * Example of C/S communication:
     * <pre>
     * >>> MATCH * substring database
     * <<< 152 81 matches found
     * <<< elements "00-database-info"
     * <<< [...]
     * <<< vera "00-database-url"
     * <<< .
     * <<< 250 ok [d/m/c = 0/81/955839; 0.000r 0.000u 0.000s]
     * </pre>
     *
     * @param   string word
     * @param   string strategy default DICT_STRATEGY_SUBSTRING one of the DICT_STRATEGY_* constants
     * @param   strind db default '*'
     * @return  string definition
     * @throws  io.IOException
     */
    public function getWords($word, $strategy= DICT_STRATEGY_SUBSTRING, $db= '*') {
      // TBD
    }
    
    /**
     * Get available strategies
     *
     * Example of C/S communication:
     * <pre>
     * >>> SHOW STRAT
     * >>> 111 8 databases present
     * <<< exact "Match words exactly"
     * <<< prefix "Match prefixes"
     * <<< substring "Match substring occurring anywhere in word"
     * <<< suffix "Match suffixes"
     * <<< re "POSIX 1003.2 (modern) regular expressions"
     * <<< regexp "Old (basic) regular expressions"
     * <<< soundex "Match using SOUNDEX algorithm"
     * <<< lev "Match words within Levenshtein distance one"
     * <<< .
     * <<< 250 ok
     * </pre>
     *
     * @return  mixed
     * @throws  io.IOException
     */
    public function getStrategies() {
      // TBD    
    }
    
    /**
     * Get available databases
     *
     * Example of C/S communication:
     * <pre>
     * >>> SHOW DB
     * <<< 110 11 databases present
     * <<< elements "Elements database 20001107"
     * <<< web1913 "Webster's Revised Unabridged Dictionary (1913)"
     * <<< wn "WordNet (r) 1.7"
     * <<< gazetteer "U.S. Gazetteer (1990)"
     * <<< jargon "Jargon File (4.3.0, 30 APR 2001)"
     * <<< foldoc "The Free On-line Dictionary of Computing (09 FEB 02)"
     * <<< easton "Easton's 1897 Bible Dictionary"
     * <<< hitchcock "Hitchcock's Bible Names Dictionary (late 1800's)"
     * <<< devils "THE DEVIL'S DICTIONARY ((C)1911 Released April 15 1993)"
     * <<< world95 "The CIA World Factbook (1995)"
     * <<< vera "V.E.R.A. -- Virtual Entity of Relevant Acronyms December 2001"
     * <<< .
     * <<< 250 ok
     * </pre>
     *
     * @return  mixed
     * @throws  io.IOException
     */
    public function getDatabases() {
      // TBD    
    }
    
    /**
     * Retrieve information
     *
     * Example of C/S communication:
     * <pre>
     * >>> SHOW INFO devils
     * <<< 112 information for devils
     * <<< 00-database-info
     * <<< This file was converted from the original database on:
     * <<<           Sun Sep 10 15:32:59 2000
     * <<< [...]
     * <<< .
     * <<< 250 ok
     * </pre>
     *
     * @return  mixed
     * @throws  io.IOException
     */
    public function getDatabaseInfo() {
      // TBD    
    }
    
    /**
     * Get server statistics
     *
     * Example of C/S communication:
     * <pre>
     * >>> SHOW SERVER
     * <<< 114 server information
     * <<< dictd 1.7.0/rf on Linux 2.2.19
     * <<< On miranda.org: up 35+19:00:44, 1110507 forks (1292.8/hour)
     * <<< 
     * <<< Database      Headwords         Index          Data  Uncompressed
     * <<< elements            130          2 kB         14 kB         45 kB
     * <<< web1913          185399       3438 kB         11 MB         30 MB
     * <<< wn               136975       2763 kB       8173 kB         25 MB
     * <<< gazetteer         52994       1087 kB       1754 kB       8351 kB
     * <<< jargon             2373         42 kB        619 kB       1427 kB
     * <<< foldoc            13533        262 kB       2016 kB       4947 kB
     * <<< easton             3968         64 kB       1077 kB       2648 kB
     * <<< hitchcock          2619         34 kB         33 kB         85 kB
     * <<< devils              997         15 kB        161 kB        377 kB
     * <<< world95             277          5 kB        936 kB       2796 kB
     * <<< vera               8930        101 kB        154 kB        537 kB
     * <<< .
     * <<< 250 ok
     * </pre>
     *
     * @return  mixed
     * @throws  io.IOException
     */
    public function getServer() {
      // TBD    
    }
    
    /**
     * Retrieve status
     * Example of C/S communication:
     * <pre>
     * >>> STATUS
     * <<< 210 status [d/m/c = 0/167/2380096; 211.000r 0.000u 0.000s]
     * </pre>
     *
     * @return  mixed
     * @throws  io.IOException
     */
    public function getStatus() {
      return $this->_sockcmd('STATUS', 210);   
    }  

    /**
     * Set a trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }
  } 
?>
