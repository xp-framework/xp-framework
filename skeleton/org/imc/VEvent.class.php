<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('util.Date');

  /**
   * VEvent
   * 
   * @see      xp://org.imc.VCalendar
   * @purpose  Represent a single event
   */
  class VEvent extends Object {
    var
      $date         = NULL,
      $starts       = NULL,
      $ends         = NULL,
      $summary      = '',
      $location     = '',
      $description  = '',
      $attendee     = array(),
      $organizer    = '';

    /**
     * Set Date
     *
     * @access  public
     * @param   &util.Date date
     */
    function setDate(&$date) {
      $this->date= &$date;
    }

    /**
     * Get Date
     *
     * @access  public
     * @return  &util.Date
     */
    function &getDate() {
      return $this->date;
    }

    /**
     * Set Starts
     *
     * @access  public
     * @param   &util.Date starts
     */
    function setStarts(&$starts) {
      $this->starts= &$starts;
    }

    /**
     * Get Starts
     *
     * @access  public
     * @return  &util.Date
     */
    function &getStarts() {
      return $this->starts;
    }

    /**
     * Set Ends
     *
     * @access  public
     * @param   &util.Date ends
     */
    function setEnds(&$ends) {
      $this->ends= &$ends;
    }

    /**
     * Get Ends
     *
     * @access  public
     * @return  &util.Date
     */
    function &getEnds() {
      return $this->ends;
    }

    /**
     * Set Summary
     *
     * @access  public
     * @param   string summary
     */
    function setSummary($summary) {
      $this->summary= $summary;
    }

    /**
     * Get Summary
     *
     * @access  public
     * @return  string
     */
    function getSummary() {
      return $this->summary;
    }

    /**
     * Set Location
     *
     * @access  public
     * @param   string location
     */
    function setLocation($location) {
      $this->location= $location;
    }

    /**
     * Get Location
     *
     * @access  public
     * @return  string
     */
    function getLocation() {
      return $this->location;
    }

    /**
     * Set Description
     *
     * @access  public
     * @param   string description
     */
    function setDescription($description) {
      $this->description= $description;
    }

    /**
     * Get Description
     *
     * @access  public
     * @return  string
     */
    function getDescription() {
      return $this->description;
    }

    /**
     * Add attendee
     *
     * @access  public
     * @param   mixed[] attendee
     */
    function addAttendee($attendee) {
      $this->attendee[]= $attendee;
    }

    /**
     * Get attendees
     *
     * @access  public
     * @return  mixed[]
     */
    function getAttendees() {
      return $this->attendee;
    }

    /**
     * Set Organizer
     *
     * @access  public
     * @param   string organizer
     */
    function setOrganizer($organizer) {
      $this->organizer= $organizer;
    }

    /**
     * Get Organizer
     *
     * @access  public
     * @return  string
     */
    function getOrganizer() {
      return $this->organizer;
    }
  }
?>
