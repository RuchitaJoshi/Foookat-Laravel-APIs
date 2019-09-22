<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><!--[if IE]>
<html xmlns="http://www.w3.org/1999/xhtml" class="ie"><![endif]--><!--[if !IE]><!-->
<html style="margin: 0;padding: 0;" xmlns="http://www.w3.org/1999/xhtml"><!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/><!--<![endif]-->
    <meta name="viewport" content="width=device-width"/>
    <title>Password Reset Link</title>
    <link href="{{ URL::asset('css/custom/email-style.css') }}" rel="stylesheet">
    <!--[if !mso]><!-->
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Ubuntu:400,700,400italic,700italic);
    </style>
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,700,400italic,700italic" rel="stylesheet"
          type="text/css"/><!--<![endif]-->
    <meta name="robots" content="noindex,nofollow"/>
    <meta property="og:title" content="My First Campaign"/>
</head>
<!--[if mso]>
<body class="mso">
<![endif]-->
<!--[if !mso]><!-->
<body class="full-padding" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;">
<!--<![endif]-->
<div class="wrapper" style="min-width: 320px;background-color: #f2f2f2;" lang="x-wrapper">
    <div class="preheader"
         style="Margin: 0 auto;max-width: 560px;min-width: 280px; width: 280px;width: calc(28000% - 173040px);">
        <div style="border-collapse: collapse;display: table;width: 100%;">
            <!--[if (mso)|(IE)]>
            <table align="center" class="preheader" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width: 280px" valign="top"><![endif]-->
            <div class="snippet"
                 style="Float: left;font-size: 12px;line-height: 19px;max-width: 280px;min-width: 140px; width: 140px;width: calc(14000% - 78120px);padding: 10px 0 5px 0;color: #b8b8b8;font-family: Ubuntu,sans-serif;">

            </div>
            <!--[if (mso)|(IE)]></td>
            <td style="width: 280px" valign="top"><![endif]-->
            <div class="webversion"
                 style="Float: left;font-size: 12px;line-height: 19px;max-width: 280px;min-width: 139px; width: 139px;width: calc(14100% - 78680px);padding: 10px 0 5px 0;text-align: right;color: #b8b8b8;font-family: Ubuntu,sans-serif;">

            </div>
            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
        </div>
    </div>

    <div class="header"
         style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 173000px);"
         id="emb-email-header-container">
        <!--[if (mso)|(IE)]>
        <table align="center" class="header" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 600px"><![endif]-->
        <div class="logo emb-logo-margin-box"
             style="font-size: 26px;line-height: 32px;Margin-top: 6px;Margin-bottom: 20px;color: #c3ced9;font-family: Roboto,Tahoma,sans-serif;Margin-left: 20px;Margin-right: 20px;"
             align="center">
            <div class="logo-center" style="font-size:0px !important;line-height:0 !important;" align="center"
                 id="emb-email-header"><a href="http://foookat.com"><img
                            style="height: auto;width: 100%;border: 0;max-width: 284px;"
                            src="https://s3.ap-south-1.amazonaws.com/foookat-app/foookat-orange.png"
                            alt="" width="284"/></a></div>
        </div>
        <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
    </div>

    <div class="layout one-col fixed-width"
         style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 173000px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
        <div class="layout__inner"
             style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;"
             emb-background-style>
            <!--[if (mso)|(IE)]>
            <table align="center" cellpadding="0" cellspacing="0">
                <tr class="layout-fixed-width" emb-background-style>
                    <td style="width: 600px" class="w560"><![endif]-->
            <div class="column"
                 style="text-align: left;color: #60666d;font-size: 14px;line-height: 21px;font-family: sans-serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);">

                {{--<div style="font-size: 12px;font-style: normal;font-weight: normal;" align="center">--}}
                {{--<img class="gnd-corner-image gnd-corner-image-center gnd-corner-image-top"--}}
                {{--style="border: 0;display: block;height: auto;width: 100%;max-width: 900px;" alt="" width="600"--}}
                {{--src="images/foookat-orange1.png"/>--}}
                {{--</div>--}}

                {{--<div style="Margin-left: 20px;Margin-right: 20px;Margin-top: 20px;">--}}
                {{--<div style="line-height:10px;font-size:1px">&nbsp;</div>--}}
                {{--</div>--}}

                <div style="Margin-left: 20px;Margin-right: 20px;">
                    <h2 style="Margin-top: 0;Margin-bottom: 0;font-style: normal;font-weight: normal;color: #43464a;font-size: 17px;line-height: 26px;">
                        Hi&nbsp;{{ $user->name }},</h2>
                    <p class="size-14" style="Margin-top: 16px;Margin-bottom: 20px;font-size: 14px;line-height: 21px;"
                       lang="x-size-14">We received a request to reset the password for your account.
                        If you requested a reset for {{ $user->email }}, click the button below. If you didn’t make this
                        request, please ignore this email.&nbsp;</p>
                </div>

                <div style="Margin-left: 20px;Margin-right: 20px;">
                    <div class="btn btn--flat btn--large" style="Margin-bottom: 20px;text-align: center;">
                        <![if !mso]><a
                                style="border-radius: 4px;display: inline-block;font-size: 14px;font-weight: bold;line-height: 24px;padding: 12px 24px;text-align: center;text-decoration: none !important;transition: opacity 0.1s ease-in;color: #fff;background-color: #00afd1;font-family: sans-serif;"
                                href="{{ $link = route('password.reset.request', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}">Reset
                            Password</a><![endif]>
                        <!--[if mso]><p style="line-height:0;margin:0;">&nbsp;</p>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml"
                                     href="{{ $link = route('password.reset.request', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"
                                     style="width:236px"
                                     arcsize="9%" fillcolor="#00AFD1" stroke="f">
                            <v:textbox style="mso-fit-shape-to-text:t" inset="0px,11px,0px,11px">
                                <center style="font-size:14px;line-height:24px;color:#FFFFFF;font-family:sans-serif;font-weight:bold;mso-line-height-rule:exactly;mso-text-raise:4px">
                                    Reset Password
                                </center>
                            </v:textbox>
                        </v:roundrect><![endif]--></div>
                </div>

                <div style="Margin-left: 20px;Margin-right: 20px;">
                    <div style="line-height:10px;font-size:1px">&nbsp;</div>
                </div>

                <div style="Margin-left: 20px;Margin-right: 20px;">
                    <p class="size-14" style="Margin-top: 0;Margin-bottom: 20px;font-size: 14px;line-height: 21px;"
                       lang="x-size-14">Best Regards,<br/>
                        <strong>Team Foookat</strong></p>
                </div>

                <div style="Margin-left: 20px;Margin-right: 20px;Margin-bottom: 24px;">
                    <p style="Margin-top: 0;Margin-bottom: 0;">
                    </p></div>

            </div>
            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
        </div>
    </div>

    <div class="layout email-footer"
         style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 173000px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
        <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;">
            <!--[if (mso)|(IE)]>
            <table align="center" cellpadding="0" cellspacing="0">
                <tr class="layout-email-footer">
                    <td style="width: 400px;" valign="top" class="w360"><![endif]-->
            <div class="column wide"
                 style="text-align: left;font-size: 12px;line-height: 19px;color: #b8b8b8;font-family: Ubuntu,sans-serif;Float: left;max-width: 400px;min-width: 320px; width: 320px;width: calc(8000% - 47600px);">
                <div style="Margin-left: 20px;Margin-right: 20px;Margin-top: 10px;Margin-bottom: 10px;">
                    <table class="email-footer__links emb-web-links"
                           style="border-collapse: collapse;table-layout: fixed;">
                        <tbody>
                        <tr>
                            <td><span style="display:inline-block;padding-right: 5px;"> Follow us on: </span></td>
                            <td style="width: 26px;" class="emb-web-links ng-scope" ng-if="email.webLinks.facebook.on">
                                <span ng-if="email.webLinks.facebook.on" class="emb-content-inner ng-scope"
                                      ng-mouseenter="hovered = true;" ng-mouseleave="hovered = false;"
                                      style="display: inline-block;"><a
                                            href="https://www.facebook.com/Foookat-737838579684221"
                                            cm-tag-type="facebook" emb-disable-drag="" unselectable="on"
                                            draggable="false"><img
                                                src="https://i8.createsend1.com//static/eb/master/13-the-blueprint-3/images/facebook.png"
                                                width="26" height="26" emb-disable-drag="" unselectable="on"
                                                draggable="false"></a><div
                                            class="js-delete-controls emb-content-controls emb-reset ng-isolate-scope ng-hide"
                                            ng-show="hovered" emb-trashcan="" trashcan-key="facebook"
                                            tag-type="weblinkTag"></div></span></td>
                            <td style="width: 26px;" class="emb-web-links ng-scope" ng-if="email.webLinks.instagram.on">
                                <!-- ngIf: email.webLinks.instagram.on --><span ng-if="email.webLinks.instagram.on"
                                                                                class="emb-content-inner ng-scope"
                                                                                ng-mouseenter="hovered = true;"
                                                                                ng-mouseleave="hovered = false;"
                                                                                style="display: inline-block;"><a
                                            href="https://www.instagram.com/foookat" cm-tag-type="instagram"
                                            emb-disable-drag="" unselectable="on" draggable="false"><img
                                                src="https://i1.createsend1.com//static/eb/master/13-the-blueprint-3/images/instagram.png"
                                                width="26" height="26" emb-disable-drag="" unselectable="on"
                                                draggable="false"></a><div
                                            class="js-delete-controls emb-content-controls emb-reset ng-isolate-scope ng-hide"
                                            ng-show="hovered" emb-trashcan="" trashcan-key="instagram"
                                            tag-type="weblinkTag"></div></span></td>
                            <td style="width: 26px;" class="emb-web-links ng-scope" ng-if="email.webLinks.twitter.on">
                                <!-- ngIf: email.webLinks.twitter.on --><span ng-if="email.webLinks.twitter.on"
                                                                              class="emb-content-inner ng-scope"
                                                                              ng-mouseenter="hovered = true;"
                                                                              ng-mouseleave="hovered = false;"
                                                                              style="display: inline-block;"><a
                                            href="https://twitter.com/foookat?lang=en" cm-tag-type="twitter"
                                            emb-disable-drag="" unselectable="on" draggable="false"><img
                                                src="https://i9.createsend1.com//static/eb/master/13-the-blueprint-3/images/twitter.png"
                                                width="26" height="26" emb-disable-drag="" unselectable="on"
                                                draggable="false"></a><div
                                            class="js-delete-controls emb-content-controls emb-reset ng-isolate-scope ng-hide"
                                            ng-show="hovered" emb-trashcan="" trashcan-key="twitter"
                                            tag-type="weblinkTag"></div></span></td>
                        </tr>
                        </tbody>
                    </table>
                    <div style="Margin-top: 20px;">
                        <div>Foookat Online Services&nbsp;Pvt. Ltd. &#169; 2016</div>
                    </div>
                    <div style="Margin-top: 18px;">

                    </div>
                </div>
            </div>
            <!--[if (mso)|(IE)]></td>
            <td style="width: 200px;" valign="top" class="w160"><![endif]-->
            <div class="column narrow"
                 style="text-align: left;font-size: 12px;line-height: 19px;color: #b8b8b8;font-family: Ubuntu,sans-serif;Float: left;max-width: 320px;min-width: 200px; width: 320px;width: calc(72200px - 12000%);">
                <div style="Margin-left: 20px;Margin-right: 20px;Margin-top: 10px;Margin-bottom: 10px;">

                </div>
            </div>
            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
        </div>
    </div>
    <div style="line-height:40px;font-size:40px;">&nbsp;</div>
</div>
</body>
</html>
