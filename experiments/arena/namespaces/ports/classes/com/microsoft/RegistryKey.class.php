<?php
/* This class is part of the XP framework
 *
 * $Id: RegistryKey.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::microsoft;
 
  ::uses('com.microsoft.RegistryException', 'com.microsoft.wscript.WshShell');

  define('REG_SZ',        'REG_SZ');
  define('REG_EXPAND_SZ', 'REG_EXPAND_SZ'); 
  define('REG_DWORD',     'REG_DWORD');
  define('REG_BINARY',    'REG_BINARY'); 
  
  /**
   * Registry
   *
   * Usage example (reading a key)
   * <code> 
   *   uses('com.microsoft.RegistryKey');
   *   
   *   $k= &new RegistryKey($argv[1]);
   *   printf('Reading key %s (exists: %d)', $argv[1], $k->exists());
   *   
   *   try(); {
   *     $value= $k->getValue();
   *   } if (catch('RegistryException', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   
   *   var_dump($value);
   * </code>
   *
   * Usage example (creating a key)
   * <code> 
   *   uses('com.microsoft.RegistryKey');
   *   
   *   $k= &new RegistryKey($argv[1]);
   *   printf('Creating key %s and setting its value to 6100 (REG_DWORD)', $argv[1]);
   *   
   *   try(); {
   *     $k->setValue(6100, REG_DWORD);
   *     $value= $k->getValue();
   *   } if (catch('RegistryException', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   
   *   var_dump($value);
   * </code>
   *
   * @see      http://msdn.microsoft.com/library/en-us/script56/html/wsmthregwrite.asp?frame=true
   * @see      http://msdn.microsoft.com/library/default.asp?url=/library/en-us/script56/html/wsObjWScript.asp
   * @ext      com
   * @purpose  Registry access
   * @platform Windows
   */
  class RegistryKey extends lang::Object {
    public 
      $name = '';
       
    public
      $_sh  = NULL;
  
    /**
     * Constructor
     *
     * @param   string name e.g. HKEY_CURRENT_USER\Environment\TMP
     */    
    public function __construct($name) {
      
      $this->name= $name;
      $this->_sh= com::microsoft::wscript::WshShell::getInstance();
    }
    
    /**
     * Get this key's name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Checks whether this key exists
     *
     * @return  bool
     */
    public function exists() {
    
      // Really ugly, but the WshShell object does not have a 
      // regExists() method
      return (NULL !== @$this->_sh->regRead($this->name));
    }
    
    /**
     * Deletes this key
     *
     * @return  bool
     * @throws  com.microsoft.RegistryException
     */
    public function delete() {
      if (NULL === $this->_sh->regDelete($this->name)) {
        throw(new RegistryException('Could not delete key "'.$this->name.'"'));        
      }
      return TRUE;
    }
    
    /**
     * Read this key's value
     *
     * @return  &mixed   
     * @throws  com.microsoft.RegistryException
     */
    public function getValue() {
      if (NULL === ($v= $this->_sh->regRead($this->name))) {
        throw(new RegistryException('Could not read key "'.$this->name.'"'));  
      }
      return $v;
    }
    
    /**
     * Set this key's value. Creates the key if necessary.
     *
     * @param   mixed val
     * @param   string type default REG_SZ
     * @return  bool
     * @throws  com.microsoft.RegistryException
     */
    public function setValue($val, $type= REG_SZ) {
      if (NULL === $this->_sh->regWrite($this->name, $val, $type)) {
        throw(new RegistryException('Could not write key "'.$this->name.'"'));        
      }
      return TRUE;
    }
  }
?>
