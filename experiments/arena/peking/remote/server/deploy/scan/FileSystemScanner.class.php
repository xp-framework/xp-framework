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
      $pattern  = '',
      $files    = array();
      
      
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
    function scanDeployments() {
      $deployments= array();
      $this->folder->isOpen() && $this->folder->rewind();

      while ($entry= $this->folder->getEntry()) {
        if (!preg_match($this->pattern, $entry)) continue;
        
        $f= &new File($this->folder->getURI().$entry);

        if (isset($this->files[$f->getURI()]) && $f->lastModified() > $this->files[$f->getURI()]) {
        
          // Deployment changed. Server must be restarted
          return  FALSE;
        }
        
        if (isset($this->files[$f->getURI()]) && $f->lastModified() <= $this->files[$f->getURI()]) {
        
          // File already deployed
          continue;
        }

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

        $d= &new Deployment($entry);
        $d->setClassLoader(new ArchiveClassLoader($ear));
        $d->setImplementation($beanclass);
        $d->setInterface($prop->readString('bean', 'remote'));
        $d->setDirectoryName($prop->readString('bean', 'lookup'));
        
        $deployments[]= &$d;
        
        $this->files[$f->getURI()]= time();
        
        unset($f);
      }
      
      clearstatcache();
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
