<?php
/*
 *
 * $Id:$
 */

  uses(
    'xml.Tree',
    'org.dia.DiaCompound',
    'org.dia.DiaData',
    'org.dia.DiaLayer',
    'org.dia.DiaObject',
    'org.dia.DiaUMLClass'
  );
  
  /**
   * Represents a 'dia' diagram
   */
  class DiaDiagram extends DiaCompound {

    var
      $ns= array('dia' => 'http://www.gnome.org/projects/dia/'),
      $node_name= 'dia:diagram';

    /**
     * Returns the next ID for an element (auto increment) with leading '0'
     * 
     *
     * @model   static
     * @return  int
     */
    function getId() {
      static $id= 0;
      return '0'.$id++;
      // too complicated: return sprintf('%0'.(strlen($id)+1).'d', $id++);
    }

    /**
     * Initialize this DiaDiagram with DiaData and DiaLayer
     *
     * @access  protected
     */
    function initialize() {
      $data= &new DiaData();
      $data->initialize();
      $this->add($data);
      $this->add(new DiaLayer('Background', TRUE));
    }

    /**
     * Add namespace declaration to root node
     *
     * @access  protected
     * @param   array ns
     */
    #[@xmlmapping(xpath = '@xmlns', type = 'array')]
    function addNamespace($ns) {
      $this->ns[]= &$ns;
    }

    /**
     * Returns the full XML source of the 'dia' diagram
     *
     * @return  string XML representation of the DIAgramm
     */
    function getSource($indent= INDENT_DEFAULT) {
      $Node= &$this->getNode();
      $Tree= &new Tree();
      $Tree->root= &$Node;
      return $Tree->getDeclaration()."\n".$Tree->getSource($indent);
    }

    /**
     * @param   string filename Filename to save the DIAgramm to
     * @param   boolean zip default TRUE Gzip the DIAgram file?
     */
    function saveTo($filename, $zip= TRUE) {
      // open $File according to $zip
      if ($zip) {
        uses('io.ZipFile');
        $File= &new ZipFile($filename);
      } else {
        uses('io.File');
        $File= &new File($filename);
      }

      // try to write XML source to file
      try (); {
        $File->open(FILE_MODE_WRITE) && // default compression: 6
        $File->write($this->getSource(INDENT_DEFAULT)); // default indentation
        $File->close();
      } if (catch('Exception', $e)) {
        Console::writeLine('Fatal Exception: '.$e->toString());
        exit(-1);
      }
    }

    /************************* Parent Functions *************************/

    /**
     * Return XML representation of DiaComposite
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &parent::getNode();
      while (list($ns, $url)= each($this->ns)) {
        $node->setAttribute('xmlns:'.$ns, $url);
      }
      return $node;
    }

  }
?>
