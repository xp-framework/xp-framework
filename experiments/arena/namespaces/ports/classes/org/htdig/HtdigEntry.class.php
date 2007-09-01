<?php
/* This class is part of the XP framework
 *
 * $Id: HtdigEntry.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace org::htdig;

  ::uses('text.parser.DateParser');

  /**
   * Represents a htdig search entry
   *
   * @purpose  Wrap search entry
   */
  class HtdigEntry extends lang::Object {
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
     * @param   int current
     */
    public function setCurrent($current) {
      $this->current= (int)$current;
    }

    /**
     * Get Current
     *
     * @return  int
     */
    public function getCurrent() {
      return $this->current;
    }

    /**
     * Set DocId
     *
     * @param   string docId
     */
    public function setDocId($docId) {
      $this->docId= (int)$docId;
    }

    /**
     * Get DocId
     *
     * @return  string
     */
    public function getDocId() {
      return $this->docId;
    }

    /**
     * Set Stars
     *
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
     * @param   int stars
     */
    public function setNstars($stars) {
      $this->setStars($stars);
    }

    /**
     * Get Stars
     *
     * @return  int
     */
    public function getStars() {
      return $this->stars;
    }

    /**
     * Set Score
     *
     * @param   int score
     */
    public function setScore($score) {
      $this->score= (int)$score;
    }

    /**
     * Get Score
     *
     * @return  int
     */
    public function getScore() {
      return $this->score;
    }

    /**
     * Set Url
     *
     * @param   string url
     */
    public function setUrl($url) {
      $this->url= urldecode($url);
    }

    /**
     * Get Url
     *
     * @return  string
     */
    public function getUrl() {
      return $this->url;
    }

    /**
     * Set Title
     *
     * @param   string title
     */
    public function setTitle($title) {
      $this->title= urldecode($title);
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
     * Set Excerpt
     *
     * @param   string excerpt
     */
    public function setExcerpt($excerpt) {
      $this->excerpt= urldecode($excerpt);
    }

    /**
     * Get Excerpt
     *
     * @return  string
     */
    public function getExcerpt() {
      return $this->excerpt;
    }

    /**
     * Set Metadescription
     *
     * @param   string metadescription
     */
    public function setMetadescription($metadesc) {
      $this->metadescription= urldecode($metadesc);
    }

    /**
     * Get Metadescription
     *
     * @return  string
     */
    public function getMetadescription() {
      return $this->metadescription;
    }

    /**
     * Set Modified
     *
     * @param   &lang.Object modified
     */
    public function setModified($modified) {
      if (is('util.Date', $modified)) {
        $this->modified= $modified;
        return;
      }
      
      try {
        $d= text::parser::DateParser::parse(urldecode($modified));
      } catch (lang::FormatException $e) {
      
        // Date could not be parsed, so default to now.
        $this->modified= util::Date::now();
      }
      
      $this->modified= $d;
    }

    /**
     * Get Modified
     *
     * @return  &util.Date
     */
    public function getModified() {
      return $this->modified;
    }

    /**
     * Set Size
     *
     * @param   int size
     */
    public function setSize($size) {
      $this->size= (int)$size;
    }

    /**
     * Get Size
     *
     * @return  int
     */
    public function getSize() {
      return $this->size;
    }

    /**
     * Set Hopcount
     *
     * @param   int hopcount
     */
    public function setHopcount($hopcount) {
      $this->hopcount= (int)$hopcount;
    }

    /**
     * Get Hopcount
     *
     * @return  int
     */
    public function getHopcount() {
      return $this->hopcount;
    }

    /**
     * Set Percent
     *
     * @param   int percent
     */
    public function setPercent($percent) {
      $this->percent= (int)$percent;
    }

    /**
     * Get Percent
     *
     * @return  int
     */
    public function getPercent() {
      return $this->percent;
    }
    
    /**
     * Create a HtdigEntry from an array
     *
     * @param   &mixed array
     * @return  &org.htdig.HtdigResult
     * @throws  lang.IllegalArgumentException when array is malformed
     */
    public static function fromArray($arr) {
      $entry= new ();
      
      foreach (array_keys($arr) as $key) {
        if (!method_exists($entry, 'set'.$key))
          throw (new lang::IllegalArgumentException('The given array is malformed. Its key '.$key.' is not associated with a function.'));
          
        call_user_func(array($entry, 'set'.$key), $arr[$key]);
      }
      
      return $entry;
    }
    
    /**
     * Returns the string representation of this object.
     *
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

        $s.= sprintf($fmt, $key, is('Generic', $this->$key) 
          ? $this->$key->toString()
          : var_export($this->$key, 1)
        )."\n";
      }
      return $s.'}';
    }
  }
?>
