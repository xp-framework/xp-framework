<!--
 ! Bugzilla skin (inspired by mozilla.org)
 !
 ! $Id$
 !-->
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
>
  <xsl:output method="html" encoding="iso-8859-1"/>

  <xsl:template name="caption">
    <xsl:param name="string" select="''"/>
    
    <xsl:choose>
      <xsl:when test="contains($string, '[NEW]') or contains($string, '[REOPENED]')">
        <b><font color="#990000"><xsl:value-of select="$string"/></font></b>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$string"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template match="item">
    <a href="{@link}">
      <xsl:call-template name="caption">
        <xsl:with-param name="string" select="."/>
      </xsl:call-template>
    </a>
  </xsl:template>

  <xsl:template match="/">
    <xsl:param name="contentsource" select="document(concat(
      'rdf:', 
      normalize-space(/klip/locations/contentsource))
    )"/>

    <html>
      <head>
        <title><xsl:value-of select="/klip/identity/title"/></title>
      </head>
      <body 
       topmargin="0" 
       leftmargin="0" 
       marginheight="0" 
       marginwidth="0" 
       bgcolor="#ffffff" 
       text="#000000" 
       link="#524da5"
      >
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td bgcolor="#efd394" background="chrome://ui/skins/image/bg_bugzilla.gif"><img hspace="2" vspace="2" src="chrome://ui/skins/image/bugzilla.gif" width="31" height="31"/></td>
          <td bgcolor="#efd394" background="chrome://ui/skins/image/bg_bugzilla.gif">
            <a href="{normalize-space(/klip/locations/defaultlink)}"><b><xsl:value-of select="/klip/identity/title"/></b></a>:
            <xsl:value-of select="$contentsource/items/@count"/> bug(s) found
          </td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <xsl:for-each select="$contentsource/items/item">
          <tr>
            <td width="1%" valign="top" bgcolor="#efebe7">
              <img src="chrome://ui/skins/image/bugzilla_arrow.gif" width="10" height="15"/>
            </td>
            <td valign="top">
              <xsl:apply-templates select="."/>
            </td>
          </tr>
          <tr>
            <td width="1%" valign="top" bgcolor="#efebe7"><img src="chrome://ui/skins/image/blank.gif" width="1" height="1"/></td>
            <td background="chrome://ui/skins/image/bugzilla_divider_bg.gif"><img src="chrome://ui/skins/image/blank.gif" width="1" height="1"/></td>
          </tr>
        </xsl:for-each>
      </table>
    </body>
  </html>
  </xsl:template>

</xsl:stylesheet>
