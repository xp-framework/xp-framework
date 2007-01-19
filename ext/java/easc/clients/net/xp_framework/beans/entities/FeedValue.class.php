<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$
 */

  /**
   * Feed value value object
   *
   * @purpose  Demo class  
   */
  class FeedValue extends Object {
    public
      $feed_id,
      $title,
      $url,
      $bz_id,
      $lastchange;
    
    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "%s(pk= %s){\n".
        "  [feed_id]    %s\n".
        "  [title]      %s\n".
        "  [url]        %s\n".
        "  [bz_id]      %s\n".
        "  [lastchange] %s\n".
        "}",
        $this->getClassName(),
        xp::stringOf($this->pk),
        xp::stringOf($this->feed_id),
        xp::stringOf($this->title),
        xp::stringOf($this->url),
        xp::stringOf($this->bz_id),
        xp::stringOf($this->lastchange)
      );
    }
  }  
?>
