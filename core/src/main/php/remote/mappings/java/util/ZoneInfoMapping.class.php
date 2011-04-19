<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Mapping for sun.util.calendar.ZoneInfo
   *
   * @purpose  Mapping
   */
  class ZoneInfoMapping extends Object {

    var
      $rawOffset= NULL,
      $rawOffsetDiff= NULL,
      $checksum= NULL,
      $dstSavings= NULL,
      $transitions= NULL,
      $offsets= NULL,
      $simpleTimeZoneParams= NULL,
      $willGMTOffsetChange= NULL,
      $ID= NULL;

    /**
     * Set rawOffset
     *
     * @param   lang.Object rawOffset
     */
    public function setRawOffset($rawOffset) {
      $this->rawOffset= $rawOffset;
    }

    /**
     * Get rawOffset
     *
     * @return  lang.Object
     */
    public function getRawOffset() {
      return $this->rawOffset;
    }

    /**
     * Set rawOffsetDiff
     *
     * @param   lang.Object rawOffsetDiff
     */
    public function setRawOffsetDiff($rawOffsetDiff) {
      $this->rawOffsetDiff= $rawOffsetDiff;
    }

    /**
     * Get rawOffsetDiff
     *
     * @return  lang.Object
     */
    public function getRawOffsetDiff() {
      return $this->rawOffsetDiff;
    }

    /**
     * Set checksum
     *
     * @param   lang.Object checksum
     */
    public function setChecksum($checksum) {
      $this->checksum= $checksum;
    }

    /**
     * Get checksum
     *
     * @return  lang.Object
     */
    public function getChecksum() {
      return $this->checksum;
    }

    /**
     * Set dstSavings
     *
     * @param   lang.Object dstSavings
     */
    public function setDstSavings($dstSavings) {
      $this->dstSavings= $dstSavings;
    }

    /**
     * Get dstSavings
     *
     * @return  lang.Object
     */
    public function getDstSavings() {
      return $this->dstSavings;
    }

    /**
     * Set transitions
     *
     * @param   lang.Object transitions
     */
    public function setTransitions($transitions) {
      $this->transitions= $transitions;
    }

    /**
     * Get transitions
     *
     * @return  lang.Object
     */
    public function getTransitions() {
      return $this->transitions;
    }

    /**
     * Set offsets
     *
     * @param   lang.Object offsets
     */
    public function setOffsets($offsets) {
      $this->offsets= $offsets;
    }

    /**
     * Get offsets
     *
     * @return  lang.Object
     */
    public function getOffsets() {
      return $this->offsets;
    }

    /**
     * Set simpleTimeZoneParams
     *
     * @param   lang.Object simpleTimeZoneParams
     */
    public function setSimpleTimeZoneParams($simpleTimeZoneParams) {
      $this->simpleTimeZoneParams= $simpleTimeZoneParams;
    }

    /**
     * Get simpleTimeZoneParams
     *
     * @return  lang.Object
     */
    public function getSimpleTimeZoneParams() {
      return $this->simpleTimeZoneParams;
    }

    /**
     * Set willGMTOffsetChange
     *
     * @param   lang.Object willGMTOffsetChange
     */
    public function setWillGMTOffsetChange($willGMTOffsetChange) {
      $this->willGMTOffsetChange= $willGMTOffsetChange;
    }

    /**
     * Get willGMTOffsetChange
     *
     * @return  lang.Object
     */
    public function getWillGMTOffsetChange() {
      return $this->willGMTOffsetChange;
    }

    /**
     * Set ID
     *
     * @param   lang.Object ID
     */
    public function setID($ID) {
      $this->ID= $ID;
    }

    /**
     * Get ID
     *
     * @return  lang.Object
     */
    public function getID() {
      return $this->ID;
    }
  } 
?>
