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
  
  <xsl:template match="main/news">
    <xsl:variable name="news" select="'../content/news.rdf.xml'"/>
    <xsl:comment> News generated from <xsl:value-of select="$news"/> </xsl:comment>
    
    <!-- 
      Default namespace "quirk", see 
      http://web.resource.org/rss/1.0/modules/SRDF/ 
    -->
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <xsl:for-each select="document($news)/rdf:RDF/rss:item">
        <tr>
          <td width="1%" valign="top" nowrap="nowrap">
            <img src="/image/anc_detail.gif" width="22" height="19" border="0"/>
            <img src="/image/spacer.gif" hspace="4" width="1" height="1" border="0"/>
          </td>
          <td width="99%" valign="top">
            <b>
              <a href="{rss:link}"><xsl:value-of select="rss:title"/></a>
            </b>
            <br/>
            <xsl:apply-templates select="rss:description"/>
            <br/>
            <small>
              <xsl:value-of select="translate(dc:date, 'T', ' ')"/>
            </small>
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
