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
    new File ($_SERVER['DOCUMENT_ROOT'].'/src/'.$_REQUEST['f'].'.class.php')
  );
  echo $p->getHighlight();
      
      ]]>
    </xsl:processing-instruction>
  </xsl:template>
</xsl:stylesheet>
