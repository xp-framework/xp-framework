/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('XmlHttpRequestFactory', 'SelectionArea');
  

  function lookupPerson(q) {

    // Don't make query for too short string
    if (q.length < 1) { return; }
    
    var request= new XmlHttpRequestFactory().create();
    request.open('GET', 'ac.php?q=' + q, true);
    request.onreadystatechange= function() {
      if (request.readyState == 4) {
        try {
          eval(request.responseText);
        } catch(e) {
          alert(e);
        }
      }
    }
    
    request.send(null);
  }
  
  function handleKeyUp(element, event) {
    
    switch (event.keyCode) {
      case 38:       // up arrow  
      case 40:       // down arrow
      case 37:       // left arrow
      case 39:       // right arrow
      case 33:       // page up  
      case 34:       // page down  
      case 36:       // home  
      case 35:       // end                  
      case 13:       // enter  
      case 9:        // tab  
      case 27:       // esc  
      case 16:       // shift  
      case 17:       // ctrl  
      case 18:       // alt  
      case 20:       // caps lock
      case 8:        // backspace  
      case 46:       // delete
        return true;
        break;
    
      default:
        lookupPerson(element.value);
        return true;
    }
  }
  
  function selectUser(selectbox) {
    var n,p;
    n= selectbox.options[selectbox.selectedIndex].text;
    p= selectbox.options[selectbox.selectedIndex].value;
    
    document.getElementById('personinput').value= n;
    document.getElementById('personid').value= p;
  }
