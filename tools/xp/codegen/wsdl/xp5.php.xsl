<?xml version="1.0" ?>
<!--
 ! Stylesheet that generates an XP class from a WSDL
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:exsl="http://exslt.org/common"
 xmlns:func="http://exslt.org/functions"
 xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
 xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
 xmlns:http="http://schemas.xmlsoap.org/wsdl/http/"
 xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/"
 xmlns:xpdoc="http://xp-framework.net/TR/apidoc/"
 extension-element-prefixes="func"
>
  <xsl:output method="text" indent="no"/>
  <xsl:param name="collection" select="'xml.wsdl.gen'"/>
  <xsl:param name="prefix" select="''"/>
  <xsl:param name="boundary" select="'d4c3$bd1e091e.e245bfe04'"/>

  <xsl:variable name="lcletters">abcdefghijklmnopqrstuvwxyz</xsl:variable>
  <xsl:variable name="ucletters">ABCDEFGHIJKLMNOPQRSTUVWXYZ</xsl:variable>

  <!--
   ! Type mapping for what the SOAP API maps automatically
   !
   ! @see   xp://webservices.soap.SOAPClient
   !-->
  <xsl:variable name="typemap">
    <mapping for="xsd:string">string</mapping>
    <mapping for="string">string</mapping>
    <mapping for="xsd:long">webservices.soap.types.SOAPLong</mapping>
    <mapping for="xsd:int">int</mapping>
    <mapping for="xsd:float">float</mapping>
    <mapping for="xsd:double">float</mapping>
    <mapping for="xsd:boolean">bool</mapping>
    <mapping for="soapenc:Array">array</mapping>
    <mapping for="xsd:base64Binary">webservices.soap.types.SOAPBase64Binary</mapping>
    <mapping for="apachesoap:Map">webservices.soap.types.SOAPHashmap</mapping>
  </xsl:variable>

  <!--
   ! Function that adds a MIME boundary
   !
   ! @type   named
   ! @param  string string
   !-->
  <func:function name="func:nextpart">
    <xsl:param name="filename"/>
  
    <func:result select="concat(
      '------_=_NextPart_', $boundary, '&#10;',
      'Content-Type: text/plain; name=&quot;', $collection, '.', $prefix, $filename, '&quot;&#10;',
      'Content-Transfer-Encoding: 8bit&#10;',
      '&#10;'
    )"/>
  </func:function>  

  <!--
   ! Function that transforms the first character of a string into lowercase
   !
   ! @type   named
   ! @param  string string
   !-->
  <func:function name="func:lcfirst">
    <xsl:param name="string"/>
  
    <func:result select="concat(
      translate(substring($string, 1, 1), $ucletters, $lcletters),
      substring($string, 2)
    )"/>
  </func:function>  

  <!--
   ! Function that transforms the first character of a string into uppercase
   !
   ! @type   named
   ! @param  string string
   !-->
  <func:function name="func:ucfirst">
    <xsl:param name="string"/>
  
    <func:result select="concat(
      translate(substring($string, 1, 1), $lcletters, $ucletters),
      substring($string, 2)
    )"/>
  </func:function>  

  <!--
   ! Function for class name.
   !
   ! @type   named
   ! @param  string name
   ! @param  string postfix default ''
   !-->
  <func:function name="func:class">
    <xsl:param name="name"/>
    <xsl:param name="postfix" select="''"/>
    
    <func:result>
      <xsl:variable name="className">
        <xsl:choose>
          <xsl:when test="contains($name, 'Service')">
            <xsl:value-of select="func:ucfirst(substring-before($name, 'Service'))"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="func:ucfirst($name)"/>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:variable>
      <xsl:value-of select="concat(translate($className, '.', '_'), $postfix)"/>
    </func:result>
  </func:function>

  <!--
   ! Template for creating API doc comments
   !
   ! @type   named
   ! @param  string name
   ! @param  string indent default '  '
   !-->
  <xsl:template name="xpdoc:comment">
    <xsl:param name="string"/>
    <xsl:param name="indent" select="'  '"/>
 
    <xsl:value-of select="concat($indent, ' * ')"/>
   
    <xsl:choose>
      <xsl:when test="normalize-space($string) = ''">
        <xsl:text>(Insert documentation here)&#10;</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:variable name="remaining" select="substring-after($string, '&#xA;')"/>
        <xsl:value-of select="concat(
          normalize-space(substring($string, 1, string-length($string) - string-length($remaining))),
          '&#10;'
        )"/>
        <xsl:if test="$remaining != ''">  
          <xsl:call-template name="xpdoc:comment">
            <xsl:with-param name="string" select="$remaining"/>
          </xsl:call-template>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <!--
   ! Template for return value documentation
   !
   ! @type   named
   ! @param  string for
   ! @param  string indent default '    '
   !-->
  <xsl:template name="xpdoc:return">
    <xsl:param name="for"/>
    <xsl:param name="indent" select="'    '"/>

    <xsl:choose>
      <xsl:when test="contains($for, ':') and /wsdl:definitions/wsdl:message[@name = substring-after($for, ':')]/wsdl:part[1] != ''">
        <xsl:value-of select="concat(
          $indent,
          ' * @return  '
        )"/>
        <xsl:call-template name="parttype">
          <xsl:with-param name="node" select="/wsdl:definitions/wsdl:message[@name = substring-after($for, ':')]/wsdl:part[1]"/>
        </xsl:call-template>
      </xsl:when>
      <xsl:when test="/wsdl:definitions/wsdl:message[@name = $for]/wsdl:part[1] != ''">
        <xsl:value-of select="concat(
          $indent,
          ' * @return  '
        )"/>
        <xsl:call-template name="parttype">
          <xsl:with-param name="node" select="/wsdl:definitions/wsdl:message[@name = $for]/wsdl:part[1]"/>
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise></xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <!--
   ! Template for input arguments documentation
   !
   ! @type   named
   ! @param  string for
   ! @param  string indent default '    '
   !-->
  <xsl:template name="xpdoc:arguments">
    <xsl:param name="for"/>
    <xsl:param name="indent" select="'    '"/>
    
    <xsl:choose>
      <xsl:when test="contains($for, ':')">
        <xsl:for-each select="/wsdl:definitions/wsdl:message[@name = substring-after($for, ':')]/wsdl:part">
          <xsl:value-of select="concat(
            $indent,
            ' * @param   '
          )"/>
          <xsl:call-template name="parttype">
            <xsl:with-param name="node" select="."/>
          </xsl:call-template>
          <xsl:text> </xsl:text>
          <xsl:value-of select="@name"/>
          <xsl:if test="position() != last()"><xsl:text>&#10;</xsl:text></xsl:if>
        </xsl:for-each>
      </xsl:when>
      <xsl:otherwise>
        <xsl:for-each select="/wsdl:definitions/wsdl:message[@name = $for]/wsdl:part">
          <xsl:value-of select="concat(
            $indent,
            ' * @param   '
          )"/>
          <xsl:call-template name="parttype">
            <xsl:with-param name="node" select="."/>
          </xsl:call-template>
          <xsl:text> </xsl:text>
          <xsl:value-of select="@name"/>
          <xsl:if test="position() != last()"><xsl:text>&#10;</xsl:text></xsl:if>
        </xsl:for-each>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <!--
   ! Template for argument documentation
   !
   ! @type   named
   ! @param  string type
   !-->
  <xsl:template name="xpdoc:argument">
    <xsl:param name="type"/>

    <xsl:choose>
      <xsl:when test="exsl:node-set($typemap)/mapping[@for = $type]">
        <xsl:value-of select="exsl:node-set($typemap)/mapping[@for = $type]"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>mixed (</xsl:text>
        <xsl:value-of select="$type"/>
        <xsl:text>)</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:text> </xsl:text>
  </xsl:template>

  <!--
   ! Template for input value messages
   !
   ! @type   named
   ! @param  string for
   !-->
  <xsl:template name="arguments">
    <xsl:param name="for"/>
    
    <xsl:choose>
      <xsl:when test="contains($for, ':')">
        <xsl:for-each select="/wsdl:definitions/wsdl:message[@name = substring-after($for, ':')]/wsdl:part">
          <xsl:text>$</xsl:text>
          <xsl:value-of select="@name"/>
          <xsl:if test="position() &lt; last()"><xsl:text>, </xsl:text></xsl:if>
        </xsl:for-each>
      </xsl:when>
      <xsl:otherwise>
        <xsl:for-each select="/wsdl:definitions/wsdl:message[@name = $for]/wsdl:part">
          <xsl:text>$</xsl:text>
          <xsl:value-of select="@name"/>
          <xsl:if test="position() &lt; last()"><xsl:text>, </xsl:text></xsl:if>
        </xsl:for-each>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <!--
   ! Template for input value messages
   !
   ! @type   named
   ! @param  string for
   ! @param  string indent default '        '
   !-->
  <xsl:template name="argumentnames">
    <xsl:param name="for"/>
    <xsl:param name="indent" select="'        '"/>

    <xsl:variable name="args" select="/wsdl:definitions/wsdl:message[@name = $for or @name = substring-after($for, ':')]/wsdl:part"/>

    <xsl:if test="count($args) &gt; 0">
      <xsl:value-of select="',&#xa;'"/>
      <xsl:for-each select="$args">
        <xsl:value-of select="$indent"/>
        <xsl:text>new Parameter('</xsl:text>
        <xsl:value-of select="@name"/>
        <xsl:text>', $</xsl:text>
        <xsl:value-of select="@name"/>
        <xsl:text>)</xsl:text>
        <xsl:if test="position() &lt; last()"><xsl:text>,&#10;</xsl:text></xsl:if>
      </xsl:for-each>
    </xsl:if>
  </xsl:template>

  <!--
   ! Template for a part's type name
   !
   ! @type   named
   ! @param  node-set node default node(NULL)
   !-->
  <xsl:template name="parttype"> 
    <xsl:param name="node" select="/.."/>

    <xsl:choose>
      <xsl:when test="exsl:node-set($typemap)/mapping[@for = $node/@type]">
        <xsl:value-of select="exsl:node-set($typemap)/mapping[@for = $node/@type]"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>mixed (</xsl:text>
        <xsl:value-of select="$node/@type"/>
        <xsl:text>)</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <!--
   ! Template that matches a complexType node that has a subnode whose
   ! name is "all"
   !
   ! @type   match
   !-->
  <xsl:template match="xsd:complexType[child::*[name() = 'xsd:all']]">
    <value-of select="func:nextpart(@name)"/>
    <xsl:text><![CDATA[<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Type wrapper
   *
   * @purpose  Specialized SOAP type
   */
  class ]]></xsl:text>
  <xsl:value-of select="func:ucfirst(concat($prefix, @name))"/>
  <xsl:text> extends Object {
    public</xsl:text>
    <xsl:for-each select="xsd:all/xsd:element">
      $<xsl:value-of select="@name"/>
      <xsl:if test="position() &lt; last()">,</xsl:if>
    </xsl:for-each>
    <xsl:text>;&#10;</xsl:text>
    
    <xsl:for-each select="xsd:all/xsd:element">

      <!-- Getter -->
      <xsl:text><![CDATA[
    /**
     * Retrieves ]]></xsl:text><xsl:value-of select="@name"/><xsl:text><![CDATA[
     *
     * @return  ]]></xsl:text>
      <xsl:call-template name="xpdoc:argument">
        <xsl:with-param name="type" select="@type"/>
      </xsl:call-template>
      <xsl:text>
     */
    public function get</xsl:text>
      <xsl:value-of select="func:ucfirst(@name)"/>
      <xsl:text>() {
      return $this-></xsl:text><xsl:value-of select="@name"/><xsl:text>;
    }
</xsl:text>
      
      <!-- Setter -->
      <xsl:text><![CDATA[
    /**
     * Sets ]]></xsl:text><xsl:value-of select="@name"/><xsl:text><![CDATA[
     *
     * @param   ]]></xsl:text>
      <xsl:call-template name="xpdoc:argument">
        <xsl:with-param name="type" select="@type"/>
      </xsl:call-template>
      <xsl:value-of select="@name"/>
      <xsl:text>
     */
    public function set</xsl:text>
      <xsl:value-of select="func:ucfirst(@name)"/>
      <xsl:text>($</xsl:text><xsl:value-of select="@name"/><xsl:text>) {
      $this-></xsl:text><xsl:value-of select="@name"/>
      <xsl:text>= $</xsl:text>
      <xsl:value-of select="@name"/><xsl:text>;
    }
</xsl:text>
    </xsl:for-each>
    
    <xsl:text><![CDATA[  }
?>
]]></xsl:text>
  </xsl:template>

  <!--
   ! Template that matches a complexType node that has a subnode whose
   ! name is "sequence"
   !
   ! @type   match
   !-->
  <xsl:template match="xsd:complexType[child::*[local-name() = 'sequence']]">
    <xsl:value-of select="func:nextpart(@name)"/>
    <xsl:text><![CDATA[<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Type wrapper
   *
   * @purpose  Specialized SOAP type
   */
  class ]]></xsl:text>
  <xsl:value-of select="func:ucfirst(concat($prefix, @name))"/>
  <xsl:text> extends Object {
    public</xsl:text>
    <xsl:for-each select="child::*[local-name() = 'sequence']/xsd:element">
      $<xsl:value-of select="@name"/>
      <xsl:if test="position() &lt; last()">,</xsl:if>
    </xsl:for-each>
    <xsl:text>;&#10;</xsl:text>
    
    <xsl:for-each select="xsd:sequence/xsd:element">

      <!-- Getter -->
      <xsl:text><![CDATA[
    /**
     * Retrieves ]]></xsl:text><xsl:value-of select="@name"/><xsl:text><![CDATA[
     *
     * @return  ]]></xsl:text>
      <xsl:call-template name="xpdoc:argument">
        <xsl:with-param name="type" select="@type"/>
      </xsl:call-template>
      <xsl:text>
     */
    public function get</xsl:text>
      <xsl:value-of select="func:ucfirst(@name)"/>
      <xsl:text>() {
      return $this-></xsl:text><xsl:value-of select="@name"/><xsl:text>;
    }
</xsl:text>
      
      <!-- Setter -->
      <xsl:text><![CDATA[
    /**
     * Sets ]]></xsl:text><xsl:value-of select="@name"/><xsl:text><![CDATA[
     *
     * @param   ]]></xsl:text>
      <xsl:call-template name="xpdoc:argument">
        <xsl:with-param name="type" select="@type"/>
      </xsl:call-template>
      <xsl:value-of select="@name"/>
      <xsl:text>
     */
    public function set</xsl:text>
      <xsl:value-of select="func:ucfirst(@name)"/>
      <xsl:text>($</xsl:text><xsl:value-of select="@name"/><xsl:text>) {
      $this-></xsl:text><xsl:value-of select="@name"/>
      <xsl:text>= $</xsl:text>
      <xsl:value-of select="@name"/><xsl:text>;
    }
</xsl:text>
    </xsl:for-each>
    
    <xsl:text><![CDATA[  }
?>
]]></xsl:text>
  </xsl:template>

  <!--
   ! Template that matches a complexType node
   !
   ! @type   match
   !-->
  <xsl:template match="xsd:complexType"/>

  <!--
   ! Template to match on root node
   !
   ! @type   match
   !-->
  <xsl:template match="/">
    <xsl:apply-templates select="wsdl:definitions"/>
  </xsl:template>

  <!--
   ! Template to match on port types
   !
   ! @type   match
   !-->
  <xsl:template match="wsdl:portType">
    <xsl:for-each select="wsdl:operation">      
      <xsl:text><![CDATA[
    /**
     * Invokes the method "]]></xsl:text><xsl:value-of select="@name"/><xsl:text><![CDATA["
     *
]]></xsl:text>
      <xsl:call-template name="xpdoc:arguments">
        <xsl:with-param name="for" select="wsdl:input/@message"/>
      </xsl:call-template>
      <xsl:call-template name="xpdoc:return">
        <xsl:with-param name="for" select="wsdl:output/@message"/>
      </xsl:call-template>
      <xsl:text>
<![CDATA[     * @throws  webservices.soap.SOAPFaultException in case a fault occurs
     * @throws  io.IOException in case an I/O error occurs
     * @throws  xml.FormatException in case not-well-formed XML is returned
     */
    public function ]]></xsl:text>
      <xsl:value-of select="func:lcfirst(@name)"/>
      <xsl:text>(</xsl:text>
      <xsl:call-template name="arguments">
        <xsl:with-param name="for" select="wsdl:input/@message"/>
      </xsl:call-template>
      <xsl:text><![CDATA[) {
      return $this->client->invoke(
        ']]></xsl:text>
      <xsl:value-of select="@name"/>
      <xsl:text>'</xsl:text>
      <xsl:call-template name="argumentnames">
        <xsl:with-param name="for" select="wsdl:input/@message"/>
      </xsl:call-template>
      <xsl:text>
      );
    }
</xsl:text>
    </xsl:for-each>
  </xsl:template>
  
  <!--
   ! Template for WSDL definitions
   !
   ! @type   match
   !-->
  <xsl:template match="wsdl:definitions">
    <xsl:variable name="types" select="wsdl:types/xsd:schema/xsd:complexType[child::*[local-name() = 'sequence' or local-name() = 'all']]"/>

    <!-- User-defined types -->
    <xsl:apply-templates select="$types"/>
    
    <!-- The service itself -->
    <xsl:value-of select="func:nextpart(func:class(wsdl:service/@name, 'Client'))"/>
    <xsl:text><![CDATA[<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'webservices.soap.SoapDriver',
    'webservices.soap.Parameter',
    'util.log.Traceable'
  );
  
  /**
]]></xsl:text>
    <xsl:call-template name="xpdoc:comment">
      <xsl:with-param name="string" select="/comment()"/>
    </xsl:call-template>
    <xsl:text><![CDATA[   *
   * @purpose  SOAP service wrapper class
   */  
  class ]]></xsl:text>
    <xsl:value-of select="func:class(concat($prefix, wsdl:service/@name))"/>
    <xsl:text><![CDATA[Client extends Object implements Traceable {
    protected
      $client = NULL;
    
    /**
     * Constructor
     *
     * @param   string endpoint default ']]></xsl:text>
    <xsl:value-of select="wsdl:service/wsdl:port/soap:address/@location"/>
    <xsl:text><![CDATA['
     */
    public function __construct($endpoint= ']]></xsl:text>
    <xsl:value-of select="wsdl:service/wsdl:port/soap:address/@location"/>
    <xsl:text><![CDATA[') {
      $this->client= SoapDriver::getInstance()->forEndpoint($endpoint, ']]></xsl:text><xsl:value-of select="@targetNamespace"/><xsl:text>');
</xsl:text>
    <xsl:for-each select="$types">
      <xsl:text>
      $this->client->registerMapping(
        new QName('</xsl:text>
      <xsl:value-of select="../@targetNamespace"/>
      <xsl:text>', '</xsl:text>
      <xsl:value-of select="@name"/>
      <xsl:text>'), 
        XPClass::forName('</xsl:text>
      <xsl:value-of select="concat($collection, '.', func:ucfirst(concat($prefix, @name)))"/>
      <xsl:text>')
      );</xsl:text>
    </xsl:for-each>
    <xsl:text>
    }
    
    /**
     * Set trace
     *
     * @param   util.log.LogCategory
     */
    public function setTrace($cat) {
      $this->client->setTrace($cat);
    }
</xsl:text>
    <xsl:apply-templates select="wsdl:portType"/>
    <xsl:text><![CDATA[  }
?>
]]></xsl:text>
    <xsl:value-of select="concat('------_=_NextPart_', $boundary, '--&#10;')"/>
  </xsl:template>
</xsl:stylesheet>
