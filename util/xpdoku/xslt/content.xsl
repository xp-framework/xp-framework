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

  <xsl:template name="navigation">
    <b>See also</b>
    <br/>
    <ul class="nav">
      <xsl:for-each select="/document/main/references/ref">
        <li><xsl:apply-templates select="."/></li>
      </xsl:for-each>
    </ul>
  </xsl:template>

  <xsl:template match="ref[@type= 'ext']">
    <a href="{@link}" target="_new"><xsl:value-of select="."/></a>
  </xsl:template>

  <xsl:template match="ref[@type= 'google']">
    <a href="http://google.de/search?q={@link}" target="_new">Google search: <xsl:value-of select="@link"/></a>
  </xsl:template>
  
  <xsl:template match="ref">
    <a href="/content/{@link}.html"><xsl:value-of select="."/></a>
  </xsl:template>
  
  <xsl:template match="main">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <th valign="top" align="left"><xsl:value-of select="content/title"/></th>
        <td valign="top" align="right" style="color: #666666">(<xsl:value-of select="content/editor"/>)</td>
	  </tr>
	  <tr bgcolor="#cccccc">
        <td colspan="2"><img src="/image/spacer.gif" height="1" border="0"/>
      </td></tr>
    </table>
    <br/>
    
    <table border="0" width="100%" cellspacing="0" cellpadding="2" bgcolor="#eeeeff" style="border: 1px dotted #3654a5">
      <tr>
        <td width="4%" nowrap="nowrap">
          <img src="/image/nav_toc.gif" height="17" width="17" alt="=&gt;" hspace="2" vspace="2"/>
        </td>
        <td>
          <a name="contents"><b style="color: #000066">
            Table of Contents
          </b></a>
        </td>
      </tr>
      <xsl:for-each select="content/para/caption">
        <tr>
          <td width="4%" nowrap="nowrap">
            <img src="/image/caret-r.gif" height="7" width="11" alt="=&gt;" hspace="2" vspace="4"/>
          </td>
          <td>
            Chapter <b><xsl:value-of select="position()"/></b>: <a href="#{position()}"><xsl:value-of select="."/></a>
          </td>
        </tr>
      </xsl:for-each>
    </table>
    <br/>
    <br/>
    
    <xsl:apply-templates select="content/para"/>
  </xsl:template>
  
  <xsl:template match="main/content/para">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
          <a name="{position()}"><b><xsl:value-of select="caption"/></b></a>
        </td>
        <td width="4%" nowrap="nowrap" align="right">
          <a href="#top"><img alt="^" title="Top" border="0" src="/image/nav_top.gif" height="17" width="17" hspace="0" vspace="0"/></a>
        </td>
      </tr>
    </table>
    <xsl:call-template name="divider"/>
    <p style="line-height: 16px; text-align: justify">
      <xsl:apply-templates select="text"/>
    </p>
  </xsl:template>

  <xsl:template match="code">
    <br/><br/>
    <xsl:call-template name="frame">
      <xsl:with-param name="color" select="'#cccccc'"/>
      <xsl:with-param name="content">
        <code><pre style="display: block"><xsl:apply-templates/></pre></code>
      </xsl:with-param>
    </xsl:call-template>
    <br/>
  </xsl:template>

</xsl:stylesheet>
