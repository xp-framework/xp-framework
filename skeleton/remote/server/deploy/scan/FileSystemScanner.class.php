<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.archive.Archive',
    'io.File',
    'io.Folder',
    'util.Properties',
    'remote.server.deploy.IncompleteDeployment',
    'remote.server.deploy.scan.DeploymentScanner'
  );
  
  /**
   * Deployment scanner that as
   *
   * @purpose  Interface
   */
  class FileSystemScanner extends Object implements DeploymentScanner {
    public
      $folder   = NULL,
      $pattern  = '',
      $files    = array(),
      $deployments= array();
      
      
    /**
     * Constructor
     *
     * @param   string dir
     * @param   string pattern default ".xar$"
     */
    public function __construct($dir, $pattern= '.xar$') {
      $this->folder= new Folder($dir);
      $this->pattern= '/'.$pattern.'/';
    }
  
    /**
     * Get a list of deployments
     *
     * @return  remote.server.deploy.Deployable[]
     */
    public function scanDeployments() {
      clearstatcache();
      $this->changed= FALSE;

      while ($entry= $this->folder->getEntry()) {
        if (!preg_match($this->pattern, $entry)) continue;
        
        $f= new File($this->folder->getURI().$entry);
        
        if (isset($this->files[$entry]) && $f->lastModified() <= $this->files[$entry]) {
        
          // File already deployed
          continue;
        }
        
        $this->changed= TRUE;

        $ear= new Archive(new File($this->folder->getURI().$entry));
        try {
          $ear->open(ARCHIVE_READ) &&
          $meta= $ear->extract('META-INF/bean.properties');
        } catch (Throwable $e) {
          $this->deployments[$entry]= new IncompleteDeployment($entry, $e);
          continue;
        }
        
        $prop= Properties::fromString($meta);
        $beanclass= $prop->readString('bean', 'class');
        
        if (!$beanclass) {
          $this->deployments[$entry]= new IncompleteDeployment($entry, new FormatException('bean.class property missing!'));
          continue;
        }

        $d= new Deployment($entry);
        $d->setClassLoader(new ArchiveClassLoader($ear));
        $d->setImplementation($beanclass);
        $d->setInterface($prop->readString('bean', 'remote'));
        $d->setDirectoryName($prop->readString('bean', 'lookup'));
        
        $this->deployments[$entry]= $d; 
        $this->files[$entry]= time();

        delete($f);
      }
      
      // Check existing deployments
      foreach (array_keys($this->deployments) as $entry) {
        
        $f= new File($this->folder->getURI().$entry);
        if (!$f->exists()) {
          unset($this->deployments[$entry], $this->files[$entry]);
          
          $this->changed= TRUE;
        }

        delete($f);
      }

      $this->folder->close();
      return $this->changed;
    }
    
    /**
     * Get deployments
     *
     * @return  var[] deployments
     */
    public function getDeployments() {
      return $this->deployments;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'(pattern= '.$this->pattern.') {'.$this->folder->toString().'}';
    }

  } 
?>
