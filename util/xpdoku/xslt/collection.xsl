<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="1.0"
>

  <xsl:param name="collection"/>
  <xsl:param name="package" select="''"/>
  <xsl:param name="mode" select="'collection'"/>

  <!-- Include main window part -->
  <xsl:include href="xsl-helper.xsl"/>  

  <xsl:output method="html" encoding="iso-8859-1"/>
  
  <xsl:template match="collection">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
      <th valign="top" align="left">Collection <xsl:value-of select="./@prefix"/>
        <xsl:if test="string-length (../@prefix) = 0">
          <a href="../index.html"><img src="/image/caret-t.gif" border="0"/></a>
        </xsl:if>
        <xsl:if test="string-length (../@prefix) &gt; 0">
          <a href="{../@prefix}.html"><img src="/image/caret-t.gif" border="0"/></a>
        </xsl:if>
      </th>
      <td valign="top" align="right">(<xsl:value-of select="count (.//class)"/> classes)</td></tr>
      <tr bgcolor="#cccccc"><td colspan="2"><img src="/image/spacer.gif" height="1" border="0"/></td></tr>
    </table>
    <br/>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <xsl:if test="count (./collection) &gt; 0">
        <tr>
          <td width="1%" valign="top"><img src="/image/nav_overview.gif"/></td>
          <td width="50%" valign="top">
            <b>Collections below <xsl:value-of select="./@prefix"/>:</b>
          </td>
        </tr>
        <tr>
          <td/>
          <td>
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
              <xsl:for-each select="collection">
                <xsl:sort select="./@shortName"/>
                <tr><td width="1%" valign="top"><img src="/image/nav_overview2.gif"/></td>
                <td width="50%"><b><a href="{./@prefix}.html"><xsl:value-of select="./@shortName"/></a></b> 
                <img src="/image/caret-r.gif"/><br/><br/>
                </td></tr>
              </xsl:for-each>
            </table>
          </td>
        </tr>
      </xsl:if>
      <xsl:if test="count (./class) &gt; 0">
        <tr>
          <td width="1%" valign="top"><img src="/image/nav_overview.gif"/></td>
          <td width="50%" valign="top">
            <b>Classes in <xsl:value-of select="./@prefix"/>:</b><br/><br/>
          </td>
        </tr>
        <tr>
          <td/>
          <td>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <xsl:for-each select="class">
                <tr>
                  <td valign="top" width="1%"><img src="/image/nav_overview3.gif"/></td>
                  <td width="50%">
                    <xsl:sort select="./@className"/>
                    <a href="../classes/{./@className}.html"><xsl:value-of select="./@className"/></a><br/>
                    <tt><xsl:value-of select="./cvsver"/></tt><br/>
                    <xsl:value-of select="./purpose"/><br/><br/>
                  </td>
                </tr>
              </xsl:for-each>
            </table>
            <br/><br/>
          </td>
        </tr>
      </xsl:if>
    </table>

  </xsl:template>
  
</xsl:stylesheet>
