<!DOCTYPE HTML>
<html>
<head>
    <META content='no-cache' http-equiv='Pragma'>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link id = "dynamic-favicon" rel="SHORTCUT ICON" href="/assets/icons/icon_emd_round.png" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="/libs/fontawesome/css/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="/libs/bootstrap/css/bootstrap.min.css?v=4.4.1"/>
    <script type="text/javascript" src="/libs/jquery/js/jquery-3.6.1.min.js"></script>
    <script type="text/javascript" src="/libs/webtoolkit/md5.js"></script>
    <style type="text/css">
        #Login {
            width: 500px;
            margin: auto;
        }

        #Form {
            margin: 0 40px;
        }

        body, form, div {
            font-family: tahoma;
            font-size: 12px;
        }

        .cls-footer {
            font-size: 11px;
            font-style: italic;
            color: #808080;
        }

        .lbl-remember {
            display: inline-block;
            margin-left: 20px;
            vertical-align: middle;
            font-size: 11px;
            font-weight: normal;
        }

        #Remember {
            margin: 0;
            padding: 0;
            position: absolute;
        }

        .notify {
            font-size: 11px;
            font-style: italic;
            font-weight: normal;
        }
    </style>
    <script type="text/javascript">
        <!--
        function inIframe() {
            try {
                return window.self !== window.top;
            } catch (e) {
                return true;
            }
        }
        if (inIframe()) {
            window.top.location = window.top.location;
        }

        document.addEventListener("DOMContentLoaded", function() {
            const hostname = window.location.hostname

            let dynamicTitle = "Emddi | Quản lý hệ thống đặt và điều vận xe | NAT 2.0.0"

            let faviconUrl = "/assets/icons/icon_emd_round.png"

            let logoSrc = "/assets/icons/emddi_logo2.png"

            let poweredByText = "Emddi Web Version 2.0.0 © 2025 - Powered by Emddi Co.,Ltd"

            if (hostname.indexOf("vngo") != -1) {
                faviconUrl = "/assets/icons/vngo_favicon.png"
                dynamicTitle = "VNGO | Quản lý hệ thống đặt và điều vận xe | NAT 2.0.0"
                logoSrc = "/assets/icons/vngo_logo_main.jpg"
                poweredByText = "VNGO Web Version 2.0.0 © 2025 - Powered by VNGO Co.,Ltd"
            }

            document.getElementById("dynamic-favicon").href = faviconUrl

            document.getElementById("dynamic-logo").src = logoSrc;

            document.getElementById("powered-by").innerHTML = poweredByText;

            document.title = dynamicTitle;
        });

        //-->
    </script>
</head>
<body onresize="_rs();" ;>
<center>
    <div id="Login" class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title"><b>QUẢN LÝ HỆ THỐNG ĐẶT VÀ ĐIỀU VẬN XE</b></h3>
        </div>
        <div class="panel-body">
            <table>
                <?php
                if (isset($_GET["login"])) {
                    if ($_GET["login"] == 'failed') { ?>
                        <tr>
                            <td colspan="2" align="center"><span class="label label-danger notify">Sai tên đăng nhập hoặc mật khẩu !!!</span><br><br>
                            </td>
                        </tr>
                    <?php } else if ($_GET["login"] == 'captcha-failed') { ?>
                        <tr>
                            <td colspan="2" align="center"><span class="label label-danger notify">Captcha không đúng !!!</span><br><br>
                            </td>
                        </tr>
                    <?php } else {
                        ?>
                        <tr>
                            <td colspan="2" align="center"><span class="label label-danger notify">Bạn chưa được cấp quyền truy cập hệ thống !!!</span><br>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr>
                    <td><img id = "dynamic-logo" src="/assets/icons/emddi_logo2.png" style="width:150px"></td>
                    <td>
                        <form id="Form" method='post' action='login.php'>
                            <input type="hidden" name="MachineID" id="MachineID" value="">
                            <div class="form-group" style="width:100%">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-user"></i>&nbsp;</div>
                                    <input type="text" class="form-control" id="Username" name="user_name"
                                           placeholder="Tên đăng nhập" style="width:225px;font-size:12px">
                                </div>
                            </div>
                            <div class="form-group" style="width:100%">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-key"></i></div>
                                    <input type="password" class="form-control" id="Password" name="password"
                                           placeholder="Mật khẩu" style="width:225px;font-size:12px">
                                </div>
                            </div>
                            <div class="form-group" style="width:100%">
                                <div class="input-group">
                                    <input id='Remember' type="checkbox"> <label for='Remember' class='lbl-remember'>Nhớ
                                        tên truy cập</label>
                                </div>
                            </div>
                            <br/>
                            <div class="form-group" style="width:100%">
                                <div class="input-group">
                                    <button type="button" onclick="login();" class="btn btn-primary"
                                            style="font-size:12px;font-weight:bold;width:265px">ĐĂNG NHẬP HỆ THỐNG
                                    </button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
        <div id = "powered-by" class="panel-footer cls-footer">Emddi Web Version 2.0.0 © 2025 - Powered by Emddi Co.,Ltd</div>
    </div>
    <!--[if lt IE 9]>
    <script src="../libs/bootstrap/js/respond.min.js"></script>
    <script src="../libs/jquery/js/jquery.placeholder.js"></script>
    <script src="ie.js"></script>
    <![endif]-->
</center>
</body>
</html>
<script type="text/javascript">

    function _rs() {
        var margin = ($(document).height() - $("#Login").height()) / 2 - 25;
        $("#Login").css('margin-top', margin + 'px');
    }
    $("#Username").focus();
    _rs();

    function login() {
        /* for remember */
        setCookie("puppy", $("#Remember").is(':checked'), 365);
        if ($("#Remember").is(':checked')) {
            setCookie("myuser", $("#Username").val(), 365);
            setCookie("mypass", '', 365);
        }
        /* do Submit */
        $("#Form").submit()
    }
    $("#Username")
        .off('keyup')
        .on('keyup', function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                $("#Password").focus();
            }
        });
    $("#Password")
        .off('keyup')
        .on('keyup', function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                login();
            }
        });

    if (getCookie("puppy") == "true") {
        $("#Remember").attr('checked', true);
        $("#Username").val(getCookie("myuser"));
        $("#Password").val("");
    } else {
        $("#Username").val("");
        $("#Password").val("");
    }

</script>
