<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="1.0"
>
  <xsl:output method="xhtml" encoding="iso-8859-1"/>
  
  <xsl:param name="collection"/>
  <xsl:param name="package"/>
  <xsl:param name="mode" select="'class'"/>
  
  <!-- Include main window part -->
  <xsl:include href="xsl-helper.xsl"/>

  <xsl:template name="classheader">
    <xsl:param name="classname"/>
    <xsl:param name="collection"/>
    
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
      <th valign="top" align="left">Class <xsl:value-of select="$classname"/>
      <a href="../collections/{./@collection}.html"><img src="/image/caret-t.gif" border="0"/></a>
      </th>
      <td valign="top" align="right">
        <xsl:if test="string-length (./@version) != 0">
          (version <xsl:value-of select="./@version"/>)
        </xsl:if>
      </td></tr>
      <tr bgcolor="#cccccc"><td colspan="2"><img src="/image/spacer.gif" height="1" border="0"/></td></tr>
    </table>
    <br/>
  </xsl:template>

  <xsl:template name="class">
    <!-- Overview -->
    <table border="0" cellpadding="0" cellspacing="0">

      <!-- General overview -->
      <tr>
        <td width="1%" valign="top"><img src="/image/anc_overview.gif"/></td>
        <td width="50%">
          <b>General file information:</b>
          <table border="0" cellpadding="2" cellspacing="0" width="100%">
            <tr>
              <td width="30%" valign="top">Filename:</td>
              <td width="70%"><xsl:value-of select="./@filename"/><br/>
                <a href="/showsource.php?f={./@classname}">[View source]</a>
              </td>
            </tr>
            <tr>
              <td>Generation time:</td>
              <td><xsl:value-of select="./@generated_at"/></td>
            </tr>
            <tr>
              <td>Fully qualified name:</td>
              <td><xsl:value-of select="./@classname"/> in collection <xsl:value-of select="./@collection"/></td>
            </tr>
            <tr>
              <td>Last CVS Checkin:</td>
              <td>Version <xsl:value-of select="./@version"/> by <xsl:value-of select="./@checkinUser"/>
                at <xsl:value-of select="./@checkinDate"/>, <xsl:value-of select="./@checkinTime"/></td>
              </tr>
          </table>
          <br/>
        </td>
      </tr>
      <xsl:call-template name="embedded-divider"/>

      <!-- Class purpose -->
      <tr>
        <td width="1%" valign="top"><img src="/image/anc_detail.gif"/></td>
        <td width="50%" valign="top">
          <b>Declaration and purpose</b>
          <table border="0" cellpadding="2" cellspacing="0" width="100%">
            <tr>
              <td valign="top" width="30%">Declaration:</td>
              <td valign="top" width="70%">
                <code>
                  <xsl:value-of select="./comments/class/model"/> class 
                  <xsl:value-of select="substring (./@classname, string-length (./@collection)+2)"/>
                  <xsl:if test="string-length (./comments/class/extends) != 0"> extends <xsl:value-of select="./comments/class/extends"/><br/>
                  </xsl:if>
                </code>
              </td>
            </tr>
            <tr>
              <td valign="top">Purpose:</td>
              <td valign="top">
                <xsl:value-of select="./comments/class/purpose"/>
              </td>
            </tr>
            <tr>
              <td valign="top">Additional comments:</td>
              <td valign="top">
                <xsl:apply-templates select="./comments/class/text"/>
              </td>
            </tr>
          </table>
          <br/>
        </td>
      </tr>
      <xsl:call-template name="embedded-divider"/>


      <!-- References -->
      <xsl:if test="count (comments/class/references/reference) &gt; 0">
        <tr>
          <td width="1%" valign="top"><img src="/image/anc_see.gif"/></td>
          <td width="50%">
            <xsl:apply-templates select="comments/class/references"/>
          </td>
        </tr> 
        <xsl:call-template name="embedded-divider"/>
      </xsl:if>

      <!-- Defines -->
      <xsl:if test="count (./defines/*) &gt; 0">
        <tr>
          <td width="1%" valign="top"><img src="/image/anc_method.gif"/></td>
          <td width="50%"><xsl:apply-templates select="./defines"/><br/></td>
        </tr>
        <xsl:call-template name="embedded-divider"/>
      </xsl:if>

      <!-- Methods -->
      <xsl:if test="count (./comments/function/*) &gt; 0">
        <tr>
          <td width="1%" valign="top"><img src="/image/anc_method.gif"/></td>
          <td width="50%">
            <b>Methods defined in this class:</b><br/>
            <table border="0" cellspacing="0" cellpadding="2">
              <xsl:for-each select="./comments/function/*">
                <xsl:sort select="name()"/>
                <xsl:call-template name="functionname"/>
              </xsl:for-each>
            </table>
          </td>
        </tr>
      </xsl:if>
    
    <!-- Member-variables -->
    <!-- not yet being parsed -->
  
    <!-- Details -->
  </table>  
  
  <xsl:for-each select="./comments/function/*">
    <xsl:sort select="name()"/>
    <xsl:call-template name="function"/>
  </xsl:for-each>
  
  </xsl:template>
  
  <xsl:template match="references">
    <b>References:</b><br/><br/>
    <table border="0" width="100%" cellpadding="0" cellspacing="0">
      <xsl:for-each select="reference">
        <xsl:apply-templates select="."/>
      </xsl:for-each>
    </table>
  </xsl:template>
  
  <xsl:template match="reference">
    <tr>
      <td width="1%" valign="top"><img src="/image/nav_see.gif"/></td>
      <td width="50%"><xsl:apply-templates select="link"/><br/><br/></td>
    </tr>
  </xsl:template>
  
  <xsl:template match="link">
    <xsl:choose>
      <xsl:when test="./scheme = 'http'">
        <a href="{./scheme}://{./host}{./path}#{./fragment}" target="_blank">
        <xsl:value-of select="./scheme"/>://<xsl:value-of select="./host"/><xsl:value-of select="./path"/>#<xsl:value-of select="./fragment"/>
        </a>
      </xsl:when>

      <xsl:otherwise>
        <a href="http://xp.php3.de/ref/?_host={./host}&amp;_path={./path}&amp;_fragment={./fragment}"><xsl:value-of select="./scheme"/>://
        <xsl:value-of select="./host"/><xsl:value-of select="./path"/><xsl:value-of select="./fragment"/></a>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  <xsl:template match="defines">
    <b>Defines / Constants defined in this class:</b><br/>
    <table border="0" cellspacing="0" cellpadding="0">
      <xsl:for-each select="*">
        <tr>
          <!-- <td width="1%" valign="top"><img src="/image/anc_method.gif"/></td>-->
          <td width="50%"><pre><xsl:value-of select="name()"/></pre></td>
          <td>=</td>
          <td><pre><xsl:value-of select="."/></pre></td>
        </tr>
      </xsl:for-each>
    </table>
  </xsl:template>
  
  <xsl:template name="functionname">
    <tr>
      <td><xsl:value-of select="./return/type"/></td>
      <td><a href="#{name()}"><xsl:value-of select="name()"/></a>
        (<xsl:for-each select="./params/param">
          <xsl:value-of select="./type"/><xsl:text> </xsl:text>
          <xsl:value-of select="./name"/>
          <xsl:if test="position() &lt; count (../param)">, </xsl:if>
        </xsl:for-each>)
      </td>
    </tr>
  </xsl:template>
  
  <xsl:template name="function">
    <xsl:call-template name="divider"/>
    <br/>
    <table border="0" cellpadding="0" cellspacing="0">
      <tr>
        <xsl:choose>
          <xsl:when test="name() = '__construct' or name() = '__destruct' or name() = /classdoc/@classname">
            <td width="1%" valign="top"><img src="/image/nav_constructor.gif"/></td>
          </xsl:when>
          <xsl:otherwise>
            <td width="1%" valign="top"><img src="/image/nav_method.gif"/></td>
          </xsl:otherwise>
        </xsl:choose>
        <td width="80%">
          <b>
          <xsl:value-of select="./access"/><xsl:text> </xsl:text>
          <xsl:if test="string-length (./return/type) = 0">void<xsl:text> </xsl:text></xsl:if>
          <xsl:if test="string-length (./return/type) != 0">
            <xsl:value-of select="./return/type"/><xsl:text> </xsl:text>
          </xsl:if>
          <a name="{name()}"><xsl:value-of select="name()"/></a><xsl:text> </xsl:text>
          (<xsl:for-each select="./params/param">
           <xsl:value-of select="./type"/><xsl:text> </xsl:text>
           <xsl:value-of select="./name"/>
           <xsl:if test="position() &lt; count (../param)">, </xsl:if>
          </xsl:for-each>)
          </b>
        </td>
        <td valign="top" align="right" width="1%"><a href="#top"><img src="/image/caret-u.gif" border="0"/></a></td>
      </tr>
      <tr><td></td>
        <td>
          <table border="0" cellpadding="2" cellspacing="0" width="100%">
            <!-- Access information -->
            <tr>
              <td width="1%" valign="top"><img src="/image/caret-r.gif"/></td>
              <td width="20%" valign="top">
                Access
              </td>
              <td width="60%" valign="top">
                <img src="/image/caret-r_{./access}.gif"/> <xsl:value-of select="./access"/>
              </td>
            </tr>
            
            <!-- Param information -->
            <xsl:if test="count (./params/*) &gt; 0">
              <tr>
                <td width="1%" valign="top"><img src="/image/caret-r.gif"/></td>
                <td width="20%" valign="top">
                  Arguments
                </td>
                <td width="60%" valign="top">
                  <xsl:for-each select="./params/*">
                    <tt><u>
                    <xsl:value-of select="./type"/><xsl:text> </xsl:text>
                    <xsl:value-of select="./name"/></u>
                    <xsl:if test="string-length (./default) &gt; 0">
                      = <xsl:value-of select="./default"/><xsl:text> </xsl:text>
                    </xsl:if>
                    </tt>
                    <br/>
                    <xsl:value-of select="./description"/>
                    <xsl:if test="string-length (./description) != 0"><br/><br/></xsl:if>
                  </xsl:for-each>
                </td>
              </tr>
            </xsl:if>
            
            <!-- Return information -->
            <tr>
              <td width="1%" valign="top"><img src="/image/caret-r.gif"/></td>
              <td width="20%" valign="top">
                Returns
              </td>
              <td width="60%" valign="top">
                <tt><u>
                  <xsl:if test="string-length(./return/type) != 0">
                    <xsl:value-of select="./return/type"/>
                  </xsl:if>
                  <xsl:if test="string-length(./return/type) = 0">
                    void
                  </xsl:if>
                </u></tt><br/>
                <xsl:copy-of select="./return/description"/><br/>
              </td>
            </tr>
            
            <!-- Exception information -->
            <xsl:if test="count (./throws/*) &gt; 0">
              <tr>
                <td width="1%" valign="top"><img src="/image/caret-r.gif"/></td>
                <td width="20%" valign="top">
                  Exceptions
                </td>
                <td width="60%" valign="top">

                </td>
              </tr>
            </xsl:if>
            
            <!-- Additional information for this function -->
            <tr>
              <td width="1%" valign="top"><img src="/image/nav_contrib.gif"/></td>
              <td colspan="2" valign="top">
                <xsl:copy-of select="./text"/><br/>
                <xsl:for-each select="./references/reference/link">
                  <img src="/image/icn_li.gif"/><xsl:apply-templates/><br/>
                </xsl:for-each>
              </td>
            </tr>
          </table>
        </td>
        <td></td>
      </tr>
    </table>
    <br/>
  </xsl:template>

</xsl:stylesheet>
