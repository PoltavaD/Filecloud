<?

if (isset($_SESSION['auth']) && $_SESSION['auth'] == 'ok' ) {
  ?>
    <div><a href="/auth/logout">Logout</a></div>
    <br><br>
    <div>
        <form action="/files/saveFile" method="post" enctype="multipart/form-data">
            <input name="image" type="file">
            <button type="submit">Upload</button>
        </form>
    </div>
    <br><br>
    <div><a href="/files/showFile">Скачать</a></div>
  <?
} else {
    header('location: /auth');
    exit();
}