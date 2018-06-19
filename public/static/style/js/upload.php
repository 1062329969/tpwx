<?php
$file_path = "../uploads/";

if (isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) 
{
    $upload_file = $_FILES['Filedata'];
    $file_info = pathinfo($upload_file['name']);
    $file_type = $file_info['extension'];
    $file_path = $file_path.date("Ymd")."/";
    if(!is_dir($file_path))
    {
        mkdir($file_path);
    }
    $save = $file_path . md5(uniqid($_FILES["Filedata"]['name'])) . '.' . $file_info['extension'];
    $name = $_FILES['Filedata']['tmp_name'];
    if (!move_uploaded_file($name, $save)) {
        exit;
    }
    $save = "/static/style/".substr($save, 2);
    echo $save;
}

exit;
?>