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
  <xsl:variable name="cvs" select="document('../src/cvs_history.xml')/document"/>
   
  <xsl:template name="cvs">
    <xsl:param name="action"/>
    <xsl:for-each select="$cvs/entry[@action = $action]">
      <xsl:if test="position() &lt; 4">
        <tr>
          <td><img src="/image/caret-r.gif" height="7" width="11" alt="=&gt;" hspace="2" vspace="4"/></td>
          <td>
            <a title="{collection}.{class}" href="/apidoc/classes/{collection}.{class}.html">
              <xsl:value-of select="class"/>
            </a>
          </td>
        </tr>
        <tr>
          <td/>
          <td>
            <small><xsl:value-of select="user"/>, <xsl:value-of select="date"/></small>
          </td>
        </tr>
      </xsl:if>
    </xsl:for-each>    
  </xsl:template>

  <xsl:template name="navigation">
    
    <a href="/">XP</a> stands for <b>X</b>ML <b>P</b>HP.<br/>
    XP is far more than that!
    
    <!-- News -->
    <br/><br/>
    <xsl:call-template name="nav-divider">
      <xsl:with-param name="caption">News</xsl:with-param>
      <xsl:with-param name="link">news.html</xsl:with-param>
    </xsl:call-template>
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <xsl:for-each select="$news/rdf:RDF/rss:item[position() &lt; 6.1]">
        <tr>
          <td valign="top"><img src="/image/caret-r.gif" height="7" width="11" alt="=&gt;" hspace="2" vspace="4"/></td>
          <td>
            <a>
              <xsl:attribute name="href">
                <xsl:choose>
                  <xsl:when test="substring-before(substring-after(rss:link, '//'), '/') = 'xp-framework.net'">
                    <xsl:value-of select="substring-after(substring-after(rss:link, '//'), '/')"/>
                  </xsl:when>
                  <xsl:otherwise>
                    <xsl:value-of select="rss:link"/>
                  </xsl:otherwise>
                </xsl:choose>
              </xsl:attribute>
              <xsl:value-of select="rss:title"/>
            </a>
          </td>
        </tr>
        <tr>
          <td/>
          <td>
            <small><xsl:value-of select="translate(dc:date, 'T', ' ')"/></small>
          </td>
        </tr>
      </xsl:for-each>
    </table>
    
    <!-- CVS activity -->
    <br/><br/>
    <xsl:call-template name="nav-divider">
      <xsl:with-param name="caption">CVS activity</xsl:with-param>
    </xsl:call-template>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td colspan="2"><b>Changed classes</b></td>
      </tr>
      <xsl:call-template name="cvs"><xsl:with-param name="action" select="'M'"/></xsl:call-template>
      <tr>
        <td colspan="2"><b>Added classes</b></td>
      </tr>
      <xsl:call-template name="cvs"><xsl:with-param name="action" select="'A'"/></xsl:call-template>
    </table>
    
  </xsl:template>

  <xsl:template match="main">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <th valign="top" align="left">Welcome to XP</th>
        <td valign="top" align="right" style="color: #666666">(Version 1.0)</td>
	  </tr>
	  <tr bgcolor="#cccccc">
        <td colspan="2"><img src="/image/spacer.gif" height="1" border="0"/>
      </td></tr>
    </table>
    
    <!-- Introduction -->
    <p style="line-height: 16px">
      <xsl:apply-templates select="introduction"/>
    </p>
    
    <!-- Links -->
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <th valign="top" align="left">Further reading:</th>
	  </tr>
	  <tr bgcolor="#cccccc">
        <td colspan="2"><img src="/image/spacer.gif" height="1" border="0"/>
      </td></tr>
    </table>
    <br/>
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
  
</xsl:stylesheet>
