<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
>
  <xsl:output method="html" encoding="iso-8859-1"/>

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
       bgcolor="#525083" 
       text="#dfdff6" 
       link="#dfdff6"
      >
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="2" bgcolor="#7371b4"><img src="chrome://ui/header.gif"/></td>
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
            <xsl:for-each select="$contentsource/items/item">
              <a href="{@link}"><xsl:value-of select="."/></a><br/>
            </xsl:for-each>
          </td>
        </tr>
      </table>
    </body>
  </html>
  </xsl:template>

</xsl:stylesheet>
