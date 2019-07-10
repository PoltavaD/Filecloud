<?php
class Auth extends CI_Controller
{
    public function index()

    {
        $this->load->view('header');
        $this->load->view('home');
        $this->load->view('footer');
    }

    public function login()

    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        session_name('filecloud');
        session_start();

        $conn = mysqli_connect(
            'localhost',
            'root',
            '',
            'filecloud'
        );

        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        };

        if (isset($_GET['login']) && isset($_GET['pass'])) {
            $login = $_GET['login'];
            $pass = $_GET['pass'];
        }

        if ($login == '' or $pass == ''){
            header('location: /auth');
            exit('Поля не могут быть пустыми!');
        }

        $sql = 'select * from users where login="'.$login.'"';

        $result = mysqli_query(
            $conn,
            $sql
        );

        $row = mysqli_fetch_assoc($result);

        if (isset($row['pass']) && $row['pass'] != '') {
            $pass = password_verify($pass, $row['pass']);
        } else {
            mysqli_close($conn);
            header('location: /auth/logout');
            exit();
        }

        if(!$pass) {
            ?><div><a href="/auth">Не верный пароль!</a></div><br><?
        }

        if(!$result->num_rows) {
            mysqli_close($conn);
            ?><div><a href="/auth">Зарегестрируйтесь!</a></div><br><?
        } elseif ($pass) {
            $_SESSION['auth'] = 'ok';
            $_SESSION['id'] = $row['id'];
            mysqli_close($conn);
            header('location: /files/getFile');
            exit();
        }
    }

    public function singup()

    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        session_name('filecloud');
        session_start();

        $conn = mysqli_connect(
            'localhost',
            'root',
            '',
            'filecloud'
        );

        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        };

        if (isset($_GET['pass']) && isset($_GET['pass2'])){
            $pass = $_GET['pass'];
            $pass2 = $_GET['pass2'];
        }

        if ($pass != $pass2) {
            mysqli_close($conn);
            echo '<a href="/auth">Пароли не совпадают!</a>';
            exit();
        }

        if (isset($_GET['login']) && isset($_GET['pass'])) {
            $login = $_GET['login'];
            $pass = $_GET['pass'];
        }

        $pass = password_hash($pass, PASSWORD_DEFAULT);

        $sql = 'select * from users where login="'.$login.'"';

        $result = mysqli_query(
            $conn,
            $sql
        );

        $sql_ins = "insert into users (`login`,`pass`) values ('" . $login . "','" . $pass . "')";

        if($result->num_rows) {
            echo '<a href="/auth">Такой пользователь уже есть</a>';
            exit();
        } else {
            mysqli_query($conn, $sql_ins);
            $_SESSION['auth'] = 'ok';
            $_SESSION['id'] = mysqli_insert_id($conn);
            mysqli_close($conn);
            header('location: /files/getFile');
            exit();
        }
    }

    public function logout()

    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        session_name('filecloud');
        session_start();
        session_destroy();
        header('location: /auth');
    }
}
