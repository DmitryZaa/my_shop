<?php if(!isset($_POST['email_addr'])) : ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <style type="text/css">
  	.wrap {
  		width: 500px;
  		margin: auto;
  		text-align: center;
  	}
  </style>
  <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
  <script>tinymce.init({ selector:'textarea' });</script>
</head>
<body>
<div class="wrap">
	  <h1>Форма для отправки email</h1>
	  <form enctype="multipart/form-data" method="post" action="">
	   <p>Загрузите ваши фотографии на сервер</p>
	   <p><input type="email" name="email_addr" placeholder="email получателя"></p>
	   <p><input type="text" name="subject" placeholder="Заголовок письма"></p>
	   <p><textarea name="body_letter">Текст письма напишите тут</textarea></p>
	   <p><input type="file" name="user_files[]" multiple></p>
	   <p><input type="submit" value="Отправить"></p>
	  </form>
</div>  
</body>
</html>
<? else : 
ini_set("max_file_uploads",9999);
ini_set("upload_max_filesize",9999);
error_reporting( E_ERROR );
header("Content-type: text/html; charset=UTF-8");

$uploaddir = 'for_uploaded_files/';

$addres = $_POST['email_addr'];
$subject = $_POST['subject'];
$body_letter = $_POST['body_letter'];
//echo $addres . $subject . $body_letter;
$filename = array();
foreach($_FILES['user_files']['name'] as $k=>$f) {
  if (!$_FILES['user_files']['error'][$k]) {
    if (is_uploaded_file($_FILES['user_files']['tmp_name'][$k])) {
      if (move_uploaded_file($_FILES['user_files']['tmp_name'][$k], $uploaddir . $_FILES['user_files']['name'][$k])) {
        
        $filename[$k] = $_FILES['user_files']['name'][$k];
        
      }
    }
  }
}

$boundary = "--" . md5(uniqid(time()));
$headers = "MIME-Version: 1.0\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
$headers .= "From: web@master.tk\n";
$multipart = "--$boundary\n";
$multipart .= "Content-Type: text/html; charset=utf-8\n";
$multipart .= "Content-Transfer-Encoding: Quot-Printed\n\n";
$multipart .= "$body_letter\n\n";
$message_part = "";
foreach ($filename as $key => $value) {
	$fp = fopen($uploaddir . $value, "r");
	$file = fread($fp, filesize($uploaddir . $value));
	$message_part .= "--$boundary\n";
	$message_part .= "Content-Type: application/octet-stream\n";
	$message_part .= "Content-Transfer-Encoding: base64\n";
	$message_part .= "Content-Disposition: attachment; filename=\"$value\"\n\n";
	$message_part .= chunk_split(base64_encode($file)) . "\n";
}
$multipart .= $message_part . "--$boundary--\n";

mail($addres, $subject, $multipart, $headers);

echo 'Сообщение отправлено <br> <a href="/form_mail.php">Отправить еще</a> ';

endif; ?>
