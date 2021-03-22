<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:import href="layout.xsl?01"/>
    <xsl:param name="css">
        <link href="/css/main.css{$template-version}" rel="stylesheet" type="text/css"></link>
    </xsl:param>
    <xsl:param name="javascript">
        <script type="text/javascript" language="javascript" src="{concat('/script/AutoForm/autoform.js',$template-version)}"/>
    </xsl:param>
    <xsl:param name="account" select="document('/authentication/account.xml')/authentication/account"/>
    <xsl:template match="main|content" mode="body">
        <header>
            <a href="/"><img src="/favicon.ico"/></a>
            <h1>Lazy Framework Demo</h1>
        </header>
        <article>
            <xsl:choose>
                <xsl:when test="$account">
                    <p>Hello, <strong><xsl:value-of select="substring-before($account/@mail, '@')"/></strong>. You're successfully logged in!</p>
                    <form name="logout" method="post" action="/authentication/logout.xml">
                        <input id="logoutSubmit" type="submit" value="Logout"/>
                        <div></div>
                    </form>
                </xsl:when>
                <xsl:otherwise>
                    <input type="radio" name="user" value="login" id="loginForm" checked="checked"/>
                    <form name="login" method="post" action="/authentication/login.xml">
                        <ul>
                            <li>Please login with your email and password, or register first.</li>
                            <li><input id="Login" type="text" name="Login" placeholder="Your email"/></li>
                            <li><input id="Password" type="password" name="Password" placeholder="Your password"/></li>
                            <li><input id="loginSubmit" type="submit" value="Login"/><label for="registerForm">Register new user</label></li>
                        </ul>
                        <div></div>
                    </form>
                    <input type="radio" name="user" value="register" id="registerForm"/>
                    <form name="register" method="post" action="/authentication/register.xml">
                        <ul>
                            <li>Register new account.</li>
                            <li><input id="Login2" type="text" name="Login" placeholder="Your email"/></li>
                            <li><input id="Password3" type="password" name="Password" placeholder="Your password"/></li>
                            <li><input id="Password2" type="password" name="Password2" placeholder="Retype your password"/></li>
                            <li><input id="registerSubmit" type="submit" value="Register"/><label for="loginForm">Login</label></li>
                        </ul>
                        <div></div>
                    </form>
                </xsl:otherwise>
            </xsl:choose>
        </article>
    </xsl:template>
</xsl:stylesheet>
