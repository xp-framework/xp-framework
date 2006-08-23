<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.cca.ArchiveClassLoader',
    'io.File',
    'io.Folder',
    'util.Properties',
    'remote.server.deploy.IncompleteDeployment'
  );
  
  /**
   * Deployment scanner that as
   *
   * @purpose  Interface
   */
  class FileSystemScanner extends Object {
    var
      $folder   = NULL,
      $pattern  = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string dir
     * @param   string pattern default ".xar$"
     */
    function __construct($dir, $pattern= '.xar$') {
      $this->folder= &new Folder($dir);
      $this->pattern= '/'.$pattern.'/';
    }
  
    /**
     * Get a list of deployments
     *
     * @access  public
     * @return  remote.server.deploy.Deployable[]
     */
    function getDeployments() {
      $deployments= array();

      while ($entry= $this->folder->getEntry()) {
        if (!preg_match($this->pattern, $entry)) continue;

        $ear= &new Archive(new File($this->folder->getURI().$entry));
        try(); {
          $ear->open(ARCHIVE_READ) &&
          $meta= $ear->extract('META-INF/bean.properties');
        } if (catch('Exception', $e)) {  
          $deployments[]= &new IncompleteDeployment($entry, $e);
          continue;
        }
        
        $prop= &Properties::fromString($meta);
        $beanclass= $prop->readString('bean', 'class');
        
        if (!$beanclass) {
          $deployments[]= &new IncompleteDeployment($entry, new FormatException('bean.class property missing!'));
          continue;
        }

        $cl= &new ArchiveClassLoader($ear);
        try(); {
          $class= &$cl->loadClass($beanclass);
          $class && $deployments[]= &new Deployment($entry, $class);
        } if (catch('Exception', $e)) {
          $deployments[]= &new IncompleteDeployment($entry, $e);
        }
      }

      return $deployments;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'(pattern= '.$this->pattern.') {'.$this->folder->toString().'}';
    }

  } implements(__FILE__, 'remote.server.deploy.scan.DeploymentScanner');
?>
