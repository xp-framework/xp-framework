<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.Tree',
    'com.sun.webstart.jnlp.JnlpInformation',
    'com.sun.webstart.jnlp.JnlpApplicationDesc',
    'com.sun.webstart.jnlp.JnlpJ2seResource',
    'com.sun.webstart.jnlp.JnlpJarResource',
    'com.sun.webstart.jnlp.JnlpPropertyResource'
  );

  define('JNLP_SPEC_1_PLUS',  '1.0+');
  
  /**
   * JNLP
   *
   * @see      reference
   * @purpose  purpose
   */
  class JnlpDocument extends Tree {
    public
      $resources    = NULL,
      $information  = NULL,
      $appdesc      = NULL;
      
    protected
      $_nodes       = array();

    /**
     * Create a new JNLP document 
     *
     * @access  public
     * @param   string codebase
     * @param   string href
     * @param   string spec default JNLP_SPEC_1_PLUS
     */
    public function __construct($codebase, $href, $spec= JNLP_SPEC_1_PLUS) {
      $this->root= new Node('jnlp', NULL, array(
        'spec'      => $spec,
        'codebase'  => $codebase,
        'href'      => $href,
      ));
      $this->_nodes['information']= $this->root->addChild(new Node('information'));
      $this->_nodes['security']= $this->root->addChild(new Node('security'));
      $this->_nodes['resources']= $this->root->addChild(new Node('resources'));
      $this->_nodes['application-desc']= $this->root->addChild(new Node('application-desc'));
    }

    /**
     * Find first node within a given base (one-level search, no recursion)
     * whose (tag) name matches the specified name.
     *
     * @access  protected
     * @param   &xml.Node base
     * @param   string name
     * @return  &xml.Node
     */
    protected function findFirst(Node $base, $name) {
      for ($i= 0, $s= sizeof($base->children); $i < $s; $i++) {
        if ($name == $base->children[$i]->getName()) return $base->children[$i];
      }
      return NULL;
    }
    
    /**
     * Extract information from nodesets
     *
     * @access  protected
     * @throws  lang.FormatException in case extraction fails
     */
    protected function extract() {

      // Extract information
      with ($this->_nodes['information']= self::findFirst($this->root, 'information')); {
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

            default:
              throw (new FormatException('Unknown identifier "'.$name.'" / Section "information"'));
          }
        }
      }
      
      // Extract security (TBI)
      $this->_nodes['security']= self::findFirst($this->root, 'security');

      // Extract resources
      with ($this->_nodes['resources']= self::findFirst($this->root, 'resources')); {
        for ($i= 0, $s= sizeof($this->_nodes['resources']->children); $i < $s; $i++) {
          $name= $this->_nodes['resources']->children[$i]->getName();
          switch ($name) {
            case 'j2se':    // The Java2 version required
              $this->resources[]= new JnlpJ2seResource(
                $this->_nodes['resources']->children[$i]->getAttribute('version')
              );
              break;

            case 'jar':     // A jar
              $this->resources[]= new JnlpJarResource(
                $this->_nodes['resources']->children[$i]->getAttribute('href')
              );
              break;
            
            case 'property':
              $this->resources[]= new JnlpPropertyResource(
                $this->_nodes['resources']->children[$i]->getAttribute('name'),
                $this->_nodes['resources']->children[$i]->getAttribute('value')
              );
              break;

            default:
              throw (new FormatException('Unknown identifier "'.$name.'" / Section "resources"'));
          }
        }
      }

      // Extract application description
      with ($this->_nodes['application-desc']= self::findFirst($this->root, 'application-desc')); {
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
     * @model   static
     * @access  public
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
     * @model   static
     * @access  public
     * @param   &io.File file
     * @return  &com.sun.webstart.JnlpDocument
     */
    public static function fromFile(File $file) {
      if ($j= parent::fromFile($file, __CLASS__)) {
        $j->extract();
      }
      return $j;
    }
    
    /**
     * Sets the codebase
     *
     * @access  public
     * @param   string codebase
     */
    public function setCodebase($codebase) {
      $this->root->setAttribute('codebase', $codebase);
    }

    /**
     * Returns the codebase
     *
     * @access  public
     * @return  string
     */
    public function getCodebase() {
      return $this->root->getAttribute('codebase');
    }

    /**
     * Sets the spec
     *
     * @access  public
     * @param   string spec
     */
    public function setSpec($spec) {
      $this->root->setAttribute('spec', $spec);
    }

    /**
     * Returns the spec
     *
     * @access  public
     * @return  string
     */
    public function getSpec() {
      return $this->root->getAttribute('spec');
    }

    /**
     * Sets the href
     *
     * @access  public
     * @param   string href
     */
    public function setHref($href) {
      $this->root->setAttribute('href', $href);
    }

    /**
     * Returns the href
     *
     * @access  public
     * @return  string
     */
    public function getHref() {
      return $this->root->getAttribute('href');
    }
    
    /**
     * Add a resource
     *
     * @access  public
     * @param   &com.sun.webstart.JnlpResource resource
     * @return  &com.sun.webstart.JnlpResource the added resource
     */
    public function addResource(JnlpResource $resource) {
      $this->resources[]= $resource;
      $this->_nodes['resources']->addChild(new Node(
        $resource->getTagName(), 
        NULL,
        $resource->getTagAttributes()
      ));
      return $resource;
    }
    
    /**
     * Get all resources
     *
     * @access  public
     * @return  com.sun.webstart.JnlpResource[]
     */
    public function getResources() {
      return $this->resources;
    }

    /**
     * Get information
     *
     * @access  public
     * @return  &com.sun.webstart.JnlpInformation
     */
    public function getInformation() {
      return $this->information;
    }

    /**
     * Set information
     *
     * @access  public
     * @param   &com.sun.webstart.JnlpInformation i
     */
    public function setInformation(JnlpInformation $i) {
      $this->information= $i;
      $this->_nodes['information']->addChild(new Node('title', $i->getTitle()));
      $this->_nodes['information']->addChild(new Node('vendor', $i->getVendor()));
      foreach ($i->description as $kind => $descr) {
        $this->_nodes['information']->addChild(new Node(
          'description', 
          $descr, 
          $kind ? array('kind' => $kind) : NULL
        ));
      }
      $this->_nodes['information']->addChild(new Node('homepage', NULL, array('href' => $i->getHref())));
      $this->_nodes['information']->addChild(new Node('icon', NULL, array('href' => $i->getIcon())));
    }
    
    
    /**
     * Get application description
     *
     * @access  public
     * @return  &com.sun.webstart.JnlpApplicationDesc
     */
    public function getApplicationDesc() {
      return $this->appdesc;
    }
  }
?>
