<?php
/* Diese Klasse ist Bestandteil des XP-Frameworks
 *
 * $Id$
 */

  uses('util.log.LogCategory');
  
  define('LOG_DEFINES_DEFAULT', 'default');
  
  /**
   * Kapselt einen Logger (SingleTon)
   * 
   * Beispielzeilen:
   * <pre>
   * [20:45:30 16012 info] ===> Starting work on 2002/05/29/ 
   * [20:45:30 16012 info] ===> Done, 0 order(s) processed, 0 error(s) occured 
   * [20:45:30 16012 info] ===> Finish 
   * </pre>
   *
   * Das Format des fixen Teils jeder Log-Zeile kann über:
   * - den Identifier [eine ID, die im Log Wiedererkennungswert hat, bspw. die PID]
   * - die Variable "format" (wie soll der fixe Teil formatiert werden)
   * festgelegt werden.
   *
   * Hinweise:
   * - Der Identifier defaultet auf die PID
   * - Die Reihenfolge für den Format-String "format" ist wie folgt:
   *   1) Das Datum
   *   2) Der Identifier
   *   3) Der Indicator [info, warn, error oder debug]
   *   Der Format-String "format" defaultet auf "[%1$s %2$s %3$s]"
   * - Das Datumsformat "dateformat" defaultet auf "H:i:s", siehe http://php.net/date
   *
   * @model singleton
   */
  class Logger extends Object {
    var 
      $category= array();
    
    var
      $defaultIdentifier,
      $defaultDateformat,
      $defaultFormat,
      $defaultFlags,
      $defaultAppenders;
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */ 
    function &getCategory($name= LOG_DEFINES_DEFAULT) {
      if (!isset($this->category[$name])) $name= LOG_DEFINES_DEFAULT;
      return $this->category[$name];
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function configure(&$prop) {
    
      // Read default properties
      $this->defaultIdentifier= $prop->readString(LOG_DEFINES_DEFAULT, 'identifier', $this->defaultIdentifier);
      $this->defaultFormat= $prop->readString(LOG_DEFINES_DEFAULT, 'format', $this->defaultFormat);
      $this->defaultDateformat= $prop->readString(LOG_DEFINES_DEFAULT, 'date.format', $this->defaultDateformat);
      $this->defaultFlags= $prop->readInteger(LOG_DEFINES_DEFAULT, 'flags', $this->defaultFlags);
      $this->defaultAppenders= $prop->readArray(LOG_DEFINES_DEFAULT, 'appenders', $this->defaultAppenders);
      
      // Read all other properties
      $section= $prop->getFirstSection();
      do {
        // Create new
        $this->category[$section]= &new LogCategory(
          $this->defaultIdentifier,
          $prop->readString($section, 'format', $this->defaultFormat),
          $prop->readString($section, 'date.format', $this->defaultDateformat),
          $prop->readInteger($section, 'flags', $this->defaultFlags)
        );
        
        // Has an appender?
        $param_section= $section;
        if (NULL === ($appenders= $prop->readArray($section, 'appenders', NULL))) {
          $appenders= $this->defaultAppenders;
          $param_section= LOG_DEFINES_DEFAULT;
        }
        
        // Go through all of the appenders
        foreach ($appenders as $appender) {
          try(); {
            $reflect= ClassLoader::loadClass($appender);
          } if (catch('Exception', $e)) {
            return throw($e);
          }
          $a= &$this->category[$section]->addAppender(new $reflect());
          $params= $prop->readArray($param_section, 'appender.'.$appender.'.params', array());
          
          // Params
          foreach ($params as $param) {
            $a->{$param}= strftime(
              $prop->readString(
                $param_section, 
                'appender.'.$appender.'.param.'.$param,
                ''
              )
            );
          }
        }
      } while ($section= $prop->getNextSection());
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function finalize() {
      foreach (array_keys($this->category) as $name) {
        $this->category[$name]->finalize();
      }
    }
  
    /**
     * Gibt eine Instanz zurück
     *
     * @access  public
     * @return  Logger Das Logger-Objekt
     */
    function &getInstance() {
      static $__instance;
  
      if (!isset($__instance)) {
        $__instance= new Logger();
        $__instance->defaultIdentifier= getmypid();
        $__instance->defaultFormat= '[%1$s %2$s %3$s]';
        $__instance->defaultDateformat= 'H:i:s';
        $__instance->defaultFlags= LOGGER_FLAG_ALL;
        $__instance->defaultAppenders= array();
        
        // Create an empty LogCategory
        $__instance->category[LOG_DEFINES_DEFAULT]= &new LogCategory(
          $__instance->defaultIdentifier,
          $__instance->defaultFormat,
          $__instance->defaultDateformat,
          $__instance->defaultFlags
        );

      }
      return $__instance;
    }
  }
?>
