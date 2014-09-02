<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.Object',
    'util.PropertyAccess'
  );

  /**
   * Abstract property decorator
   */
  abstract class PropertyDecorator extends Object implements PropertyAccess {

    protected $properties;

    /**
     * @param PropertyAccess $decoratedProperties
     */
    public function __construct(PropertyAccess $decoratedProperties) {
      $this->properties= $decoratedProperties;
    }

    /**
     * Returns the decorated properties object
     *
     * @return PropertyAccess
     */
    public function getDecoratedProperties() {
      return $this->properties;
    }

    /**
     * Read array value
     *
     * @param   string $section
     * @param   string $key
     * @param   mixed $default default array()
     * @return  string[]
     */
    public function readArray($section, $key, $default= array()) {
      return $this->properties->readArray($section, $key, $default);
    }

    /**
     * Read hash value
     *
     * @param   string $section
     * @param   string $key
     * @param   mixed $default default NULL
     * @return  $util.Hashmap
     */
    public function readHash($section, $key, $default= NULL) {
      return $this->properties->readHash($section, $key, $default);
    }

    /**
     * Read bool value
     *
     * @param   string $section
     * @param   string $key
     * @param   bool $default default FALSE
     * @return  bool
     */
    public function readBool($section, $key, $default= FALSE) {
      return $this->properties->readBool($section, $key, $default);
    }

    /**
     * Read string value
     *
     * @param   string $section
     * @param   string $key
     * @param   mixed $default default NULL
     * @return  string
     */
    public function readString($section, $key, $default= NULL) {
      return $this->properties->readString($section, $key, $default);
    }

    /**
     * Read integer value
     *
     * @param   string $section
     * @param   string $key
     * @param   mixed $default default 0
     * @return  int
     */
    public function readInteger($section, $key, $default= 0) {
      return $this->properties->readInteger($section, $key, $default);
    }

    /**
     * Read float value
     *
     * @param   string $section
     * @param   string $key
     * @param   mixed $default default array()
     * @return  double
     */
    public function readFloat($section, $key, $default= 0.0) {
      return $this->properties->readFloat($section, $key, $default);
    }

    /**
     * Read section
     *
     * @param   string $section
     * @param   mixed $default default array()
     * @return  [:string]
     */
    public function readSection($section, $default= array()) {
      return $this->properties->readSection($section, $default);
    }

    /**
     * Read range value
     *
     * @param   string $section
     * @param   string $key
     * @param   mixed $default default 0.0
     * @return  int[]
     */
    public function readRange($section, $key, $default= array()) {
      return $this->properties->readRange($section, $key, $default);
    }

    /**
     * Test whether a given section exists
     *
     * @param   string $section
     * @return  bool
     */
    public function hasSection($section) {
      return $this->properties->hasSection($section);
    }

    /**
     * Retrieve first section name, set internal pointer
     *
     * @return  string
     */
    public function getFirstSection() {
      return $this->properties->getFirstSection();
    }

    /**
     * Retrieve next section name, NULL if no more sections exist
     *
     * @return  string
     */
    public function getNextSection() {
      return $this->properties->getNextSection();
    }

    /**
     * Creates a string representation of this property file
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName() . '(' .  $this->properties->toString() . ')';
    }

  }
