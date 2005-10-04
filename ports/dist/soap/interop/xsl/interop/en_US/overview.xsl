<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for home page
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:include href="../layout.xsl"/>
  
  <!--
   ! Template for context navigation
   !
   ! @see      ../../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">
    <!-- TDB: Add links / news -->
  </xsl:template>

  <!--
   ! Template for viewlog link
   !
   ! @see      ../../layout.xsl
   ! @purpose  Context navigation
   !-->
  <func:function name="func:viewlog">
    <xsl:param name="service"/>
    <xsl:param name="method"/>
    <xsl:param name="type"/>
    
    <xsl:variable name="map">
      <name type="stacktrace">View stacktrace</name>
      <name type="inout">View input/output</name>
      <name type="log">View XML log</name>
    </xsl:variable>
    
    <func:result>
      <a href="details?service={$service}&amp;method={$method}&amp;type={$type}">
        <xsl:value-of select="exsl:node-set($map)/name[@type= $type]"/>
      </a>
    </func:result>
  </func:function>

  <xsl:template match="error[@checker = 'net.xp_framework.scriptlet.interop.state.OverviewState']">
    <table class="error" width="100%">
      <tr>
        <td>
          There are no test results available at the moment.<br/>
          Please try again later and/or contact or interop group.
        </td>
      </tr>
    </table>
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h1>SOAP interop test suite</h1>
    
    <h2>SOAP interop test results</h2>
    <p>Here are the test results of the current tests:</p>
    
    <!-- Process all given errors first -->
    <xsl:apply-templates select="/formresult/formerrors/error"/>
    
    <xsl:if test="count(/formresult/formerrors/error) = 0">
      <!-- Show test results -->
      <h3>Test statistics</h3>

      <table width="100%" class="testresult">
        <tr class="service_description_head">
          <th>Service name</th>
          <th>PASSED</th>
          <th>FAILED</th>
          <th>Information</th>
        </tr>
        
        <xsl:for-each select="/formresult/clients/client">
          <tr class="service_description_{position() mod 2}">
            <td>
              <a href="{$__state}?servicedetails={@name}">
                <xsl:value-of select="./@name"/>
              </a>
            </td>
            <td><xsl:value-of select="count(method[@result = 1])"/></td>
            <td><xsl:value-of select="count(method[@result != 1])"/></td>
            <td><xsl:value-of select="./@uri"/></td>
          </tr>
          
          <!-- Display single method stats -->
          <xsl:if test="/formresult/formvalues/param[@name= 'servicedetails'] = ./@name">
            <xsl:for-each select="method">
              <tr>
                <td><xsl:value-of select="./@name"/></td>
                <td><xsl:if test="@result = 1">PASSED</xsl:if>&#160;</td>
                <td><xsl:if test="@result != 1">FAILED</xsl:if>&#160;</td>
                <td>
                  <!-- Display error type -->
                  <xsl:if test="@error">
                    Error: <xsl:value-of select="@error"/>
                  </xsl:if>
                  &#160;

                  <!-- Got SOAPFault? -->
                  <xsl:if test="soapfault">
                    <xsl:copy-of select="func:viewlog(../@name, ./@name, 'stacktrace')"/>
                  </xsl:if>
                  &#160;
                  
                  <!-- I/O Log -->
                  <xsl:copy-of select="func:viewlog(../@name, ./@name, 'inout')"/>
                  &#160;
                  
                  <!-- XML Log -->
                  <xsl:copy-of select="func:viewlog(../@name, ./@name, 'log')"/>
                </td>
              </tr>
            </xsl:for-each>
          </xsl:if>
        </xsl:for-each>
      </table>
    </xsl:if>
  </xsl:template>
</xsl:stylesheet>
