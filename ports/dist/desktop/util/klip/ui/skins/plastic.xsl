<!--
 ! Plastic skin (light blue)
 !
 ! $Id$
 !-->
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
>
  <xsl:output method="html" encoding="iso-8859-1"/>

  <xsl:template match="item[@read = '1']">
    <a href="{@link}"><xsl:value-of select="."/></a>
  </xsl:template>

  <xsl:template match="item[@read = '0']">
    <b><a href="{@link}"><xsl:value-of select="."/></a></b>
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
       bgcolor="#839de6" 
       text="#293462" 
       link="#293462"
      >
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="2" bgcolor="#8391d5" background="chrome://ui/skins/image/bg_plastic.gif"><img src="chrome://ui/skins/image/blank.gif" width="1" height="5"/></td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td valign="top">
            <a href="{normalize-space(/klip/locations/defaultlink)}">
              <img border="0" width="16" height="16" src="{normalize-space(/klip/locations/icon)}"/>
            </a>
            <br/><br/>
            <xsl:value-of select="$contentsource/items/@count"/>
          </td>
          <td valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="1">
              <xsl:for-each select="$contentsource/items/item">
                <tr>
                  <td width="1%" valign="top">
                    <img src="chrome://ui/skins/image/arrow.gif"/>
                  </td>
                  <td valign="top">
                    <xsl:apply-templates select="."/>
                  </td>
                </tr>
              </xsl:for-each>
            </table>
          </td>
        </tr>
      </table>
    </body>
  </html>
  </xsl:template>

</xsl:stylesheet>
