<?php
/*
 *
 * $Id:$
 */

  uses(
    'xml.Node',
    'org.dia.DiaComponent',
    'lang.IllegalArgumentException'
  );

  /**
   * Base class of all complex elements in a DIAgram
   *
   *
   */
  class DiaCompound extends Object {

    var
      $children= array();

    /**
     * Add DiaComponent to this DiaCompound
     *
     * xmlmapping(xpath = '*', class = 'DiaElement')
     *
     * @access  protected
     * @param   &DiaComponent component
     * @throws  lang.IllegalArgumentException
     */
    function add(&$component) {
      if (!is('DiaComponent', $component)) 
        return throw(new IllegalArgumentException(
          'Wrong object type: '.$component->getClassName()
        ));
      $this->children[$component->hashCode()]= &$component;
    }

    /**
     * Remove DiaComponent from this DiaCompound
     *
     * @access  protected
     * @param   &DiaComponent component
     * @return  bool
     */
    function del(&$component) {
      $hashCode= $component->hashCode();
      if (array_key_exists($hashCode, $this->children)) {
        delete($this->children[$hashCode]);
        unset($this->children[$hashCode]);
        return TRUE;
      } else {
        return FALSE;
      }
    }

    /**
     * Returns DiaComponent child by the given hashCode of the component
     *
     * @access  protected
     * @param   int hashCode
     * @return  &DiaComponent
     */
    function &getChild($hashCode) {
      return $this->children[$hashCode];
    }

    /**
     * Returns all DiaComponent children of given object-type
     *
     * @access  protected
     * @param   string type
     * @return  &DiaComponent
     */
    function &getChildByType($type) {
      $objs= array();
      foreach (array_keys($this->children) as $key) {
        if (is($type, $this->children[$key])) {
          $objs[]= &$this->children[$key];
        }
      }
      return $objs;
    }
    /**
     * TODO: better?
     * childByType: getType() composite, object
     * childByName: getName() attribute, layer(, font)
     */
    function &getChildAttributeByName($name) {
      $attrs= &$this->getChildByType('DiaAttribute');
      foreach (array_keys($attrs) as $key) {
        if ($attrs[$key]->getName() === $name) {
          return $attrs[$key];
        }
      }
      return NULL;
    }

    /********** Interface Methods *************/

    /**
     * Returns XML representation of this DiaCompound
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &new Node($this->node_name);
      foreach (array_keys($this->children) as $key) {
        $node->addChild($this->children[$key]->getNode());
      }
      return $node;
    }

  } implements(__FILE__, 'DiaComponent');
?>
