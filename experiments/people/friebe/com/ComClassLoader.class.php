<?php
/* This class is part of the XP framework's experiments
 *
 * $Id: ActiveXObject.class.php 8321 2006-11-05 15:22:47Z friebe $
 */
 
  /**
   * COM ClassLoader
   *
   * @ext      com
   * @purpose  Class loader
   */
  class ComClassLoader extends ClassLoader {

    /**
     * Retrieve details for a specified class.
     *
     * @access  public
     * @param   string class fully qualified class name
     * @return  array or NULL to indicate no details are available
     */
    function getClassDetails($com) {
      ob_start();
      com_print_typeinfo($com, NULL, FALSE);
      $buffer= ob_get_contents();
      ob_end_clean();
      
      $details= array(array(), array());
      $tokens= token_get_all('<?php '.$buffer.' ?>');
      for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
        switch ($tokens[$i][0]) {
        
          // class IWebBrowser2 { /* GUID={D30C1661-CDAF-11D0-8A3E-00C04FC9E26E} */
          case T_CLASS:
            $details['class']= array(
              DETAIL_COMMENT      => '',
              DETAIL_ANNOTATIONS  => array('com' => array('name' => $tokens[$i+ 2][1]))
            );
            while (T_COMMENT !== $tokens[$i][0] && $i < $s) $i++;
            $details['class'][DETAIL_ANNOTATIONS]['com']['guid']= substr($tokens[$i][1], 9, -5);
            break;

          // /* DISPID=550 */
          // /* VT_HRESULT [25] */
          // /* Controls if the frame is offline (read from cache) */
          // var $Offline;
          case T_VAR:
            $details[0][$tokens[$i+ 2][1]]= array(
              DETAIL_ANNOTATIONS => array()
            );
            break;
            
          // /* DISPID=1610940418 */
          // /* VT_HRESULT [25] */
          // function ExplorerPolicy(
          //         /* VT_BSTR [8] [in] */ $bstrPolicyName,
          //         /* VT_PTR [26] [out] --> VT_VARIANT [12]  */ &$pValue 
          //         )
          // {
          //         /* Return explorer policy value */
          // }
          case T_FUNCTION:
            while (T_STRING !== $tokens[$i][0] && $i < $s) $i++;
            $m= strtolower($tokens[$i][1]);
            $details[1][$m]= array(
              DETAIL_MODIFIERS    => MODIFIER_PUBLIC,
              DETAIL_ARGUMENTS    => array(),
              DETAIL_RETURNS      => 'mixed',   // Unknown
              DETAIL_THROWS       => array(),
              DETAIL_COMMENT      => NULL,
              DETAIL_ANNOTATIONS  => array(),
              DETAIL_NAME         => $tokens[$i][1]
            );
            
            // Handle arguments
            while ('(' !== $tokens[$i][0] && $i < $s) $i++;
            while (')' !== $tokens[$i][0] && $i < $s) {
              if (T_COMMENT === $tokens[$i][0]) {
                sscanf(substr($tokens[$i][1], 3), '%[^ ] [%d] [%[^]]] --> %[^ ] [%d]', $type, $id, $arg, $ptype, $pid);
              }
              if (T_VARIABLE === $tokens[$i][0]) {
                $details[1][$m][DETAIL_ARGUMENTS][]= array(
                  substr($tokens[$i][1], 1),
                  'VT_PTR' == $type ? '&'.$ptype : $type,
                  FALSE,
                  NULL
                );
              }
              $i++;
            }
            
            // Handle comment
            while (T_COMMENT !== $tokens[$i][0] && $i < $s) $i++;
            $details[1][$m][DETAIL_COMMENT]= substr($tokens[$i][1], 3, -3);
            break;
        }
      }
      return $details;
    }
  }
?>
