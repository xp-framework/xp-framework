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
    <xsl:call-template name="divider"/><br/>
    <xsl:apply-templates select="news"/>
    
    <!-- Links -->
    <br/><xsl:call-template name="divider"/><br/>
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <xsl:for-each select="links/link">
        <tr>
          <td width="4%" nowrap="nowrap">
            <img src="/image/anc_overview.gif" height="19" width="22" alt="=&gt;" hspace="2" vspace="2"/>
          </td>
          <td>
            <a href="{@href}"><b><xsl:value-of select="."/></b></a>
          </td>
        </tr>
      </xsl:for-each>
    </table>
  </xsl:template>
  
  <xsl:template match="introduction//*">
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
        <td>
          <a name="contents"><b style="color: #a33818">
            Recent news
          </b></a>
        </td>
      </tr>
      <xsl:for-each select="$news/rdf:RDF/rss:item[position() &lt; 6.1]">
        <tr>
          <td width="4%" nowrap="nowrap">
            <img src="/image/caret-r.gif" height="7" width="11" alt="=&gt;" hspace="2" vspace="4"/>
          </td>
          <td>
            <b><xsl:value-of select="translate(dc:date, 'T', ' ')"/></b>: <a style="color: #a33818" href="news.html#{position()}"><xsl:value-of select="rss:title"/></a>
          </td>
        </tr>
      </xsl:for-each>
    </table>
  </xsl:template>

</xsl:stylesheet>
