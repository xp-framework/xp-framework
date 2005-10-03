<?xml version="1.0" encoding="utf-8"?>
<!-- 
 ! View pictures
 !
 ! $Id: view.xsl 5879 2005-10-01 18:53:45Z kiesel $
 !-->
<xsl:stylesheet 
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:import href="layout.xsl"/>
  <xsl:template name="page-title">
    Search - <xsl:value-of select="/formresult/config/general/site"/>
  </xsl:template>

  <xsl:template name="contents">
    <div id="container">
      <div id="header">
        <!-- ... -->
        <div id="header-nav">
          <a href="/{/formresult/navigation/@latestdate}">latest</a> |
          <a href="{func:link('about')}">about</a> |
          <a href="{func:link('search')}">search</a> |
          <a href="{func:link('links')}">links</a>
        </div>
      </div>
      
      <h3>Search this site with google</h3>
      <p>
        You can search this site using the Google search engine. Please specify
        your search query:<br/>
        
        <form method="GET" action="http://google.com/search">
        <input type="hidden" name="domains" value="{/formresult/uri/host}"/>
        <table>
          <tr>
            <td><img src="/image/google.gif"/></td>
            <td>
              <input type="text" width="20" name="q" maxlength="255"/>
            </td>
            <td>
              <input type="submit" name="submit" value="search this site"/>
            </td>
          </tr>
        </table>
        </form>
      </p>
    </div>
  </xsl:template>
</xsl:stylesheet>
