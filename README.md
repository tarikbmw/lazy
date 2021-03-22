# Lazy Framework
This is a lightweight PHP8 XML/XSLT based framework. This framework supporting MySQL/Oracle databases. And simplify render to XML/JSON/HTML format.

Sample: http://www.lazyframework.ru

## Configuration file
Put below XML in cfg/Application.xml

```
<?xml version="1.0" encoding="UTF-8"?>
<!--
    name - Application or module name, used as a part of url (https://site_name/module_name/action_name.output_name), default value - `main`;
    class - Application class name, default value `\Application`;
    output - Pointer to Output XML node with same name attribute, default value `html`;
    charset - Content character encoding, default value `utf-8`;
    lang - HTML lang attribute, default value `Ru-ru`;
    database - Pointer to Database XML node with same name attribute, default value `db1`;

    You may use url with out redirect /index.php?module=module_name&action=action_name&output=output_name
-->
<Application name="main" class="\Application" output="xml" charset="utf-8" lang="Ru-ru" database="db1">
    <Session class="\Core\Session" name="PHPSESSID">PutSomeRandomStuffHere</Session>
    <WorkingDirectory path="/var/www/LazyFramework" uri="http://localhost"/>
    <!-- Output processors -->
    <Output name="html" class="\Core\Render\XSLTProcessor" mime="text/html"/>
    <Output name="xml" class="\Core\Render\XML" mime="application/xml"/>
    <Output name="json" class="\Core\Render\JSON" mime="application/json"/>
    <!-- Database handlers -->
    <Database name="db1" class="\Core\Database\MySql\Connector" hostname="p:localhost" login="root" password="root" schema="lazy" charset="utf8"/>
    <Database name="db2" class="\Core\Database\MySql\Connector" hostname="" login="" password="" schema="" charset=""/>
    <Database name="oracle" class="\Core\Database\Oracle\Connector" hostname="" login="" password="" schema="" charset=""/>
    <!-- XSL Template -->
    <Stylesheet path="/var/www/LazyFramework/public" uri="http://localhost" default="main.xsl" version="v01"/>
    <Log enabled="true" path="/srv/hosts/LazyFramework/log" name="application.log" showDate="false" filenameDateFormat="Y-m-d" dateFormat="d.m.Y H:i.s"/>
    <Cache name="default" hostname="localhost" port="0"/>
    <DefaultAction module="main" name="main" class="\Application\Action\Main"/>
    <!-- Modules definitions -->
    <Module name="main" class="\Application">
        <Action method="GET" name="main" class="Main"/>
    </Module>
    <Module name="authentication" class="\Module\Authentication">
        <Action method="POST" name="login" class="Login"/>
        <Action method="POST" name="register" class="Register"/>
        <Action method="POST" name="logout" class="Logout"/>
        <Action method="GET" name="logout" class="Logout"/>
        <Action method="GET" name="account" class="Account"/>
    </Module>
    <Module name="content" class="\Module\Content">
        <Action method="GET" name="entry" class="Entry"/>
    </Module>
</Application>
```

For demo application put this XML in cfg/Install.xml
```
<?xml version="1.0" encoding="UTF-8"?>
<Application name="install" class="\Install" output="xml" charset="utf-8" lang="Ru-ru" database="db1">
    <Session class="\Core\Session" name="PHPSESSID">NoOneLies</Session>
    <WorkingDirectory path="/var/www/LazyFramework/install" uri="http://localhost"/>
    <Output name="html" class="\Core\Render\XSLTProcessor" mime="text/html"/>
    <Output name="xml" class="\Core\Render\XML" mime="application/xml"/>
    <Database name="db1" class="\Core\Database\MySql\Connector" hostname="p:localhost" login="root" password="root" schema="lazy" charset="utf8"/>
    <Stylesheet path="/var/www/LazyFramework/install/" uri="http://localhost" default="install.xsl" version="v01"/>
    <Log enabled="true" path="/var/www/LazyFramework/log" name="install.log" showDate="false" filenameDateFormat="Y-m-d" dateFormat="d.m.Y H:i.s"/>
    <Cache name="default" hostname="localhost" port="0"/>
    <DefaultAction module="install" name="step1" class="\Install\Action\Step1"/>
    <!-- Modules definitions -->
    <Module name="install" class="\Install" successURL="http://localhost">
        <Action method="GET" name="step1"    class="\Install\Action\Step1"/>
    </Module>
</Application>
```
