{
/* This file is part of the XP javascript framework
 *
 * $Id$ 
 */

  /**
   * Wrap selection code to provide browser independent selection
   * updating interface
   *
   * @purpose   Wrap selection code
   */
  function SelectionArea() {

    var isOpera= navigator.userAgent.indexOf('Opera') > -1;
    var isIE=    navigator.userAgent.indexOf('MSIE') > 1 && !isOpera;
    var isMoz=   navigator.userAgent.indexOf('Mozilla/5.') == 0 && !isOpera;
    
    if (isMoz) {
      this.browser= 'mozilla';
    } else if (isIE) {
      this.browser= 'ie';
    } else {
      this.browser= 'opera';
    }
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    SelectionArea.prototype.setSelectionRange= function(id, start, end) {
      var element= document.getElementById(id);

      // -1 means end of string
      if (-1 == end) { end= element.value.length; }
      
      switch (this.browser) {
        case 'ie':
          var range= element.createTextRange();
          range.moveStart("character", start);
          range.moveEnd("character", end);
          range.select();
          break;
        
        case 'mozilla':
          element.setSelectionRange(start, end);
          break;
        
        default:
          break;
      }
    }
  }
}
