<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="1.0"
>
  <xsl:output method="xhtml" encoding="iso-8859-1"/>
  
  <xsl:param name="collection"/>
  <xsl:param name="package"/>
  <xsl:param name="mode" select="'showsource'"/>

  <!-- Include main window part -->
  <xsl:include href="xsl-helper.xsl"/>

  <xsl:template name="showsource">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td width="1%" valign="top">
          <img src="/image/nav_example.gif"/>
        </td>
        <td width="50%">
          <xsl:processing-instruction name="php">
            <![CDATA[
              $className= strip_tags ($_REQUEST['f']);
              echo '<b>'.$className.'</b>';
              
              // This "return"-path must be fixed when the classpath moves
              // to the new place
              echo '<a href="/classes/'.$className.'.html"><img src="/image/caret-t.gif" border="0"></a>';
            ]]>
          </xsl:processing-instruction>
        </td>
      </tr>
    <xsl:call-template name="embedded-divider"/>
    </table>
    <xsl:processing-instruction name="php">
      <![CDATA[
/* This class is part of the XP framework
 *
 * $Id$
 */
  
  require ('lang.base.php');
  uses (
    'util.text.PHPSyntaxHighlighter',
    'io.File'
  );
  
  $file= preg_replace ('/^([\/]+)/', '', str_replace ('..', '', $_REQUEST['f']));
  
  $p= &new PHPSyntaxHighlighter (
    new File ($_SERVER['DOCUMENT_ROOT'].'/src/'.$file.'.class.php')
  );
  echo $p->getHighlight();
      
      ]]>
    </xsl:processing-instruction>
  </xsl:template>
</xsl:stylesheet>
