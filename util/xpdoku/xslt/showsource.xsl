<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="1.0"
>
  <xsl:output method="xhtml" encoding="iso-8859-1"/>
  
  <xsl:param name="collection"/>
  <xsl:param name="package"/>
  <xsl:param name="mode" select="'showsource'"/>

  <xsl:template name="navigation">
    <!-- Nothing yet -->
  </xsl:template>

  <!-- Include main window part -->
  <xsl:include href="xsl-helper.xsl"/>

  <xsl:template name="showsource">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td width="50%">
          <b>API Doc: Source of
            <xsl:processing-instruction name="php">
              <![CDATA[
                $className= strip_tags ($_REQUEST['f']);
                echo $className;

                // This "return"-path must be fixed when the classpath moves
                // to the new place
                echo '<a href="/apidoc/classes/'.$className.'.html"><img src="/image/caret-t.gif" border="0"></a>';
              ]]>
            </xsl:processing-instruction>
          </b>
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
  
  try(); {
    $p= &new PHPSyntaxHighlighter (
      new File ($_SERVER['DOCUMENT_ROOT'].'/src/'.$file.'.class.php')
    );
    $highlight= $p->getHighlight();
  } if (catch('Exception', $e)) {
    ]]>
    </xsl:processing-instruction>
    <xsl:call-template name="frame">
      <xsl:with-param name="color" select="'#990000'"/>
      <xsl:with-param name="content">
        <br/>
        <div align="center">
          <b style="font-Weight: bold; Color: #990000">
            The file you have requested does not exist.
          </b>
        </div>
        <br/>
      </xsl:with-param>
    </xsl:call-template>
    <xsl:processing-instruction name="php">
      <![CDATA[
    $highlight= '';
  }
  echo $highlight;
      
      ]]>
    </xsl:processing-instruction>
  </xsl:template>
</xsl:stylesheet>
