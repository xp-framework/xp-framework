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
    What's new?
    <br/><br/>

    <xsl:call-template name="nav-divider">
      <xsl:with-param name="caption">See also</xsl:with-param>
    </xsl:call-template>
    <ul class="nav">
      <li><a href="/">Home</a></li>
    </ul>
  </xsl:template>

  <xsl:template match="main">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <th valign="top" align="left">News overview</th>
        <td valign="top" align="right" style="color: #666666">(<xsl:value-of select="content/editor"/>)</td>
	  </tr>
	  <tr bgcolor="#cccccc">
        <td colspan="2"><img src="/image/spacer.gif" height="1" border="0"/>
      </td></tr>
    </table>
    <br/>

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
      <xsl:for-each select="$news/rdf:RDF/rss:item[position() &lt; 11]">
        <tr>
          <td width="4%" nowrap="nowrap">
            <img src="/image/caret-r.gif" height="7" width="11" alt="=&gt;" hspace="2" vspace="4"/>
          </td>
          <td>
            <a style="color: #a33818" href="#{position()}">
              <xsl:value-of select="rss:title"/>
            </a>
          </td>
          <td align="right">
            <xsl:value-of select="translate(dc:date, 'T', ' ')"/>
          </td>
        </tr>
      </xsl:for-each>
    </table>
    <br/><br/>
    
    <xsl:for-each select="$news/rdf:RDF/rss:item">
      <table border="0" cellspacing="0" cellpadding="2" width="100%">
        <tr>
          <td>
            <b>
              <a name="{position()}">
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
            </b>
          </td>
          <td width="4%" nowrap="nowrap" align="right">
            <a href="#top"><img alt="^" title="Top" border="0" src="/image/nav_top.gif" height="17" width="17" hspace="0" vspace="0"/></a>
          </td>
        </tr>
      </table>
      <xsl:call-template name="divider"/>
      <p style="line-height: 16px; text-align: justify">
        <xsl:apply-templates select="rss:description"/>
      </p>
      <small>
        <xsl:value-of select="translate(dc:date, 'T', ' ')"/>
      </small>
      <br/><br/>
    </xsl:for-each>
  </xsl:template>

</xsl:stylesheet>
