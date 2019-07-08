<?
session_name('filecloud');
session_start();

if(!isset($_SESSION['auth']) or $_SESSION['auth'] != 'ok') { ?>
    <div class="signin">
        <form action="auth/login">
            <input name="login"><br>
            <input name="pass" type="password"><br>
            <button type="submit">Sing in</button>
        </form>
    </div>
    <div class="signup">
        <form action="auth/singup">
            <input name="login"><br>
            <input name="pass" type="password"><br>
            <input name="pass2" type="password"><br>
            <button type="submit">Sing up</button>
        </form>
    </div>
<? } elseif (isset($_SESSION['auth']) && $_SESSION['auth'] == 'ok' ) { ?>
    <div><a href="files/getFile">Save pictures</a></div><br>
    <div><a href="auth/logout">Logout</a></div><br>
    <div><a href="files/showFile">My pictures</a></div><br>
<? } ?>


