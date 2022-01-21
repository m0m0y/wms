<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    /* Exception class. */
    require 'PHPMailer\src\Exception.php';

    /* The main PHPMailer class. */
    require 'PHPMailer\src\PHPMailer.php';

    /* SMTP class, needed if you want to use SMTP. */
    require 'PHPMailer\src\SMTP.php';

    
    /* $mail = new PHPMailer(); 
    $mail->IsSMTP(); 
    $mail->SMTPDebug = 0; 
    $mail->SMTPAuth = true; 
    $mail->Host = "smtp.inmed.com.ph";
    $mail->Username = "inquiry@inmed.com.ph";
    $mail->Password = "Inmed123!";
    $mail->IsHTML(true); */

   


    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug = 0;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465;
    $mail->IsHTML(true);
    $mail->Username = "pmcmailchimp@gmail.com";
    $mail->Password = "1_pmcmailchimp@gmail.com";
    

    $fname      =    "";
    $lname      =    "";

    $name       = '';

    $mail->SetFrom("inquiry@inmed.com.ph", "" . $name );
    $mail->Subject = "Warranty Registration - " . $name ;

    $email      =   '';
    
    $saddress   =    '';
    $city       =    '';
    $state      =    '';
    
    $zip        =    '';
    $country    =    '';

    $address    =   '';

    $fax      =    '';
    $mobile      =    '';

    $phone      =    '';
    
    $item      =    '';

    $pfrom      =    '';

    $pprice      =    '';
    $pdate      =    '';
    $snumber      =    '';
    $age      =    '';

    if (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']) {
        $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    }
    else {
        $ip = 'IP Not set';
    }

    $htmlMessage = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Inmed - Warranty Registration</title>
                <style>
                    html,
                    body,
                    table,
                    tbody,
                    tr,
                    td,
                    div,
                    p,
                    ul,
                    ol,
                    li,
                    h1,
                    h2,
                    h3,
                    h4,
                    h5,
                    h6 {
                        margin: 0;
                        padding: 0;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                        font-size: 0;
                        line-height: 0;
                        -ms-text-size-adjust: 100%;
                        -webkit-text-size-adjust: 100%;
                    }
                    table {
                        border-spacing: 0;
                        mso-table-lspace: 0pt;
                        mso-table-rspace: 0pt;
                    }
                    table td {
                        border-collapse: collapse;
                    }
                    .ExternalClass {
                        width: 100%;
                    }
                    .ExternalClass,
                    .ExternalClass p,
                    .ExternalClass span,
                    .ExternalClass font,
                    .ExternalClass td,
                    .ExternalClass div {
                        line-height: 100%;
                    }
                    /* Outermost container in Outlook.com */
                    .ReadMsgBody {
                        width: 100%;
                    }
                    img {
                        -ms-interpolation-mode: bicubic;
                    }
                    h1,
                    h2,
                    h3,
                    h4,
                    h5,
                    h6 {
                        font-family: Arial;
                    }
                    h1 {
                        font-size: 28px;
                        line-height: 32px;
                        padding-top: 10px;
                        padding-bottom: 24px;
                    }
                    h2 {
                        font-size: 24px;
                        line-height: 28px;
                        padding-top: 10px;
                        padding-bottom: 20px;
                    }
                    h3 {
                        font-size: 20px;
                        line-height: 24px;
                        padding-top: 10px;
                        padding-bottom: 16px;
                    }
                    p {
                        font-size: 16px;
                        line-height: 20px;
                        font-family: Georgia, Arial, sans-serif;
                    }
                    </style>
                    <style>
                        
                    .container600 {
                        width: 600px;
                        max-width: 100%;
                    }
                    @media all and (max-width: 599px) {
                        .container600 {
                            width: 100% !important;
                        }
                    }
                </style>

                <!--[if gte mso 9]>
                    <style>
                        .ol {
                        width: 100%;
                        }
                    </style>
                <![endif]-->

            </head>
            <body style="background-color:#F4F4F4;">
                <center>

                    <!--[if gte mso 9]><table width="600" cellpadding="0" cellspacing="0"><tr><td>
                                <![endif]-->
                <table class="container600" cellpadding="0" cellspacing="0" border="0" width="100%" style="width:calc(100%);max-width:calc(600px);margin: 0 auto;">
                    <tr>
                    <td width="100%" style="text-align: left;">

                        <table width="100%" cellpadding="0" cellspacing="0" style="min-width:100%;">
                            <tr>
                                <td style="background-color:#FFFFFF;color:#000000;padding:30px;">
                                    <img alt="Inmed Corporation" src="http://inmed.com.ph/assets/inmed%20logo.png" width="200" style="display: block;" />
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellpadding="0" cellspacing="0" style="min-width:100%;">
                            <tr>
                                <td style="background-color:#F8F7F0;color:#58585A;padding:30px;">

                                    <h1 style="text-align:center;">Inmed Warranty Registration</h1>
                                    <p> </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:20px;background-color:#F8F7F0;">

                                        <table width="100%" cellpadding="0" cellspacing="0" style="min-width:100%;">
                                            <thead>
                                            <tr>
                                                <th scope="col" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;line-height:30px;"></th>
                                                <th scope="col" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;line-height:30px;"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            
                                            <tr>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">Name: </td>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">'. $name .'</td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">Email: </td>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">'. $email .'</td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">Address :</td>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">'. $address . '</td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">Phone:</td>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">'. $fax . '</td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">Mobile:</td>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">'. $mobile . '</td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">Item :</td>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">' . $item . '</td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">Purchased from:</td>
                                                <td valign="top" style="padding:5px; font-family: Arial,sans-serif; font-size: 16px; line-height:20px;">' . $pfrom . '</td>
                                            </tr>
                                            
                                            </tbody>
                                        </table>

                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellpadding="0" cellspacing="0" style="min-width:100%;">
                            <tr>
                                <td width="100%" style="min-width:100%;background-color:#58585A;color:#ffffff;padding:30px;">
                                    <p style="font-size:12px;line-height:20px;font-family: Arial,sans-serif;text-align:center;">Sender\'s IP: '. $ip .'</p>
                                </td>
                            </tr>
                        </table>
                        </td>
                    </tr>
                </table>

            <!--[if gte mso 9]></td></tr></table>
                                <![endif]-->
                </center>
            </body>
            </html>
            ';
                
    $mail->Body = $htmlMessage;
    
    /* $mail->AddAddress("sales@inmed.com.ph"); */
    $mail->AddAddress("jericopresentacion8@gmail.com");
   
    if(!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        // header("Location: http://inmed.com.ph/pages/thankyou.html");
        echo 'sent';
        exit;
    }
?>
