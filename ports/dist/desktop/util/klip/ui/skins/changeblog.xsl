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
          <td bgcolor="#efd394" background="chrome://ui/skins/image/bg_changeblog.gif"><img hspace="2" vspace="2" src="{normalize-space(/klip/locations/defaultlink)}/image/logo.png" width="22" height="22"/></td>
          <td bgcolor="#efd394" background="chrome://ui/skins/image/bg_changeblog.gif">
            <a href="{normalize-space(/klip/locations/defaultlink)}"><b><xsl:value-of select="/klip/identity/title"/></b></a>:
            <xsl:value-of select="count($contentsource/items/item)"/> most recent entries
          </td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <xsl:for-each select="$contentsource/items/item">
          <tr>
            <td valign="top" width="100%">
              <table border="0" bgcolor="#cccccc" cellspacing="2" cellpadding="1" width="100%">
                <tr>
                  <td width="1%"><img src="chrome://ui/skins/image/blank.gif" width="1" height="1" align="left"/></td>
                  <td>
                    <table border="0" bgcolor="#ffffff" cellpadding="0" cellspacing="0" width="100%">
                      <tr>
                        <td width="20" valign="top">
                          <img src="chrome://ui/skins/image/changeblog_item.png" width="16" height="16" align="left"/>
                          <img src="chrome://ui/skins/image/blank.gif" width="1" height="24"/>
                        </td>
                        <td valign="top">
                          <xsl:apply-templates select="."/>
                          &#160;&#160;
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </xsl:for-each>
      </table>
    </body>
  </html>
  </xsl:template>

</xsl:stylesheet>
