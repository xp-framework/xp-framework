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
    <b>See also</b>
    <br/>
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

    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <xsl:for-each select="$news/rdf:RDF/rss:item">
        <tr>
          <td width="1%" valign="top" nowrap="nowrap">
            <img src="/image/anc_detail.gif" width="22" height="19" border="0"/>
            <img src="/image/spacer.gif" hspace="4" width="1" height="1" border="0"/>
          </td>
          <td width="99%" valign="top">
            <b>
              <a name="{position()}">
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
            <br/>
            <xsl:apply-templates select="rss:description"/>
            <br/>
            <small>
              <xsl:value-of select="translate(dc:date, 'T', ' ')"/>
            </small>
            <br/>
            <br/>
          </td>
        </tr>

        <!-- Divider -->
        <xsl:if test="position() != last()">
          <xsl:call-template name="embedded-divider"/>
        </xsl:if>

      </xsl:for-each>
    </table>
  </xsl:template>

</xsl:stylesheet>
