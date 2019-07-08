<?php
class Files extends CI_Controller
{
    public function getFile()

    {
        $this->load->view('header');
        $this->load->view('getFiles');
        $this->load->view('footer');

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    public function saveFile()

    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        session_name('filecloud');
        session_start();

        if (isset($_SESSION['id'])) {
            $user_id = $_SESSION['id'];
        } else {
            header('location: /auth');
            exit();
        }

        $this->load->database();

        $this->db->select('*')->where('id', $user_id);
        $query = $this->db->get('users');

        $users = $query->result_array();

        if(empty($users)) {
            header('location: /auth');
            exit();
        }

        if($users[0]['id'] != $user_id) {
            header('location: /auth');
            exit();
        }

        if (!isset($_FILES['image']['type'])) {
            header('location: /files/getFile');
            exit('Загрузите картинку');
        } else {
            $type = explode('/', $_FILES['image']['type']);
            $name = $_FILES['image']['name'];
            if ($type[0] !== 'image') {
                header('location: /files/getFile');
                exit('Загрузите картинку');
            }
        }

        $tmp_name = explode('\\', $_FILES['image']['tmp_name']);

        $save_name = md5(time() . rand(1,99999) . $_FILES['image']['name']) . '.' . $type[1];

        $subdirName = $save_name[0];
        $subdirName2 = $save_name[1];

        if (!file_exists('./uploads/' . $subdirName . '/' . $subdirName2)){
            mkdir('./uploads/' . $subdirName . '/' . $subdirName2, 0777, true);
        }

        $upload = move_uploaded_file($_FILES['image']['tmp_name'],'./uploads/'
            . $subdirName . '/' . $subdirName2 . '/' . $save_name);

        $data = [
            'user_id' => $user_id,
            'save_name' => $save_name,
            'tmp_name' => $tmp_name[4],
            'name' => $name
        ];

        if($upload) {
            $this->db->insert('pictures', $data);
            header('location: /files/getFile');
            exit();
        }
    }

    public function showFile()

    {
        $this->load->view('header');
        $this->load->view('download');
        $this->load->view('footer');
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        session_name('filecloud');
        session_start();

        if (isset($_SESSION['id'])) {
            $user_id = $_SESSION['id'];
        } else {
            header('location: /auth');
            exit();
        }

        $this->load->database();

        $this->db->select('*')->where('user_id', $user_id);
        $query = $this->db->get('pictures');

        $pictures = $query->result_array();

        if (count($pictures) == 0) {
            header('location: /files/getFile');
            exit();
        }

        if($pictures[0]['user_id'] != $user_id) {
            header('location: /auth');
            exit();
        }

        for ( $i=0; $i<count($pictures); $i++ ) {
            $tmp_name = $pictures[$i]['tmp_name'];
            $save_name = $pictures[$i]['save_name'];
            $subdirName = $save_name[0];
            $subdirName2 = $save_name[1];
            $path = './uploads/' . $subdirName . '/' . $subdirName2 . '/' . $save_name;
            echo "<div><a href='/files/download/?path=$path'>$tmp_name</a></div><br>";
        }
    }

    public function download()

    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        session_name('filecloud');
        session_start();

        if (!isset($_SESSION['auth']) && $_SESSION['auth'] != 'ok' ) {
            header('location: /auth');
            exit();
        }

        if (isset($_GET['path'])) {
            $path = $_GET['path'];
        }

        $this->load->database();

        $this->db->where('save_name', basename($path));
        $query = $this->db->get('pictures');
        $name = $query->row_array();

        if(empty($name)) {
            header('location: /auth');
            exit();
        }

        if ($name['user_id'] != $_SESSION['id']) {
            header('location: /auth');
            exit();
        }

        if ( file_exists($path) ) {
            header("Cache-Control: public");
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename=' . $name['name']);
            header('Content-Type: octet-stream');
            header('Content-Transfer-Encoding: binary');
            readfile($path);
        }
    }
}
