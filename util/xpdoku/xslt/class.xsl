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
  
  <xsl:template name="navigation">
    <!-- Nothing yet -->
  </xsl:template>

  <xsl:template name="classheader">
    <xsl:param name="classname"/>
    <xsl:param name="collection"/>
    
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <th valign="top" align="left">API Doc: Class <xsl:value-of select="$classname"/>
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
                [ 
                  <a href="/source/{./@classname}">View source</a> |
                  <a href="/class/{./@classname}">Link: Class</a> |
                  <a href="/package/{./@collection">Link: Collection</a>
                ]
              </td>
            </tr>
            <tr>
              <td>Generation time:</td>
              <td><xsl:value-of select="./@generated_at"/></td>
            </tr>
            <tr>
              <td>Class type:</td>
              <td><xsl:value-of select="./@type"/></td>
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

      <xsl:if test="string-length (./comments/class/deprecated) &gt; 0">
        <tr>
          <td valign="top" colspan="2">
            <xsl:call-template name="frame">
              <xsl:with-param name="color" select="'#990000'"/>
              <xsl:with-param name="content">
                <br/>
                <div align="center">
                  <b style="font-weight: bold; Color: #990000">
                    This class has been marked as deprecated.
                  </b>
                  <br/>
                  <br/>
                  Usage is discouraged though this class remains in the framework
                  for backward compatibility.
                </div>
                <br/>
              </xsl:with-param>
            </xsl:call-template>
            <br/>
          </td>
        </tr>
        <xsl:call-template name="embedded-divider"/>
      </xsl:if>
      <xsl:if test="string-length (./comments/class/experimental) &gt; 0">
        <tr>
          <td valign="top" colspan="2">
            <xsl:call-template name="frame">
              <xsl:with-param name="color" select="'#990000'"/>
              <xsl:with-param name="content">
                <br/>
                <div align="center">
                  <b style="font-weight: bold; Color: #990000">
                    This class has been marked as experimental.
                  </b>
                  <br/>
                  <br/>
                  Usage is discouraged as long as this tag exists. You may
                  use this class as it is committed for testing or to
                  improve the design. However, the API is probably supposed 
                  to change.                  
                </div>
                <br/>
              </xsl:with-param>
            </xsl:call-template>
            <br/>
          </td>
        </tr>
        <xsl:call-template name="embedded-divider"/>
      </xsl:if>

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
            <xsl:if test="string-length (./comments/class/extensions/*) != 0">
              <tr>
                <td valign="top">Requires PHP Extension:</td>
                <td valign="top">
                  <xsl:for-each select="./comments/class/extensions/*">
                    <xsl:variable name="tmpnode">
                      <link>
                        <scheme>php</scheme>
                        <host><xsl:value-of select="."/></host>
                      </link>
                    </xsl:variable>
                    <xsl:apply-templates select="$tmpnode"/>
                    <xsl:if test="position() &lt; count (../*)">
                      ,
                    </xsl:if>
                  </xsl:for-each>
                </td>
                
              </tr>
            </xsl:if>
            <tr>
              <td valign="top">Purpose:</td>
              <td valign="top">
                <xsl:value-of select="./comments/class/purpose"/>
              </td>
            </tr>
            <tr>
              <td valign="top" colspan="2">Additional comments:</td>
            </tr>
          </table>
          
          <!-- Additional comments -->
          <br/>
          <span style="white-space: pre">
            <xsl:apply-templates select="./comments/class/text"/>
          </span>
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
      <td width="50%">
        <xsl:apply-templates select="link"/>
        <xsl:if test="string-length (link/description) != 0">
          <xsl:text> - </xsl:text>
          <xsl:value-of select="link/description"/>
        </xsl:if>
        <br/><br/>
      </td>
    </tr>
  </xsl:template>
  
  <xsl:template name="cut-string">
    <xsl:param name="max_len" select="'60'"/>
    <xsl:param name="string"/>
  
    <xsl:choose>
      <xsl:when test="string-length($string) &gt; $max_len">
        <xsl:value-of select="substring($string, 1, $max_len)"/>
        <xsl:text>[...]</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$string"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  <xsl:template match="link[child::*[name() = 'scheme']/text() = 'http']">
    <a href="{./scheme}://{./host}{./path}?{./query}#{./fragment}" target="_blank">
      <xsl:call-template name="cut-string">
        <xsl:with-param name="string">
          <xsl:value-of select="./scheme"/>://<xsl:value-of select="./host"/><xsl:value-of select="./path"/>
          <xsl:if test="string-length (./query) != 0">
            ?<xsl:value-of select="./query"/>
          </xsl:if>
          <xsl:if test="string-length (./fragment) != 0">
            #<xsl:value-of select="./fragment"/>
          </xsl:if>
        </xsl:with-param>
      </xsl:call-template>
    </a>
  </xsl:template>
  
  <xsl:template match="link[child::*[name() = 'scheme']/text() = 'xp']">
    <a href="/apidoc/classes/{./host}.html#{./fragment}">
      <xsl:value-of select="./host"/>
      <xsl:if test="string-length (./fragment) != 0">
        #<xsl:value-of select="./fragment"/>
      </xsl:if>
    </a>
  </xsl:template>

  <xsl:template match="link[child::*[name() = 'scheme']/text() = 'xp-doc']">
    <a href="/apidoc/{./path}">
      <xsl:value-of select="./path"/>
    </a>
  </xsl:template>
  
  <xsl:template match="link[child::*[name() = 'scheme']/text() = 'mailto']">
    <a href="mailto:{./path}" target="_blank"><xsl:value-of select="./path"/></a>
  </xsl:template>  
  
  <xsl:template match="link[child::*[name() = 'scheme']/text() = 'php']">
    <a href="http://php3.de/{./host}" target="_blank"><xsl:value-of select="./host"/></a>
  </xsl:template>
  
  <xsl:template match="link[child::*[name() = 'scheme']/text() = 'rfc']">
    <a href="http://www.faqs.org/rfcs/rfc{./host}.html#{./fragment}" target="_blank">
      RFC <xsl:value-of select="./host"/>
      <xsl:if test="string-length (./fragment) != 0">
        Section <xsl:value-of select="./fragment"/>
      </xsl:if>
    </a>
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
      <td valign="top"><xsl:value-of select="./return/type"/></td>
      <td valign="top"><a href="#{name()}"><xsl:value-of select="name()"/></a>
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
          <xsl:if test="string-length (./model) != 0"><xsl:value-of select="./model"/><xsl:text> </xsl:text></xsl:if>
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
          <table border="0" cellpadding="2" cellspacing="0" width="100%" style="color: #666666">
            <!-- Access information -->
            <tr>
              <td width="1%" valign="top"><img src="/image/caret-r.gif" vspace="4" border="0" height="7" width="11" alt="&gt;"/></td>
              <td width="20%" valign="top">
                Access
              </td>
              <td width="60%" valign="top">
                <ul style="list-style-image: url(/image/icn_li_{./access}.gif);">
                  <li>
                    <xsl:value-of select="./access"/>
                  </li>
                </ul>
              </td>
            </tr>
            
            <!-- Model information -->
            <xsl:if test="./model/text()">
              <tr>
                <td width="1%" valign="top"><img src="/image/caret-r.gif" vspace="4" border="0" height="7" width="11" alt="&gt;"/></td>
                <td width="20%" valign="top">
                  Model
                </td>
                <td width="60%" valign="top">
                  <ul style="list-style-image: url(/image/icn_li_public.gif);">
                    <li>
                      <xsl:value-of select="./model"/>
                    </li>
                  </ul>
                </td>
              </tr>
            </xsl:if>
            
            <!-- Param information -->
            <xsl:if test="count (./params/*) &gt; 0">
              <tr>
                <td width="1%" valign="top"><img src="/image/caret-r.gif" vspace="4" border="0" height="7" width="11" alt="&gt;"/></td>
                <td width="20%" valign="top">
                  Arguments
                </td>
                <td width="60%" valign="top">
                  <ul>
                    <xsl:for-each select="./params/*">
                      <li>
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
                      </li>
                    </xsl:for-each>
                  </ul>
                </td>
              </tr>
            </xsl:if>
            
            <!-- Return information. Constructors and destructors have no real return value -->
            <xsl:if test="name() != '__construct' and name() != '__destruct' and name() != /classdoc/@classname">
              <tr>
                <td width="1%" valign="top"><img src="/image/caret-r.gif" vspace="4" border="0" height="7" width="11" alt="&gt;"/></td>
                <td width="20%" valign="top">
                  Returns
                </td>
                <td width="60%" valign="top">
                  <ul><li><tt><u>
                    <xsl:if test="string-length(./return/type) != 0">
                      <xsl:value-of select="./return/type"/>
                    </xsl:if>
                    <xsl:if test="string-length(./return/type) = 0">
                      void
                    </xsl:if>
                  </u></tt><br/>
                  <xsl:copy-of select="./return/description"/><br/>
                  </li></ul>
                </td>
              </tr>
            </xsl:if>
            
            <!-- Exception information -->
            <xsl:if test="count (./throws/*) &gt; 0">
              <tr>
                <td width="1%" valign="top"><img src="/image/caret-r.gif" vspace="4" border="0" height="7" width="11" alt="&gt;"/></td>
                <td width="20%" valign="top">
                  Exceptions
                </td>
                <td width="60%" valign="top">
                  <ul>
                    <xsl:for-each select="./throws/throw">
                      <li>
                        <tt><u>
                          <xsl:value-of select="exception"/>
                        </u></tt><xsl:text> </xsl:text>
                        <br/>
                        <xsl:value-of select="condition"/>
                      </li>
                    </xsl:for-each>
                  </ul>
                </td>
              </tr>
            </xsl:if>
            
            <!-- References -->
            <xsl:if test="count (./references/reference/link) &gt; 0">
              <tr>
                <td width="1%" valign="top"><img src="/image/caret-r.gif" vspace="4" border="0" height="7" width="11" alt="&gt;"/></td>
                <td width="20%" valign="top">
                  See also
                </td>
                <td width="60%" valign="top">
                  <ul>
                    <xsl:for-each select="./references/reference/link">
                      <li><xsl:apply-templates select="."/></li>
                    </xsl:for-each>
                  </ul>
                </td>
              </tr>
            </xsl:if>

            <!-- Additional information for this function -->
            <tr>
              <td colspan="3" valign="top" style="color: black; white-space: pre">
                <xsl:apply-templates select="./text"/><br/>
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
