<?php
/* This class is part of the XP framework
 *
 * $Id: JnlpDocument.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace com::sun::webstart::jnlp;

  ::uses(
    'xml.Tree',
    'com.sun.webstart.jnlp.JnlpInformation',
    'com.sun.webstart.jnlp.JnlpApplicationDesc',
    'com.sun.webstart.jnlp.JnlpJ2seResource',
    'com.sun.webstart.jnlp.JnlpJarResource',
    'com.sun.webstart.jnlp.JnlpPropertyResource',
    'com.sun.webstart.jnlp.JnlpExtensionResource'
  );
  
  define('JNLP_SPEC_1_PLUS',  '1.0+');
  
  /**
   * JNLP Document
   *
   * @see      http://lopica.sourceforge.net/ref.html - JNLP Tag Reference
   * @purpose  Represents JNLP XML structure
   */
  class JnlpDocument extends xml::Tree {
    public
      $resources    = NULL,
      $information  = NULL,
      $appdesc      = NULL;
      
    public
      $_nodes       = array();

    /**
     * Create a new JNLP document 
     *
     * @param   string codebase
     * @param   string href
     * @param   string spec default JNLP_SPEC_1_PLUS
     */
    public function __construct($codebase= NULL, $href= NULL, $spec= JNLP_SPEC_1_PLUS) {
      parent::__construct('jnlp');
      $this->root->setAttribute('spec', $spec);
      $this->root->setAttribute('codebase', $codebase);
      $this->root->setAttribute('href', $href);
      $this->_nodes['information']= $this->root->addChild(new ('information'));
      $this->_nodes['security']= $this->root->addChild(new ('security'));
      $this->_nodes['resources']= $this->root->addChild(new ('resources'));
      $this->_nodes['application-desc']= $this->root->addChild(new ('application-desc'));
    }

    /**
     * Find first node within a given base (one-level search, no recursion)
     * whose (tag) name matches the specified name.
     *
     * @param   &xml.Node base
     * @param   string name
     * @return  &xml.Node
     */
    protected function findFirst($base, $name) {
      for ($i= 0, $s= sizeof($base->children); $i < $s; $i++) {
        if ($name == $base->children[$i]->getName()) return $base->children[$i];
      }
      return NULL;
    }
    
    /**
     * Extract information from nodesets
     *
     * @throws  lang.FormatException in case extraction fails
     */
    public function extract() {

      // Extract information
      with ($this->_nodes['information']= $this->findFirst($this->root, 'information')); {
        $this->information= new JnlpInformation();
        for ($i= 0, $s= sizeof($this->_nodes['information']->children); $i < $s; $i++) {
          $name= $this->_nodes['information']->children[$i]->getName();
          switch ($name) {
            case 'title':
              $this->information->setTitle(
                $this->_nodes['information']->children[$i]->getContent()
              );
              break;

            case 'vendor':
              $this->information->setVendor(
                $this->_nodes['information']->children[$i]->getContent()
              );
              break;

            case 'description':
              $this->information->setDescription(
                $this->_nodes['information']->children[$i]->getContent(),
                $this->_nodes['information']->children[$i]->getAttribute('kind')
              );
              break;

            case 'homepage':
              $this->information->setHomepage(
                $this->_nodes['information']->children[$i]->getAttribute('href')
              );
              break;

            case 'icon':
              $this->information->setIcon(
                $this->_nodes['information']->children[$i]->getAttribute('href')
              );
              break;

            case 'offline-allowed':
              $this->information->setOfflineAllowed(TRUE);
              break;

            default:
              throw(new lang::FormatException('Unknown identifier "'.$name.'" / Section "information"'));
          }
        }
      }
      
      // Extract security (TBI)
      $this->_nodes['security']= $this->findFirst($this->root, 'security');

      // Extract resources
      with ($this->_nodes['resources']= $this->findFirst($this->root, 'resources')); {
        for ($i= 0, $s= sizeof($this->_nodes['resources']->children); $i < $s; $i++) {
          $node= $this->_nodes['resources']->children[$i];
          switch ($name= $node->getName()) {
            case 'j2se':    // The Java2 version required
              $this->resources[]= new JnlpJ2seResource(
                $node->getAttribute('version'),
                $node->getAttribute('href'),
                $node->getAttribute('initial-heap-size'),
                $node->getAttribute('max-heap-size')
              );
              break;

            case 'jar':     // A jar
              $this->resources[]= new JnlpJarResource(
                $node->getAttribute('href'),
                $node->getAttribute('version')
              );
              break;
            
            case 'property':
              $this->resources[]= new JnlpPropertyResource(
                $node->getAttribute('name'),
                $node->getAttribute('value')
              );
              break;

            case 'nativelib':
              $this->resources[]= new JnlpJarResource(
                $node->getAttribute('href'),
                $node->getAttribute('version')
              );
              break;              

            case 'extension':
              $this->resources[]= new JnlpExtensionResource(
                $node->getAttribute('name'),
                $node->getAttribute('href'),
                $node->getAttribute('version')
              );
              break;              

            default:
              throw(new lang::FormatException('Unknown identifier "'.$name.'" / Section "resources"'));
          }
        }
      }

      // Extract application description
      with ($this->_nodes['application-desc']= $this->findFirst($this->root, 'application-desc')); {
        $this->appdesc= new JnlpApplicationDesc();
        $this->appdesc->setMain_class($this->_nodes['application-desc']->getAttribute('main-class'));
        
        for ($i= 0, $s= sizeof($this->_nodes['application-desc']->children); $i < $s; $i++) {
          $this->appdesc->addArgument($this->_nodes['application-desc']->children[$i]->getContent());
        }
      }
    }

    /**
     * Create a new JNLP document from a string
     *
     * @param   string str
     * @return  &com.sun.webstart.JnlpDocument
     */
    public static function fromString($str) {
      if ($j= parent::fromString($str, __CLASS__)) {
        $j->extract();
      }
      return $j;
    }

    /**
     * Create a new JNLP document from a file
     *
     * @param   &io.File file
     * @return  &com.sun.webstart.JnlpDocument
     */
    public static function fromFile($file) {
      if ($j= parent::fromFile($file, __CLASS__)) {
        $j->extract();
      }
      return $j;
    }
    
    /**
     * Sets the codebase
     *
     * @param   string codebase
     */
    public function setCodebase($codebase) {
      $this->root->setAttribute('codebase', $codebase);
    }

    /**
     * Returns the codebase
     *
     * @return  string
     */
    public function getCodebase() {
      return $this->root->getAttribute('codebase');
    }

    /**
     * Sets the spec
     *
     * @param   string spec
     */
    public function setSpec($spec) {
      $this->root->setAttribute('spec', $spec);
    }

    /**
     * Returns the spec
     *
     * @return  string
     */
    public function getSpec() {
      return $this->root->getAttribute('spec');
    }

    /**
     * Sets the href
     *
     * @param   string href
     */
    public function setHref($href) {
      $this->root->setAttribute('href', $href);
    }

    /**
     * Returns the href
     *
     * @return  string
     */
    public function getHref() {
      return $this->root->getAttribute('href');
    }
    
    /**
     * Add a resource
     *
     * @param   &com.sun.webstart.JnlpResource resource
     * @return  &com.sun.webstart.JnlpResource the added resource
     */
    public function addResource($resource) {
      $this->resources[]= $resource;
      $this->_nodes['resources']->addChild(new (
        $resource->getTagName(), 
        NULL,
        $resource->getTagAttributes()
      ));
      return $resource;
    }
    
    /**
     * Get all resources
     *
     * @return  com.sun.webstart.JnlpResource[]
     */
    public function getResources() {
      return $this->resources;
    }

    /**
     * Get information
     *
     * @return  &com.sun.webstart.JnlpInformation
     */
    public function getInformation() {
      return $this->information;
    }

    /**
     * Set information
     *
     * @param   &com.sun.webstart.JnlpInformation i
     */
    public function setInformation($i) {
      $this->information= $i;
      $this->_nodes['information']->addChild(new ('title', $i->getTitle()));
      $this->_nodes['information']->addChild(new ('vendor', $i->getVendor()));
      foreach ($i->description as $kind => $descr) {
        $this->_nodes['information']->addChild(new (
          'description', 
          $descr, 
          $kind ? array('kind' => $kind) : NULL
        ));
      }
      $this->_nodes['information']->addChild(new ('homepage', NULL, array('href' => $i->getHref())));
      $this->_nodes['information']->addChild(new ('icon', NULL, array('href' => $i->getIcon())));
    }
    
    
    /**
     * Get application description
     *
     * @return  &com.sun.webstart.JnlpApplicationDesc
     */
    public function getApplicationDesc() {
      return $this->appdesc;
    }
  }
?>
