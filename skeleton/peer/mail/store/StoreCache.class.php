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
    public
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
     * @return  string
     */
    public function toString() {
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
     * @param   string key
     */
    public function remove($key) {
      unset($this->data[$key]);
    }

    /**
     * Expunge cache
     *
     */    
    public function expunge() {
      unset($this->data);
      $this->data= array();
    }

    /**
     * Get a value from cache by key
     *
     * @param   string key
     * @return  mixed value or NULL to indicate the value doesn't exist
     */    
    public function get($key) {
      if (isset($this->data[$key])) return $this->data[$key]; else return NULL;
    }
  
    /**
     * Insert into / update in cache
     *
     * @param   string key
     * @param   mixed val
     */
    public function put($key, $val) {
      $this->data[$key]= $val;
    }
    
    /**
     * Check whether cache has a value by key
     *
     * @param   string key
     * @return  bool TRUE if a value exists
     */
    public function has($key) {
      return isset($this->data[$key]);
    }
  }
?>
