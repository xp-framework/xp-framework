<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:rss="http://my.netscape.com/rdf/simple/0.9/"
  exclude-result-prefixes="rdf dc rss"
>
  <xsl:output method="xhtml" encoding="iso-8859-1"/>
  <xsl:param name="mode" select="'index'"/>
  <xsl:include href="xsl-helper.xsl"/>
  <xsl:include href="rdf-helper.xsl"/>

  <xsl:template name="navigation">
    <a href="/">XP</a> stands for <b>X</b>ML <b>P</b>HP.<br/>
    XP is far more than that!
  </xsl:template>

  <xsl:template match="main">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <th valign="top" align="left">Welcome to XP</th>
        <td valign="top" align="right" style="color: #666666">(Version 0.1)</td>
	  </tr>
	  <tr bgcolor="#cccccc">
        <td colspan="2"><img src="/image/spacer.gif" height="1" border="0"/>
      </td></tr>
    </table>
    
    <!-- Introduction -->
    <p style="line-height: 16px">
      <xsl:apply-templates select="introduction"/>
    </p>
    
    <!-- News -->
    <br/>
    <xsl:apply-templates select="news"/>
    
    <!-- Links -->
    <br/>
    <p style="line-height: 16px">
      <b>Further reading:</b>
    </p>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <xsl:for-each select="links/link">
        <xsl:if test="position() &gt; 1">
          <tr>
            <td>
              <img src="/image/spacer.gif" width="1" height="1" border="0" hspace="0" vspace="0"/>
            </td>
            <td colspan="2" style="border-bottom: 1px dashed #616161">
              <img src="/image/spacer.gif" width="1" height="1" border="0" hspace="0" vspace="0"/>
            </td>
          </tr>
        </xsl:if>
        <tr>
          <td width="4%" nowrap="nowrap">
            <img src="/image/anc_overview.gif" height="19" width="22" alt="=&gt;" hspace="2" vspace="2"/>
          </td>
          <td>
            <a href="{@href}">
              <xsl:if test="substring-before(@href, '://') = 'http'">
                <xsl:attribute name="target">_new</xsl:attribute>
              </xsl:if>
              <b><xsl:value-of select="caption"/></b>
            </a>
          </td>
        </tr>
        <tr>
          <td>&#160;</td>
          <td>
            <p style="line-height: 16px">
              <xsl:apply-templates select="description"/>
            </p>
          </td>
        </tr>
      </xsl:for-each>
    </table>
  </xsl:template>
  
  <xsl:template match="introduction//*|description//*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>
  
  <xsl:template match="main/news">
    <!-- 
      Default namespace "quirk", see 
      http://web.resource.org/rss/1.0/modules/SRDF/ 
    -->
    <table border="0" width="100%" cellspacing="0" cellpadding="2" bgcolor="#ffeadb" style="border: 1px dotted #e0670b">
      <tr>
        <td width="4%" nowrap="nowrap">
          <img src="/image/nav_list2.gif" height="17" width="17" alt="=&gt;" hspace="2" vspace="2"/>
        </td>
        <td colspan="2">
          <b style="color: #a33818">Recent news</b>
        </td>
      </tr>
      <xsl:for-each select="$news/rdf:RDF/rss:item[position() &lt; 6.1]">
        <tr>
          <td>
            <img src="/image/spacer.gif" width="1" height="1" border="0" hspace="0" vspace="0"/>
          </td>
          <td colspan="2" style="border-bottom: 1px dashed #e0670b">
            <img src="/image/spacer.gif" width="1" height="1" border="0" hspace="0" vspace="0"/>
          </td>
        </tr>
        <tr>
          <td width="4%" nowrap="nowrap">
            <img src="/image/caret-r.gif" height="7" width="11" alt="=&gt;" hspace="2" vspace="4"/>
          </td>
          <td>
            <b>
              <a style="color: #a33818">
                <xsl:attribute name="href">
                  <xsl:choose>
                    <xsl:when test="substring-before(substring-after(rss:link, '//'), '/') = 'xp.php3.de'">
                      <xsl:value-of select="substring-after(substring-after(rss:link, '//'), '/')"/>
                    </xsl:when>
                    <xsl:otherwise>
                      <xsl:value-of select="rss:link"/>
                    </xsl:otherwise>
                  </xsl:choose>
                </xsl:attribute>
                <xsl:value-of select="rss:title"/>
              </a>
            </b>
          </td>
          <td align="right">
            <b><xsl:value-of select="translate(dc:date, 'T', ' ')"/></b>
          </td>
        </tr>
        <tr>
          <td>&#160;</td>
          <td colspan="2">
            <xsl:value-of select="substring(rss:description, 0, 128)"/>...
            <a style="color: #a33818; text-decoration: none" href="news.html#{position()}">[more]</a>
          </td>
        </tr>
      </xsl:for-each>
    </table>
  </xsl:template>

</xsl:stylesheet>
