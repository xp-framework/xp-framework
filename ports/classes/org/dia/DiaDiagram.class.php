<?php
/*
 *
 * $Id: DiaDiagram.class.php 8894 2006-12-19 11:31:53Z kiesel $
 */

  uses(
    'xml.Tree',
    'org.dia.DiaCompound',
    'org.dia.DiaData',
    'org.dia.DiaLayer'
  );
  
  /**
   * Represents a 'dia' diagram as a whole. This is the root class where the
   * (un-)marshalling of diagrams starts.
   *
   * @see   http://www.gnome.org/projects/dia/
   */
  class DiaDiagram extends DiaCompound {

    public
      $ns= array('dia' => 'http://www.lysator.liu.se/~alla/dia/'), // new (unused) 'http://www.gnome.org/projects/dia/'
      $node_name= 'dia:diagram';

    /**
     * Simple constructor
     *
     */
    public function __construct() {
      $this->initialize();
    }

    /**
     * Returns the next ID for an element (auto increment) with leading 'O'
     * (capital 'o' not zero!)
     * 
     * @return  int
     */
    public static function getId() {
      static $id= 0;
      return 'O'.$id++;
      // too complicated: return sprintf('%0'.(strlen($id)+1).'d', $id++);
    }

    /**
     * Initialize this DiaDiagram with DiaData and DiaLayer
     *
     */
    public function initialize() {
      $this->set('data', new DiaData());
      $this->addLayer(new DiaLayer('Background', TRUE));
    }

    /**
     * Returns the namespace with the given prefix
     *
     * @return  string uri The namespace URI
     */
    public function getNamespace($prefix) {
      return $this->ns[$prefix];
    }

    /**
     * Add namespace declaration to root node
     *
     * @param   array namespace Example: array($prefix => $url)
     */
    #[@fromDia(xpath= 'namespace::dia', value= 'namespace')]
    public function addNamespace($namespace) {
      list($prefix, $uri)= each($namespace);
      $this->ns[$prefix]= $uri;
    }

    /**
     * Returns the DiaData object
     *
     * @return  org.dia.DiaData
     */
    public function getData() {
      return $this->getChild('data');
    }

    /**
     * Sets the DiaData object of the diagram
     *
     * @param   org.dia.DiaData Data
     */
    #[@fromDia(xpath= 'dia:diagramdata', class= 'org.dia.DiaData')]
    public function setData($Data) {
      $this->set('data', $Data);
    }

    /**
     * Returns the DiaLayer object with the given name
     *
     * @param   string name default 'Background'
     * @return  org.dia.DiaLayer
     */
    public function getLayer($name= 'Background') {
      $Child= $this->getChild($name);
      if (!is('org.dia.DiaLayer', $Child))
        throw new IllegalArgumentException("The object with name='$name' is no DiaLayer!");
      return $Child;
    }

    /**
     * Adds a DiaLayer object to the diagram
     *
     * @param   org.dia.DiaLayer Layer
     */
    #[@fromDia(xpath= 'dia:layer', class= 'org.dia.DiaLayer')]
    public function addLayer($Layer) {
      $this->set($Layer->getName(), $Layer);
    }

    /**
     * Returns the full XML source of the 'dia' diagram
     *
     * @return  string XML representation of the DIAgramm
     */
    public function getSource($indent= INDENT_DEFAULT) {
      $Node= $this->getNode();
      $Tree= new Tree();
      $Tree->root= $Node;
      return $Tree->getDeclaration()."\n".$Tree->getSource($indent);
    }

    /**
     * Writes the XML representation of this DiaDiagram to the given filename
     *
     * @param   string filename Filename to save the DIAgramm to
     * @param   bool zip default TRUE Gzip the DIAgram file?
     */
    public function saveTo($filename, $zip= TRUE) {
      // open $File according to $zip
      if ($zip) {
        uses('io.ZipFile');
        $File= new ZipFile($filename);
      } else {
        uses('io.File');
        $File= new File($filename);
      }

      // try to write XML source to file
      try {
        $File->open(FILE_MODE_WRITE) && // default compression: 6
        $File->write($this->getSource(INDENT_DEFAULT)); // default indentation
        $File->close();
      } catch (Exception $e) {
        Console::writeLine('Fatal Exception: '.$e->toString());
        exit(-1);
      }
    }

    /************************* interface methods *************************/

    /**
     * Return XML representation of DiaComposite
     *
     * @return  xml.Node
     */
    public function getNode() {
      $node= parent::getNode();
      foreach (array_keys($this->ns) as $prefix) {
        $node->setAttribute('xmlns:'.$prefix, $this->ns[$prefix]);
      }
      return $node;
    }

  }
?>
