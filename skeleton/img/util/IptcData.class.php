<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Date', 
    'img.ImagingException', 
    'lang.ElementNotFoundException'
  );

  /**
   * Reads the IPTC headers from Photoshop-files, JPEGs or TIFFs
   *
   * <code>
   *   uses('img.util.IptcData', 'io.File');
   *
   *   // Use empty iptc data as default value when no iptc data is found
   *   echo IptcData::fromFile(new File($filename), IptcData::$EMPTY)->toString();
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.img.IptcDataTest
   * @purpose  Utility
   * @see      php://iptcparse
   * @see      http://photothumb.com/IPTCExt/
   * @see      http://www.controlledvocabulary.com/pdf/IPTC_mapped_fields.pdf
   */
  class IptcData extends Object {
    public static
      $EMPTY= NULL;

    public
      $title                         = '',
      $urgency                       = '',
      $category                      = '',
      $keywords                      = array(), 
      $dateCreated                   = NULL, 
      $author                        = '', 
      $authorPosition                = '', 
      $city                          = '', 
      $state                         = '', 
      $country                       = '', 
      $headline                      = '', 
      $credit                        = '', 
      $source                        = '', 
      $copyrightNotice               = '', 
      $caption                       = '', 
      $writer                        = '', 
      $specialInstructions           = '',
      $supplementalCategories        = array(),
      $originalTransmissionReference = '';

    static function __static() {
      self::$EMPTY= new self();
    }

    /**
     * Read from a file
     *
     * @param   io.File file
     * @param   mixed default default void what should be returned in case no data is found
     * @return  img.util.IptcData
     * @throws  lang.FormatException in case malformed meta data is encountered
     * @throws  lang.ElementNotFoundException in case no meta data is available
     * @throws  img.ImagingException in case reading meta data fails
     */
    public static function fromFile(File $file) {
      if (FALSE === getimagesize($file->getURI(), $info)) {
        throw new ImagingException('Cannot read image information from '.$file->getURI());
      }
      if (!isset($info['APP13'])) {
        if (func_num_args() > 1) return func_get_arg(1);
        throw new ElementNotFoundException(
          'Cannot get IPTC information from '.$file->getURI().' (no APP13 marker)'
        );
      }
      if (!($iptc= iptcparse($info['APP13']))) {
        throw new FormatException('Cannot parse IPTC information from '.$file->getURI());
      }
      
      // Parse creation date
      if (3 == sscanf(@$iptc['2#055'][0], '%4d%2d%d', $year, $month, $day)) {
        $created= Date::create($year, $month, $day, 0, 0, 0);
      } else {
        $created= NULL;
      }

      with ($i= new self()); {
        $i->setTitle(@$iptc['2#005'][0]);
        $i->setUrgency(@$iptc['2#010'][0]);
        $i->setCategory(@$iptc['2#015'][0]);
        $i->setSupplementalCategories(@$iptc['2#020']);
        $i->setKeywords(@$iptc['2#025']);
        $i->setSpecialInstructions(@$iptc['2#040'][0]);
        $i->setDateCreated($created);
        $i->setAuthor(@$iptc['2#080'][0]);
        $i->setAuthorPosition(@$iptc['2#085'][0]);
        $i->setCity(@$iptc['2#090'][0]);
        $i->setState(@$iptc['2#095'][0]);
        $i->setCountry(@$iptc['2#101'][0]);
        $i->setOriginalTransmissionReference(@$iptc['2#103'][0]);   
        $i->setHeadline(@$iptc['2#105'][0]);
        $i->setCredit(@$iptc['2#110'][0]);
        $i->setSource(@$iptc['2#115'][0]);
        $i->setCopyrightNotice(@$iptc['2#116'][0]);
        $i->setCaption(@$iptc['2#120'][0]);
        $i->setWriter(@$iptc['2#122'][0]);
      }
      return $i;
    }

    /**
     * Set Title
     *
     * @param   string title
     */
    public function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get Title
     *
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }

    
    /**
     * Set Urgency
     *
     * @param   string urgency
     */
    public function setUrgency($urgency) {
      $this->urgency= $urgency;
    }

    /**
     * Get Urgency
     *
     * @return  string
     */
    public function getUrgency() {
      return $this->urgency;
    }

    
    /**
     * Set Category
     *
     * @param   string category
     */
    public function setCategory($category) {
      $this->category= $category;
    }

    /**
     * Get Category
     *
     * @return  string
     */
    public function getCategory() {
      return $this->category;
    }

    
    /**
     * Set Keywords
     *
     * @param   string[] keywords
     */
    public function setKeywords($keywords) {
      $this->keywords= $keywords;
    }

    /**
     * Get Keywords
     *
     * @return  string[]
     */
    public function getKeywords() {
      return $this->keywords;
    }

    
    /**
     * Set DateCreated
     *
     * @param   util.Date dateCreated default NULL
     */
    public function setDateCreated(Date $dateCreated= NULL) {
      $this->dateCreated= $dateCreated;
    }

    /**
     * Get DateCreated
     *
     * @return  util.Date
     */
    public function getDateCreated() {
      return $this->dateCreated;
    }

    
    /**
     * Set Author
     *
     * @param   string author
     */
    public function setAuthor($author) {
      $this->author= $author;
    }

    /**
     * Get Author
     *
     * @return  string
     */
    public function getAuthor() {
      return $this->author;
    }

    
    /**
     * Set AuthorPosition
     *
     * @param   string authorPosition
     */
    public function setAuthorPosition($authorPosition) {
      $this->authorPosition= $authorPosition;
    }

    /**
     * Get AuthorPosition
     *
     * @return  string
     */
    public function getAuthorPosition() {
      return $this->authorPosition;
    }

    
    /**
     * Set City
     *
     * @param   string city
     */
    public function setCity($city) {
      $this->city= $city;
    }

    /**
     * Get City
     *
     * @return  string
     */
    public function getCity() {
      return $this->city;
    }

    
    /**
     * Set State
     *
     * @param   string state
     */
    public function setState($state) {
      $this->state= $state;
    }

    /**
     * Get State
     *
     * @return  string
     */
    public function getState() {
      return $this->state;
    }

    
    /**
     * Set Country
     *
     * @param   string country
     */
    public function setCountry($country) {
      $this->country= $country;
    }

    /**
     * Get Country
     *
     * @return  string
     */
    public function getCountry() {
      return $this->country;
    }

    
    /**
     * Set Headline
     *
     * @param   string headline
     */
    public function setHeadline($headline) {
      $this->headline= $headline;
    }

    /**
     * Get Headline
     *
     * @return  string
     */
    public function getHeadline() {
      return $this->headline;
    }

    
    /**
     * Set Credit
     *
     * @param   string credit
     */
    public function setCredit($credit) {
      $this->credit= $credit;
    }

    /**
     * Get Credit
     *
     * @return  string
     */
    public function getCredit() {
      return $this->credit;
    }

    
    /**
     * Set Source
     *
     * @param   string source
     */
    public function setSource($source) {
      $this->source= $source;
    }

    /**
     * Get Source
     *
     * @return  string
     */
    public function getSource() {
      return $this->source;
    }

    
    /**
     * Set CopyrightNotice
     *
     * @param   string copyrightNotice
     */
    public function setCopyrightNotice($copyrightNotice) {
      $this->copyrightNotice= $copyrightNotice;
    }

    /**
     * Get CopyrightNotice
     *
     * @return  string
     */
    public function getCopyrightNotice() {
      return $this->copyrightNotice;
    }

    
    /**
     * Set Caption
     *
     * @param   string caption
     */
    public function setCaption($caption) {
      $this->caption= $caption;
    }

    /**
     * Get Caption
     *
     * @return  string
     */
    public function getCaption() {
      return $this->caption;
    }

    
    /**
     * Set Writer
     *
     * @param   string writer
     */
    public function setWriter($writer) {
      $this->writer= $writer;
    }

    /**
     * Get Writer
     *
     * @return  string
     */
    public function getWriter() {
      return $this->writer;
    }

    
    /**
     * Set SupplementalCategories
     *
     * @param   string[] supplementalCategories
     */
    public function setSupplementalCategories($supplementalCategories) {
      $this->supplementalCategories= $supplementalCategories;
    }

    /**
     * Get SupplementalCategories
     *
     * @return  string[]
     */
    public function getSupplementalCategories() {
      return $this->supplementalCategories;
    }

    
    /**
     * Set SpecialInstructions
     *
     * @param   string specialInstructions
     */
    public function setSpecialInstructions($specialInstructions) {
      $this->specialInstructions= $specialInstructions;
    }

    /**
     * Get SpecialInstructions
     *
     * @return  string
     */
    public function getSpecialInstructions() {
      return $this->specialInstructions;
    }

    
    /**
     * Set OriginalTransmissionReference
     *
     * @param   string originalTransmissionReference
     */
    public function setOriginalTransmissionReference($originalTransmissionReference) {
      $this->originalTransmissionReference= $originalTransmissionReference;
    }

    /**
     * Get OriginalTransmissionReference
     *
     * @return  string
     */
    public function getOriginalTransmissionReference() {
      return $this->originalTransmissionReference;
    }

    /**
     * Retrieve a string representation
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "  [title                        ] %s\n".
        "  [urgency                      ] %s\n".
        "  [category                     ] %s\n".
        "  [keywords                     ] %s\n".
        "  [dateCreated                  ] %s\n".
        "  [author                       ] %s\n".
        "  [authorPosition               ] %s\n".
        "  [city                         ] %s\n".
        "  [state                        ] %s\n".
        "  [country                      ] %s\n".
        "  [headline                     ] %s\n".
        "  [credit                       ] %s\n".
        "  [source                       ] %s\n".
        "  [copyrightNotice              ] %s\n".
        "  [caption                      ] %s\n".
        "  [writer                       ] %s\n".
        "  [supplementalCategories       ] %s\n".
        "  [specialInstructions          ] %s\n".
        "  [originalTransmissionReference] %s\n".
        "}",  
        $this->title,
        $this->urgency,
        $this->category,
        xp::stringOf($this->keywords, '  '),
        xp::stringOf($this->dateCreated),
        $this->author,
        $this->authorPosition,
        $this->city,
        $this->state,
        $this->country,
        $this->headline,
        $this->credit,
        $this->source,
        $this->copyrightNotice,
        $this->caption,
        $this->writer,
        xp::stringOf($this->supplementalCategories, '  '),
        $this->specialInstructions,
        $this->originalTransmissionReference
      );
    }
  }
?>
