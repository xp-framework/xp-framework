<?php
/* Forms & Behandlung
 *  
 * $Id$
 */

  // Input Typen
  define("IT_STRING",       0);
  define("IT_TEXT",         1);
  define("IT_SELECT",       2);
  define("IT_TEXTAREA",     4);
  define("IT_CODEAREA",     5);
  define("IT_HIDDEN",       8);
  define("IT_PLAIN",        16);
  define("IT_CHOICE",       32);
  define("IT_RADIO",        64);
  define("IT_DISABLED",    128);
  define("IT_FILE",        256);
  define("IT_XMP",         512);
  define("IT_DIVIDER",    1024);
  define("IT_CHECK",      2048);
  
  $GLOBALS["input_type_formvis"]= array(
    IT_STRING	=> '<input type="text" name="%name%" value="%value%" size="40"/>',
    IT_TEXT	=> '<input type="text" name="%name%" value="%value%"/>',
    IT_SELECT	=> '<select name="%name%">%options%</select>',
    IT_TEXTAREA => '<pre><textarea name="%name%" rows="%rows%" cols="%cols%" wrap="virtual">%value%</textarea></pre>',
    IT_CODEAREA => '<pre><textarea name="%name%" rows="%rows%" cols="%cols%" wrap="off">%value%</textarea></pre>',
    IT_HIDDEN	=> '<input type="hidden" name="%name%" value="%value%"/>',
    IT_CHOICE	=> '<input type="radio" name="%name%" value="1" %true%/> Ja <input type="radio" name="%name%" value="0" %false%/> Nein',
    IT_RADIO	=> '%options%',
    IT_PLAIN	=> '%text%',
    IT_DISABLED => '%display%&nbsp;<input type="hidden" name="%name%" value="%value%"/>',
    IT_FILE	=> '<input type="file" size="30" accept="%accept%" value="%value%" name="%name%"/>',
    IT_XMP	=> '<xmp>%value%</xmp>',
    IT_DIVIDER	=> '&amp;nbsp;',
    IT_CHECK	=> '%options%'
  );
  
  // Für IT_FILE:
  define('MAX_FILE_SIZE', 1048576);
  
  // Befehle
  define('WIZ_BACK',	0x0000);
  define('WIZ_FWRD',	0x0001);
  
  class Wizard extends Object {
    var 
      $name, $caption, $action, $form, $identifier, $submit_button, $step;
    
    var 
      $on_setup= "wiz_setup",
      $on_accept= "wiz_accept",
      $on_error= "default_error",
      $ERR_MSG= array(),
      $WARN_MSG= array(),
      $Error= 0,
      $Debug= 0,
      $session;
    
    function Wizard($params= NULL) {
      global $g_project;
      
      $this->name= 'wizzard';
      $this->session= &$g_project->session;
      $this->action= basename($GLOBALS['PHP_SELF']);
      $this->step= 0;
      $this->form= array();
      $this->submit_buttons= array(0 => "ok");
      if($params!= NULL) foreach($params as $key=> $val) $this->$key= $val;
    }
    
    function logline_text($key, $var) {
      if($this->Debug && $GLOBALS["stage_server"]) logline_text("Wizzard::$key", &$var);
    }
    
    function formvis($name, $type, $element_data) {
      global $input_type_formvis;
      $element_data["name"]= $name;
      
      // Muss noch was geparsed werden? z.B. <Select[...]>
      switch($type) {
        case IT_SELECT:
	  $options= $element_data["options"];
	  $element_data["options"]= "";
	  foreach($options as $value=> $text) $element_data["options"].= "<option value=\"$value\" ".(($element_data["value"]== $value)? 'selected="selected"': "").">$text</option>";
	  break;
        case IT_CHECK:
	  $options= $element_data["options"];
          
          // Spalten?
          $element_data["options"]= "<table border=\"0\" width=\"100%\"><tr>";
	  if(!isset($element_data["colums"])) $element_data["options"]= "";
          
          $w= 0;
          $element_data["colums"]--;
          
          foreach($options as $value=> $text) {
            if(isset($element_data["colums"])) $element_data["options"].= "<td>";
            $element_data["options"].= "<input type=\"checkbox\" name=\"$element_data[name],IT_CHECK,$value\" ".((in_array($value, $element_data["value"]))? 'checked="checked"': "")."/> $text";
            
            // Umbruch [ohne Spalten]
            if(!isset($element_data["colums"])) {
              $element_data["options"].= "<br>";
              continue;
            }
            
            // Umbruch [mit Spalten]
            $element_data["options"].= "</td>";
            if($w> 0 and ($w % $element_data["colums"]== 0)) { 
              $w= -1;
              $element_data["options"].= "</tr><tr>"; 
            }
            $w++;
          }
          
          // Spalten?
   	  if(isset($element_data["colums"])) {
            $element_data["options"]= preg_replace('=</tr>$=', '', $element_data["options"]);
            $element_data["options"].= "</tr></table>";
          }
          break;
        case IT_CHOICE:
          $element_data["true"]= ($element_data["selected"]? 'checked="checked"': "");
          $element_data["false"]= ($element_data["selected"]? "": 'checked="checked"');
          break;
	case IT_RADIO:
	  $options= $element_data["options"];
	  $element_data["options"]= "";
	  foreach($options as $value=> $text) $element_data["options"].= "<input type=\"radio\" name=\"$element_data[name]\" value=\"$value\" ".(($element_data["value"]== $value)? 'checked="checked"': "")."/> $text";
	  break;
        case IT_HIDDEN:
        case IT_TEXT:
        case IT_STRING:
        case IT_CODEAREA:
        case IT_TEXTAREA:
          $element_data["value"]= htmlspecialchars($element_data["value"]); //htmlentities($element_data["value"]);
          break;
        case IT_DISABLED:
          $element_data["display"]= "<span class=\"disabled\">".substr($element_data["value"], 0, 80)."</span>";
      }
      return preg_replace('/%([a-z]*)%/Ue', '$element_data["\\1"]', $input_type_formvis[$type]);
    }
       
    function render() {
      if(sizeof($this->form)== 0) {
        $this->logline_text("error", "no form specified");
	return "";
      }
      $return= 
        '<wiz action="'.$this->action.'" enctype="multipart/form-data" method="post" identifier="'.$this->identifier.'" step="'.$this->step.'">'."\n".
        '  <caption>'.$this->caption.'</caption>'."\n";
        
      $formvis= "";
      foreach($this->form[$this->step] as $name=> $element) {
        if($element[1]== IT_HIDDEN) {
	  $return.= "  ".$this->formvis($name, IT_HIDDEN, $element[2])."\n";
          continue;
        }
        if($element[1]== IT_DIVIDER) $tag= "divider"; else $tag= "field";
        if(!isset($element[2])) $element[2]= FALSE;
	$formvis.= '    '.
          '<field '.
            (isset($this->errors[$name])? 'error="'.$this->errors[$name].'"': "").
            'label="'.$element[0].'"'.
          '>'.$this->formvis($name, $element[1], $element[2]).'</field>'."\n";
      }
      $return.= "  <fields>\n$formvis  </fields>\n";
      $return.= "  <buttons>\n";

      // Mehrere Submit-Buttons?
      foreach($this->submit_buttons as $id) {
        $return.= "    <button type=\"$id\" name=\"submit_$id\"/>\n";
      }
      
      // Abbrechen-Button?
      if(isset($this->cancel)) $return.= "    <button type=\"cancel\" href=\"$this->cancel\"/>\n";
      $return.= "  </buttons>\n";
      $return.= "</wiz>";
      return $return;
    }
    
    function reset() {
      $this->logline_text("reset", "var_unreg && sync");
      $this->session->var_unreg("wiz_".$this->name.".form");
      $this->session->var_unreg("wiz_".$this->name.".identifier");
      $this->session->sync();
    }

    function val($element, $step= -1) {
      if($step< 0) $step= $this->step;
      return $this->form[$step][$element][2]["value"];
    }
    
    function is_defined($element, $step= -1) {
      if($step< 0) $step= $this->step;
      return isset($this->form[$step][$element][2]["value"])? 1: 0;
    }
    
    function set_val($element, $value) {
      $this->form[$this->step][$element][2]["value"]= $value;
    }
    
    function unset_val($element) {
      unset($this->form[$this->step][$element][2]);
    }
    
    // Safe-Value für SQL-Statements
    function safe_val($element, $step= -1) {
      if($step< 0) $step= $this->step;
      return str_replace("'", "''", $this->form[$step][$element][2]["value"]);
    }
    
    function cmd($command) {
      switch($command) {
        case WIZ_BACK: if($this->step> 0) return sprintf('%s?__wizcmd=%s/%s', $this->action, $this->identifier, ($this->step- 1)); break;
        case WIZ_FWRD: if(isset($this->form[$this->step+ 1]) && sizeof($this->form[$this->step+ 1])> 0) return sprintf('%s?__wizcmd=%s/%s', $this->action, $this->identifier, ($this->step+ 1)); break;
      }
      return 0;
    }
    
    function syntaxcheck($key, $sc_regex= 0) {
      global $syntaxcheck_error, $g_image_root;
      
      // Kein Syntaxcheck=> OK :)
      if(!isset($this->form[$this->step][$key][3])) return 1;
      
      if(!$sc_regex) $sc_regex= $this->form[$this->step][$key][3];
      $value= $this->form[$this->step][$key][2]["value"];
      $this->logline_text("syntaxcheck", "{var} $key {value} $value {sc_regex} $sc_regex"); 
      if(!preg_match($sc_regex, $value)) {
        if(isset($syntaxcheck_error[$sc_regex])) {
          $this->ERR_MSG[]= sprintf($syntaxcheck_error[$sc_regex], $this->form[$this->step][$key][0]);
	}
	$this->errors[$key]= '<img width="11" height="11" alt="-&gt;" src="'.$g_image_root.'/icn_arrow.png"/>';
	return 0;
      }
      $this->errors[$key]= '';
      return 1;
    }
     
    // Return: 1 => Wizzard abgeschlossen, 0 => Fehler oder nächster Schritt
    function execute() {
      global $HTTP_POST_VARS, $REQUEST_URI, $REQUEST_METHOD;

      // Setup
      $setup_func= $this->on_setup;
      $accept_func= $this->on_accept;
      $error_func= $this->on_error;
      
      // Bei POST Identifier gegenchecken
      if(isset($HTTP_POST_VARS["doit"])) {
        $this->identifier= $HTTP_POST_VARS["doit"]; 
        $this->step= (int)$GLOBALS["step"]; 
      } else {
        $this->identifier= @$this->session->var_get("wiz_".$this->name.".identifier");
        if(isset($GLOBALS["__wizcmd"]) && preg_match('=^([a-z0-9]*)/([0-9]+)$=i', $GLOBALS["__wizcmd"], $wizcmd)) {
          $this->step= $wizcmd[2];
          $this->logline_text("wizcmd", "\n$wizcmd[1]\n$this->identifier");
          $this->identifier= md5($REQUEST_URI);
        }
      }
      
      $this->form= @$this->session->var_get("wiz_".$this->name.".form");
      $this->logline_text("do_wizzard", "
        {identifier} $this->identifier
        {checksum} ".md5($REQUEST_URI)."
        {form.read_from} wiz_".$this->name.".form
        {form.size} ".sizeof($this->form)."
        {step} $this->step
        {setup_func} $setup_func
        {accept_func} $accept_func
        {error_func} $error_func
      ");
     
      
      // Reload?
      if(isset($HTTP_POST_VARS["doit"]) && !isset($this->form)) {
        $this->ERR_MSG[]= "Sie versuchen, den gleichen Vorgang erneut auszuführen!<br/><br/>".(isset($this->cancel)? $gui->button("cancel", $this->cancel): "");
        $error_func(&$this->form[$this->step]);
        return 0;
      }
            
      // Formular erstellen, wenn:
      // * Formular nicht in der Session existiert (aber nur bei GET)
      // * MD5-Hash der Request-URI nicht stimmt, auch nur bei GET
      // * Formular zu klein
      if(
        ($REQUEST_METHOD== "GET" && !isset($this->form)) ||
        ($REQUEST_METHOD== "GET" && md5($REQUEST_URI)!= $this->identifier) ||
        (@sizeof($this->form[$this->step])== 0)
        ) {
        $this->logline_text("reset_form", "{call_user_func} $setup_func");
        $setup_func(&$this->form[$this->step]);
        $this->identifier= md5($REQUEST_URI);
	$this->session->var_reg("wiz_".$this->name.".form", $this->form);
        $this->session->var_reg("wiz_".$this->name.".identifier", $this->identifier);
	$this->session->sync();
      }
      
      // Ausführen?
      if(isset($HTTP_POST_VARS["doit"])) {
        $this->ERR_MSG= array();
	
	// POST-Werte übernehmen
	$this->logline_text("POST", $HTTP_POST_VARS);
	foreach($HTTP_POST_VARS as $key=> $val) {
          // Welchen Submit-Button?
          if(preg_match("/^submit_([a-z]*)_x$/", $key, $regs)) { 
            $this->submit_button= $regs[1]; 
            continue;
          }
          
          // Checkboxen-Arrays: tarif_id,IT_CHECK,1034 = on
          if(preg_match("/^([^,]*),IT_CHECK,(.*)$/", $key, $regs)) {
            $this->logline_text($val, $this->form[$this->step][$regs[1]][2]["value"]);
            $this->form[$this->step][$regs[1]][2]["value"][$regs[2]]= $regs[2];
            continue;
          }
          
	  if(isset($this->form[$this->step][$key])) {
	    $this->form[$this->step][$key][2]["value"]= stripslashes($val);
	    
	    // Syntaxcheck
	    $this->syntaxcheck($key);
	  }
	}

	// Wizzard-Accept OK?
        $result= 0;
	if(sizeof($this->ERR_MSG)== 0) $result= $accept_func(&$this->form[$this->step]);
        $this->logline_text("wiz_accept", "{accept_func_return} $result {sizeof(ERR_MSG)} ".sizeof($this->ERR_MSG));
	if((sizeof($this->ERR_MSG)== 0)) {
          switch($result) {
            case 1: 
              // 1 => alles OK, also Wizzard killen und 1 zurückgeben
              $this->reset();
              return 1;
            case 2: 
              // 2 => es geht weiter mit dem nächsten Schritt
              $this->step++;
            case 3:
              // 4 => es geht weiter mit dem gleichen Schritt
              $this->logline_text("call_user_func", "{step} $this->step");
              $this->form[$this->step]= array();
              $setup_func(&$this->form[$this->step]);

	      $this->session->var_reg("wiz_".$this->name.".form", $this->form);
              $this->session->sync();
              
              // Formular anzeigen
              echo $this->render();
              return 0;
            default: {}
          }
	}
	
	// Sonst wohl nicht!
        $error_func(&$this->form[$this->step]);
      }

      // Formular anzeigen
      echo $this->render();
      return 0;
    }
    
    function destroy() {
    
    }
  } // end::class(GenericWizzard)
  
  // Standard-Methode zur Fehlerbehandlung: Eine GUIBox mit den "gesammelten" Fehlermeldungen anzeigen
  function default_error(&$form) {
    global $gui, $wiz;
    if(sizeof($wiz->WARN_MSG)> 0) echo $gui->box($wiz->WARN_MSG, "warn"), "<br/>";
    if(sizeof($wiz->ERR_MSG)> 0) echo $gui->box($wiz->ERR_MSG, "error"), "<br/>";
  }
  
  // Wizzard aus der Session nehmen, falls nötig
  function kill_wizzard(&$session, $name) {
    $wizzard_exists= @$session->var_get("$name.form");
    if(!isset($wizzard_exists)) return 0;
    
    logline_text("kill_wizzard", "{name} $name");
    $session->var_unreg("wiz_$name.form");
    $session->var_unreg("wiz_$name.identifier");
    $session->sync();
    return 1;
  }
?>
