<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('SKEY_FOLDER',   'folder.');
  define('SKEY_LIST',     'list.');
  define('SKEY_INFO',     'info.');
  define('SKEY_MESSAGE',  'message.');
  define('SKEY_HEADER',   'header.');
  define('SKEY_PART',     'part.');

  /**
   * MailStore cache base class
   *
   * @see      xp://peer.mail.MailStore
   * @purpose  Provide an API for caching of MailStore objects
   */
  class StoreCache extends Object {
    var
      $data = array();
    
    /**
     * Create string representation, e.g.
     *
     * <pre>
     * peer.mail.store.StoreCache[5]@{
     *   [folder/INBOX            ] object [mailfolder]
     *   [list/message/INBOX1     ] object [message]
     *   [list/message/INBOX2     ] object [message]
     *   [list/message/INBOX3     ] object [message]
     *   [list/message/INBOX5     ] object [message]
     * }
     * </pre>
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $keys= array_keys($this->data);
      $str= '';
      foreach ($keys as $key) {
        switch (gettype($this->data[$key])) {
          case 'array':  $a= sizeof($this->data[$key]); break;
          case 'object': $a= get_class($this->data[$key]); break;
          default: $a= $this->data[$key];
        }
        $str.= sprintf("  [%-24s] %s [%s]\n", $key, gettype($this->data[$key]), $a);
      }
      return $this->getClassName().'['.sizeof($keys)."]@{\n".$str.'}'; 
    }
    
    /**
     * Remove a key from cache
     *
     * @access  public
     * @param   string key
     */
    function remove($key) {
      unset($this->data[$key]);
    }

    /**
     * Expunge cache
     *
     * @access  public
     */    
    function expunge() {
      unset($this->data);
      $this->data= array();
    }

    /**
     * Get a value from cache by key
     *
     * @access  public
     * @param   string key
     * @return  &mixed value or NULL to indicate the value doesn't exist
     */    
    function &get($key) {
      return isset($this->data[$key]) ? $this->data[$key] : NULL;
    }
  
    /**
     * Insert into / update in cache
     *
     * @access  public
     * @param   string key
     * @param   &mixed val
     */
    function put($key, &$val) {
      $this->data[$key]= &$val;
    }
    
    /**
     * Check whether cache has a value by key
     *
     * @access  public
     * @param   string key
     * @return  bool TRUE if a value exists
     */
    function has($key) {
      return isset($this->data[$key]);
    }
  }
?>
