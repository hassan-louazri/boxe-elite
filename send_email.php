<?php
header('Content-type: application/json');
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function check_mx_record($email) {
    $domain = substr(strrchr($email, "@"), 1);
    return checkdnsrr($domain, "MX");
}

function verify_email_smtp($email) {
    list($user, $domain) = explode('@', $email);
    $mxhosts = array();
    
    if (!getmxrr($domain, $mxhosts)) {
        return false;
    }
    
    $connect = @fsockopen($mxhosts[0], 25, $errno, $errstr, 30);  // Try to open a socket to the first MX server on port 25 (SMTP)
    
    if ($connect) {
        if (preg_match('/^220/i', $out = fgets($connect))) {
            fputs($connect, "HELO example.com\r\n");
            $out = fgets($connect);
            fputs($connect, "MAIL FROM: <check@example.com>\r\n");
            $from = fgets($connect);
            fputs($connect, "RCPT TO: <$email>\r\n");
            $to = fgets($connect);
            fputs($connect, "QUIT\r\n");
            fclose($connect);
            
            if (preg_match('/^250/i', $from) && preg_match('/^250/i', $to)) {
                return true;
            }
        }
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors_list = [];
    $data = json_decode(file_get_contents('php://input'));

    $name = $data->name;
    $email = $data->email;
    $subject = htmlentities($data->subject, ENT_QUOTES );
    $content = htmlentities($data->content, ENT_QUOTES );

    $son = false;
    // NAME
    if (empty($name) || empty($email) || empty($subject) || empty($content )) {
       $errors_list[] ="Inputs are required";
    }
    if (!preg_match("/^[a-zA-Z-' ]*$/",$name)) {
        $errors_list[] = "Only letters and white space allowed";
    }
    // validate email
    if (!is_valid_email($email)) {
        $errors_list[] =  "Invalid Email Format";
    }
    if (!check_mx_record($email)) {
        $errors_list[] =  "No Record Found (email)";
    }
    if (!verify_email_smtp($email)) {
        $errors_list[] =  "Email Does Not Exist";
    }else{
        // Email Exists
        $to = 'contact@eliteboxing.fr';
        $from = $email;
        $headers = "From:" . $from;

        // Send the email
        if (mail($to, $subject, $content, $headers)) {
            $son = true;
        } else {
            $errors_list[] = "Sorry, something went wrong. Please try again later.";
        }
    }
    
    echo json_encode(['request'=>"sent successfully",
                      'errors' =>$errors_list,
                      'email_sent'=>$son]);
    exit();


    
} else {
   
    echo json_encode(['error'=>"Invalid request method."]);
    exit();
}
?>
