<?php
  $name;$email;$comment;$captcha;
  $to = 'web@muharif.net';
  $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
  $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
  $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
  $captcha = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
  if(!$captcha){
    echo '<h2>Please check the the captcha form.</h2>';
    exit;
  }
  $secretKey = "6Lfcq8MUAAAAAKcKQGV_DFKW9nJpfgB3A9jwqGU0";
  $ip = $_SERVER['REMOTE_ADDR'];

  // post request to server
  $url = 'https://www.google.com/recaptcha/api/siteverify';
  $data = array('secret' => $secretKey, 'response' => $captcha);

  $options = array(
    'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data)
    )
  );
  $context  = stream_context_create($options);
  $response = file_get_contents($url, false, $context);
  $responseKeys = json_decode($response,true);
  header('Content-type: application/json');
  if($responseKeys["success"]) {
    echo json_encode(array('success' => 'true'));
    $subject = 'Email from muharif.net contact form';
    $headers = "From: muharif.net contact form <mailer@ewels.co.uk>\r\nReply-To: $name <$email>\r\nX-Mailer: PHP/".phpversion();
    $message = "From: $name <$email>\nE-mail from contact form: $name\n$comment \n\n--\nSent from muharif.net contact form\n\n";
    if(mail($to, $subject, $message, $headers)){
        // $msg = 'E-mail sent. Thanks! I will get back to you as soon as possible';
        $name = $email = $subject = $message = '';
      } else {
        $error = true; $msg = "Error, could not send mail (internal error).";
      }
  } else {
    echo json_encode(array('success' => 'false'));
  }
?>
