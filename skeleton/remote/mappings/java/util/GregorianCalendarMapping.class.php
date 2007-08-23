<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Mapping for java.util.GregorianCalendar
   *
   * @purpose  Mapping
   */
  class GregorianCalendarMapping extends Object {

    public
      $minimalDaysInFirstWeek= NULL,
      $serialVersionOnStream= NULL,
      $gregorianCutover= NULL,
      $firstDayOfWeek= NULL,
      $areFieldsSet= NULL,
      $nextStamp= NULL,
      $isTimeSet= NULL,
      $lenient= NULL,
      $fields= NULL,
      $isSet= NULL,
      $time= NULL,
      $zone= NULL;

    /**
     * Set minimalDaysInFirstWeek
     *
     * @param   lang.Object minimalDaysInFirstWeek
     */
    public function setMinimalDaysInFirstWeek($minimalDaysInFirstWeek) {
      $this->minimalDaysInFirstWeek= $minimalDaysInFirstWeek;
    }

    /**
     * Get minimalDaysInFirstWeek
     *
     * @return  lang.Object
     */
    public function getMinimalDaysInFirstWeek() {
      return $this->minimalDaysInFirstWeek;
    }

    /**
     * Set serialVersionOnStream
     *
     * @param   lang.Object serialVersionOnStream
     */
    public function setSerialVersionOnStream($serialVersionOnStream) {
      $this->serialVersionOnStream= $serialVersionOnStream;
    }

    /**
     * Get serialVersionOnStream
     *
     * @return  lang.Object
     */
    public function getSerialVersionOnStream() {
      return $this->serialVersionOnStream;
    }

    /**
     * Set gregorianCutover
     *
     * @param   lang.Object gregorianCutover
     */
    public function setGregorianCutover($gregorianCutover) {
      $this->gregorianCutover= $gregorianCutover;
    }

    /**
     * Get gregorianCutover
     *
     * @return  lang.Object
     */
    public function getGregorianCutover() {
      return $this->gregorianCutover;
    }

    /**
     * Set firstDayOfWeek
     *
     * @param   lang.Object firstDayOfWeek
     */
    public function setFirstDayOfWeek($firstDayOfWeek) {
      $this->firstDayOfWeek= $firstDayOfWeek;
    }

    /**
     * Get firstDayOfWeek
     *
     * @return  lang.Object
     */
    public function getFirstDayOfWeek() {
      return $this->firstDayOfWeek;
    }

    /**
     * Set areFieldsSet
     *
     * @param   lang.Object areFieldsSet
     */
    public function setAreFieldsSet($areFieldsSet) {
      $this->areFieldsSet= $areFieldsSet;
    }

    /**
     * Get areFieldsSet
     *
     * @return  lang.Object
     */
    public function getAreFieldsSet() {
      return $this->areFieldsSet;
    }

    /**
     * Set nextStamp
     *
     * @param   lang.Object nextStamp
     */
    public function setNextStamp($nextStamp) {
      $this->nextStamp= $nextStamp;
    }

    /**
     * Get nextStamp
     *
     * @return  lang.Object
     */
    public function getNextStamp() {
      return $this->nextStamp;
    }

    /**
     * Set isTimeSet
     *
     * @param   lang.Object isTimeSet
     */
    public function setIsTimeSet($isTimeSet) {
      $this->isTimeSet= $isTimeSet;
    }

    /**
     * Get isTimeSet
     *
     * @return  lang.Object
     */
    public function getIsTimeSet() {
      return $this->isTimeSet;
    }

    /**
     * Set lenient
     *
     * @param   lang.Object lenient
     */
    public function setLenient($lenient) {
      $this->lenient= $lenient;
    }

    /**
     * Get lenient
     *
     * @return  lang.Object
     */
    public function getLenient() {
      return $this->lenient;
    }

    /**
     * Set fields
     *
     * @param   lang.Object fields
     */
    public function setFields($fields) {
      $this->fields= $fields;
    }

    /**
     * Get fields
     *
     * @return  lang.Object
     */
    public function getFields() {
      return $this->fields;
    }

    /**
     * Set isSet
     *
     * @param   lang.Object isSet
     */
    public function setIsSet($isSet) {
      $this->isSet= $isSet;
    }

    /**
     * Get isSet
     *
     * @return  lang.Object
     */
    public function getIsSet() {
      return $this->isSet;
    }

    /**
     * Set time
     *
     * @param   lang.Object time
     */
    public function setTime($time) {
      $this->time= $time;
    }

    /**
     * Get time
     *
     * @return  lang.Object
     */
    public function getTime() {
      return $this->time;
    }

    /**
     * Set zone
     *
     * @param   lang.Object zone
     */
    public function setZone($zone) {
      $this->zone= $zone;
    }

    /**
     * Get zone
     *
     * @return  lang.Object
     */
    public function getZone() {
      return $this->zone;
    }

    /**
     * Get a date from the GregorianDate
     *
     * @return  util.Date
     */
    public function toDate() {
      return new Date($this->getTime()->floatValue() / 1000);
    }
  } 
?>
