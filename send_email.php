<?php
header('Content-type: application/json');
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
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
       $errors_list[] ="Tous les champs sont requis.";
    }
    if (!preg_match("/^[a-zA-Z-' ]*$/",$name)) {
        $errors_list[] = "Lettres et espace seulement pour votre nom.";
    }
    // validate email
    if (!is_valid_email($email)) {
        $errors_list[] =  "Email invalide.";
    }else{
        // Email Exists
        $to = 'contact@eliteboxing.fr';
        $from = $email;
        $headers = "From:" . $from;

        // Send the email
        if (mail($to, $subject, $content, $headers)) {
            $son = true;
        } else {
            $errors_list[] = "Quelque chose ne marche pas. Veuillez réessayer ulterieurement. Vous pouvez nous contacter directement à travers notre courriel électronique.";
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
