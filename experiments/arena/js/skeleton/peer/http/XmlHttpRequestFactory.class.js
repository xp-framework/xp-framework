{
/* This file is part of the XP javascript frameworks
 *
 * $Id$ 
 */

  /**
   * Create XmlHttpRequest object on multiple javascript implementations.
   *
   * @purpose   Unified handling
   */
  {

    /**
     * Factory class to create XMLHttpRequest object
     *
     * @model   static
     * @see     http://jpspan.sourceforge.net/wiki/doku.php?id=javascript:xmlhttprequest#tutorials
     */
    function XmlHttpRequestFactory() { }

    /**
     * Create a suitable XMLHttpRequest object
     *
     * @model   static
     * @access  public
     * @return  &XMLHttpRequest
     */
    XmlHttpRequestFactory.prototype.create= function() {
      var request= null;
      var success= false;
      var MSXML_XMLHTTP_PROGIDS= new Array(
        'MSXML2.XMLHTTP.5.0',
        'MSXML2.XMLHTTP.4.0',
        'MSXML2.XMLHTTP.3.0',
        'MSXML2.XMLHTTP',
        'Microsoft.XMLHTTP'
      );

      for (var i=0;i < MSXML_XMLHTTP_PROGIDS.length && !success; i++) {
        try {
          request= new ActiveXObject(MSXML_XMLHTTP_PROGIDS[i]);
          success = true;
        } catch (e) {
          request= null; 
        }
      }

      if (!request && typeof XMLHttpRequest != 'undefined') {
        request= new XMLHttpRequest();
      }

      return request;
    }
  }
}
