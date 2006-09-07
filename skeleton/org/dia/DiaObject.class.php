<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaComponent',
    'org.dia.DiaCompound',
    'org.dia.DiaAttribute',
    'org.dia.DiaComposite'
  );

  /**
   * Class representing an object in a DIAgram (dia:object)
   *
   * Implements the methods for getting/setting the following object 'attributes':
   * <ul>
   *  <li>@version</li>
   *  <li>@id</li>
   *  <li>obj_pos</li>
   *  <li>obj_bb</li>
   *  <li>elem_corner</li>
   *  <li>elem_width</li>
   *  <li>elem_height</li>
   *  <li>line_color</li>
   *  <li>fill_color</li>
   *  <li>text_color</li>
   *  <li>stereotype</li>
   *  <li>Text</li>
   * </ul>
   *
   */
  class DiaObject extends DiaCompound {

    var
      $type= NULL,
      $version= NULL,
      $id= NULL,
      $node_name= 'dia:object';

    /**
     * Constructor of an dia object
     *
     * @param   string type
     * @param   string version default NULL
     * @param   string id default NULL
     */
    function __construct($type, $version= NULL, $id= NULL) {
      if (!isset($type)) return;
      $this->setNodeType($type);

      if (isset($version)) $this->setVersion($version);
      // set ID or get a new unique ID
      if (isset($id)) {
        $this->setId($id);
      } else {
        $this->setId(DiaDiagram::getId());
      }
      
      return;
      switch ($type) {

        default: 
          return throw(new IllegalArgumentException('Undefined type "'.$type.'"'));
      }
    }

    /**
     * Return the type of this node
     *
     * @access  protected
     * @return  string
     */
    function getNodeType() {
      return $this->type;
    }

    /**
     * Set the type of this node
     *
     * @access  protected
     * @param   string type
     */
    #[@fromDia(xpath= '@type', value= 'string')]
    function setNodeType($type) {
      $this->type= $type;
    }

    /**
     * Return the version of this Object
     *
     * @access  protected
     * @return  string
     */
    function getVersion() {
      return $this->version;
    }

    /**
     * Set the version string of this object
     *
     * @access  protected
     * @param   string version
     */
    #[@fromDia(xpath= '@version', value= 'string')]
    function setVersion($version) {
      $this->version= $version;
    }

    /**
     * Return the ID of this object
     *
     * @access  protected
     * @return  string
     */
    function getId() {
      return $this->id;
    }

    /**
     * Set the ID of this object
     *
     * @access  protected
     * @param   string id
     */
    #[@fromDia(xpath= '@id', value= 'string')]
    function setId($id) {
      $this->id= $id;
    }

    /**
     * Set the object position (x,y)
     *
     * @access  protected
     * @param   array position The X and Y coordinates within the diagram
     */
    #[@fromDia(xpath= 'dia:attribute[@name="obj_pos"]/dia:point/@val', value= 'array')]
    function setPosition($position) {
      $this->setPoint('obj_pos', $position);
    }

    /**
     * Set the object bounding box coordinates 
     *
     * @access  protected
     * @param   array bbox The X and Y coordinates from the topleft and bottomright corners
     */
    #[@fromDia(xpath= 'dia:attribute[@name="obj_bb"]/dia:rectangle/@val', value= 'arrayarray')]
    function setBoundingBox($bbox) {
      $this->setRectangle('obj_bb', $bbox);
    }

    /**
     * Set the object element corner coordinates
     *
     * @access  protected
     * @param   array corner
     */
    #[@fromDia(xpath= 'dia:attribute[@name="elem_corner"]/dia:point/@val', value= 'array')]
    function setElementCorner($corner) {
      $this->setPoint('elem_corner', $corner);
    }

    /**
     * Set the object element width 
     *
     * @access  protected
     * @param   string width
     */
    #[@fromDia(xpath= 'dia:attribute[@name="elem_width"]/dia:real/@val', value= 'string')]
    function setElementWidth($width) {
      $this->setReal('elem_width', $width);
    }

    /**
     * Set the object element height
     *
     * @access  protected
     * @param   string height
     */
    #[@fromDia(xpath= 'dia:attribute[@name="elem_height"]/dia:real/@val', value= 'string')]
    function setElementHeight($height) {
      $this->setReal('elem_height', $height);
    }

    /**
     * Set the object line color
     *
     * @access  protected
     * @param   string color
     */
    #[@fromDia(xpath= 'dia:attribute[@name="line_color"]/dia:color/@val', value= 'string')]
    function setLineColor($color) {
      $this->setColor('line_color', $color);
    }

    /**
     * Set the object fill color
     *
     * @access  protected
     * @param   string color
     */
    #[@fromDia(xpath= 'dia:attribute[@name="fill_color"]/dia:color/@val', value= 'string')]
    function setFillColor($color) {
      $this->setColor('fill_color', $color);
    }

    /**
     * Set the object text color
     *
     * @access  protected
     * @param   string color
     */
    #[@fromDia(xpath= 'dia:attribute[@name="text_color"]/dia:color/@val', value= 'string')]
    function setTextColor($color) {
      $this->setColor('text_color', $color);
    }

    /**
     * Return the stereotype of the object
     *
     * @access  protected
     * @return  string
     */
    function getStereotype() {
      return $this->getChildValue('stereotype');
    }

    /**
     * Sets the stereotype of the object
     *
     * @access  protected
     * @param   string stereotype
     */
    #[@fromDia(xpath= 'dia:attribute[@name="stereotype"]/dia:string', value= 'string')]
    function setStereotype($stereotype) {
      $this->setString('stereotype', $stereotype);
    }

    /**
     * Return the text of the object
     *
     * @access  protected
     * @return  &org.dia.DiaText
     */
    function getText() {
      // TODO!???
      return $this->getChildValue('text');
    }

    /**
     * Sets the text of the object
     *
     * @access  protected
     * @param   &org.dia.DiaText Text
     */
    #[@fromDia(xpath= 'dia:attribute[@name="text"]/*', class= 'org.dia.DiaText')]
    function setText($Text) {
      if (!isset($this->children['text']))
        $this->set('text', new DiaAttribute('text'));
      $this->children['text']->set($Text->getName(), $Text);
    }

    /************************* Parent Methods *************************/

    /**
     * Return XML representation of DiaComposite
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &parent::getNode();
      if (isset($this->type))
        $node->setAttribute('type', $this->type);
      if (isset($this->version))
        $node->setAttribute('version', $this->version);
      if (isset($this->id))
        $node->setAttribute('id', $this->id);
      return $node;
    }

  } implements(__FILE__, 'org.dia.DiaComponent');
?>
