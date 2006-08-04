<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.parser.DateParser');

  /**
   * Represents a htdig search entry
   *
   * @purpose  Wrap search entry
   */
  class HtdigEntry extends Object {
    public
      $current=          0,
      $docId=            0,
      $stars=            0,
      $score=            0,
      $url=              '',
      $title=            '',
      $excerpt=          '',
      $metadescription=  '',
      $modified=         NULL,
      $size=             0,
      $hopcount=         0,
      $percent=          0;

    /**
     * Set Current
     *
     * @access  public
     * @param   int current
     */
    public function setCurrent($current) {
      $this->current= (int)$current;
    }

    /**
     * Get Current
     *
     * @access  public
     * @return  int
     */
    public function getCurrent() {
      return $this->current;
    }

    /**
     * Set DocId
     *
     * @access  public
     * @param   string docId
     */
    public function setDocId($docId) {
      $this->docId= (int)$docId;
    }

    /**
     * Get DocId
     *
     * @access  public
     * @return  string
     */
    public function getDocId() {
      return $this->docId;
    }

    /**
     * Set Stars
     *
     * @access  public
     * @param   int stars
     */
    public function setStars($stars) {
      $this->stars= (int)$stars;
    }

    /**
     * Alias for the setStars function.
     *
     * (Needed because htdigs result field name is nstars)
     *
     * @access  public
     * @param   int stars
     */
    public function setNstars($stars) {
      $this->setStars($stars);
    }

    /**
     * Get Stars
     *
     * @access  public
     * @return  int
     */
    public function getStars() {
      return $this->stars;
    }

    /**
     * Set Score
     *
     * @access  public
     * @param   int score
     */
    public function setScore($score) {
      $this->score= (int)$score;
    }

    /**
     * Get Score
     *
     * @access  public
     * @return  int
     */
    public function getScore() {
      return $this->score;
    }

    /**
     * Set Url
     *
     * @access  public
     * @param   string url
     */
    public function setUrl($url) {
      $this->url= urldecode($url);
    }

    /**
     * Get Url
     *
     * @access  public
     * @return  string
     */
    public function getUrl() {
      return $this->url;
    }

    /**
     * Set Title
     *
     * @access  public
     * @param   string title
     */
    public function setTitle($title) {
      $this->title= urldecode($title);
    }

    /**
     * Get Title
     *
     * @access  public
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }

    /**
     * Set Excerpt
     *
     * @access  public
     * @param   string excerpt
     */
    public function setExcerpt($excerpt) {
      $this->excerpt= urldecode($excerpt);
    }

    /**
     * Get Excerpt
     *
     * @access  public
     * @return  string
     */
    public function getExcerpt() {
      return $this->excerpt;
    }

    /**
     * Set Metadescription
     *
     * @access  public
     * @param   string metadescription
     */
    public function setMetadescription($metadesc) {
      $this->metadescription= urldecode($metadesc);
    }

    /**
     * Get Metadescription
     *
     * @access  public
     * @return  string
     */
    public function getMetadescription() {
      return $this->metadescription;
    }

    /**
     * Set Modified
     *
     * @access  public
     * @param   &lang.Object modified
     */
    public function setModified(&$modified) {
      if (is('util.Date', $modified)) {
        $this->modified= &$modified;
        return;
      }
      
      try {
        $d= &DateParser::parse(urldecode($modified));
      } catch (FormatException $e) {
      
        // Date could not be parsed, so default to now.
        $this->modified= &Date::now();
      }
      
      $this->modified= &$d;
    }

    /**
     * Get Modified
     *
     * @access  public
     * @return  &util.Date
     */
    public function &getModified() {
      return $this->modified;
    }

    /**
     * Set Size
     *
     * @access  public
     * @param   int size
     */
    public function setSize($size) {
      $this->size= (int)$size;
    }

    /**
     * Get Size
     *
     * @access  public
     * @return  int
     */
    public function getSize() {
      return $this->size;
    }

    /**
     * Set Hopcount
     *
     * @access  public
     * @param   int hopcount
     */
    public function setHopcount($hopcount) {
      $this->hopcount= (int)$hopcount;
    }

    /**
     * Get Hopcount
     *
     * @access  public
     * @return  int
     */
    public function getHopcount() {
      return $this->hopcount;
    }

    /**
     * Set Percent
     *
     * @access  public
     * @param   int percent
     */
    public function setPercent($percent) {
      $this->percent= (int)$percent;
    }

    /**
     * Get Percent
     *
     * @access  public
     * @return  int
     */
    public function getPercent() {
      return $this->percent;
    }
    
    /**
     * Create a HtdigEntry from an array
     *
     * @model   static
     * @access  public
     * @param   &mixed array
     * @return  &org.htdig.HtdigResult
     * @throws  lang.IllegalArgumentException when array is malformed
     */
    public static function &fromArray(&$arr) {
      $entry= new HtdigEntry();
      
      foreach (array_keys($arr) as $key) {
        if (!method_exists($entry, 'set'.$key))
          throw (new IllegalArgumentException('The given array is malformed. Its key '.$key.' is not associated with a function.'));
          
        call_user_func(array(&$entry, 'set'.$key), $arr[$key]);
      }
      
      return $entry;
    }
    
    /**
     * Returns the string representation of this object.
     *
     * @access  public
     * @return  string 
     */
    public function toString() {
      
      // Retrieve object variables and figure out the maximum length 
      // of a key which will be used for the key "column". The minimum
      // width of this column is 20 characters.
      $vars= get_object_vars($this);
      $max= 20;
      foreach (array_keys($vars) as $key) {
        $max= max($max, strlen($key));
      }
      $fmt= '  [%-'.$max.'s] %s';
      
      // Build string representation.
      $s= $this->getClassName().'@('.$this->hashCode()."){\n";
      foreach (array_keys($vars) as $key) {
        if ('__id' == $key) continue;

        $s.= sprintf($fmt, $key, ->is('Generic', $key) 
          ? $this->$key->toString()
          : var_export($this->$key, 1)
        )."\n";
      }
      return $s.'}';
    }
  }
?>
