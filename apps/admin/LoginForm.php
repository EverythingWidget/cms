<?php
session_start();
include($_SESSION['ROOT_DIR'] . '/config.php');
$userName = mysql_real_escape_string($_POST['UserName']);
$password = mysql_real_escape_string($_POST['Password']);
if ($_POST['UserName'])
{
    $result = mysql_query("SELECT * FROM users WHERE user_name = '$userName' AND password = '$password' LIMIT 1") or die(mysql_error());
    while ($row = mysql_fetch_array($result))
    {
        $_SESSION['login'] = '1';
        $_SESSION['userName'] = $userName;
        ?>
        <span class="Title" style="color: #339900;" >
            شما با موفقیت وارد شدید
        </span>
        <?php
        return;
    }
}
else
{
    ?>
    <span class="Title">
        ورود
    </span>
    <span style="width: 100%; float: right;" id="LoginFormContent">
        <?php
    }
    ?>

    <form id="LoginForm" style="width: 300px; border: 2px solid #eaeaea; margin: 20px auto; min-height: 112px; padding: 10px; overflow: hidden;" action="LoginForm.php" method="POST" onsubmit="return submitLoginForm()">
        <span class="row">
            <span class="Label" style="width: 100px;">
                نام کاربری:
            </span>
            <input class="text-field" style="width: 160px;" name="UserName" id="UserName" >
        </span>

        <span class="row">
            <span class="Label" style="width: 100px;">
                کلمه عبور:
            </span>
            <input class="text-field" style="width: 160px;" type="password" name="Password" id="Password" >
        </span>
        <input type="submit" value="ورود" name="ok" class="button green" style="margin: 0px; margin-left: 3px; float: left;" >

        <?php
        if ($_POST['UserName'])
        {
            ?>
            <span style="width: 284px; float: right; border: 2px solid #aa0000; height: 30px; line-height: 30px; padding: 5px; margin-top: 10px;; text-align: center; color: #aa0000; font-size: 16px;" >
                نام کاربری و یا کلمه عبور صحیح نمی باشد
            </span>
            <?php
        }
        ?>
    </form>
    <?php
    if (!$_POST['ok'])
    {
        ?>

    </span>
    <script lang="javascript" type="text/javascript">
        function submitLoginForm()
        {
            if(obj('UserName').value)
            {
                obj('UserName').className = 'TextField';
                loadPage('LoginForm.php', 'LoginFormContent', 'UserName='+obj('UserName').value+'&Password='+obj('Password').value , '');
            }
            else
            {
                obj('UserName').className += ' Red';
            }
            return false;
        }
                
        function checkUserName()
        {
                    
        }
    </script>
    <?php
}
?>
