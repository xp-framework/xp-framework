<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.IOException', 'org.gnu.readline.Completer');
  
  define('RL_LIBRARY_VERSION',  'library_version');

  /**
   * Readline
   *
   * Example (main program):
   * <code>
   *   uses('io.File', 'org.gnu.readline.ReadLine', 'TestCompleter');
   * 
   *   // {{{ main
   *   $history= &new File('.history');
   *   if ($history->exists()) {
   *     ReadLine::readHistoryFile($history);
   *   }
   * 
   *   $version= ReadLine::getVar(RL_LIBRARY_VERSION);
   *   ReadLine::setCompleter(new TestCompleter());
   *   while (1) {
   *     $l= ReadLine::readLn('readline '.$version.' > ');
   *     if (FALSE === $l) break;
   *     
   *     var_dump($l);
   *   }
   *   
   *   var_export(ReadLine::getHistory());
   *   ReadLine::writeHistoryFile($history);
   *   // }}}
   * </code>
   *
   * TestCompleter class:
   * <code>
   *   class TestCompleter extends Object {
   * 
   *     function complete($string, $offset, $length) {
   *       return array('Binford', 'Biff', '6100', 'More', 'Power');
   *     }
   *   
   *   } implements(__FILE__, 'org.gnu.readline.Completer');
   * </code>
   *
   * @model    static
   * @ext      readline
   * @purpose  Readline functionality
   */
  class ReadLine extends Object {
  
    /**
     * Wrapper for missing static variables
     *
     * @access  private
     * @param   string name
     * @param   mixed* arg
     * @return  &mixed arg
     */
    function &registry() {
      static $static= array();
      
      switch (func_num_args()) {
        case 1: return $static[func_get_arg(0)];
        case 2: $static[func_get_arg(0)]= &func_get_arg(1); break;
      }
    }
  
    /**
     * Defines a completer
     *
     * @access  public
     * @param   &org.gnu.readline.ReadLineCompleter completer
     * @return  bool success
     * @throws  lang.IllegalArgumentException
     */
    function setCompleter(&$completer) {
      if (!is('org.gnu.readline.Completer', $completer)) {
        return throw(new IllegalArgumentException(
          'Argument is expected to implement org.gnu.readline.Completer'
        ));
      }
      ReadLine::registry('completer', $completer);
      return readline_completion_function('__complete');
    }
  
    /**
     * Read a line
     *
     * @access  public
     * @param   string prompt default ''
     * @param   bool add default TRUE whether to add the read line to the history
     * @return  string line or FALSE if ^D was pressed
     */
    function readLn($prompt= '', $add= TRUE) {
      if (($l= readline($prompt)) && $add) {
        ReadLine::addHistory($l);
      }
      return $l;
    }
    
    /**
     * Get an internal readline variable
     *
     * @access  public
     * @param   const name e.g. RL_LIBRARY_VERSION
     * @return  string
     */
    function getVar($name) {
      return readline_info($name);
    }

    /**
     * Add an item to the history
     *
     * @access  public
     * @param   string l
     * @return  bool success
     */
    function addHistory($l) {
      return readline_add_history($l);
    }

    /**
     * Clear the history
     *
     * @access  public
     * @return  bool success
     */
    function clearHistory() {
      return readline_clear_history();
    }
    
    /**
     * Retrieve the history
     *
     * @access  public
     * @return  string[]
     */
    function getHistory() {
      return readline_list_history();
    }

    /**
     * Load readline history from a file
     *
     * @access  public
     * @param   &io.File file
     * @return  bool success
     * @throws  io.IOException
     */
    function readHistoryFile(&$file) {
      if (FALSE === readline_read_history($file->getURI())) {
        return throw(new IOException('Could not read history from '.$file->getURI()));
      }
      return TRUE;
    }
    
    /**
     * Save readline history to a file
     *
     * @access  public
     * @param   &io.File file
     * @return  bool success
     * @throws  io.IOException
     */
    function writeHistoryFile(&$file) {
      if (FALSE === readline_write_history($file->getURI())) {
        return throw(new IOException('Could not write history to '.$file->getURI()));
      }
      return TRUE;
    }
  }
  
  {
    function __complete($string, $offset, $length) {
      return call_user_func(
        array(ReadLine::registry('completer'), 'complete'), 
        $string, $offset, $length
      );
    }
  }
?>
