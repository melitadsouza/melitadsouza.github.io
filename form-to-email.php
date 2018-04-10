<?php
/*
Configuration 
You are to edit these configuration values. Not all of them need to be edited.
However, the first few obviously need to be edited.
EMAIL_RECIPIENTS - your email address where you want to get the form submission.

*/

$email_recipients = "clezz5593@gmail.com";//<<=== enter your email address here
//$email_recipients =  "mymanager@gmail.com,his.manager@yahoo.com"; <<=== more than one recipients like this


$visitors_email_field = 'email';//The name of the field where your user enters their email address
                                        //This is handy when you want to reply to your users via email
                                        //The script will set the reply-to header of the email to this email
                                        //Leave blank if there is no email field in your form
$email_subject = "New Form submission";

$enable_auto_response = true;//Make this false if you donot want auto-response.

//Update the following auto-response to the user
$auto_response_subj = "Thanks for contacting me";
$auto_response ="
Hi

Thanks for contacting us. We will get back to you soon!

Regards
Melita Dsouza
";

/*optional settings. better leave it as is for the first time*/
$email_from = ''; /*From address for the emails*/
$thank_you_url = 'thank-you.html';/*URL to redirect to, after successful form submission*/

/*
This is the PHP back-end script that processes the form submission.
It first validates the input and then emails the form submission.
The variable $_POST contains the form submission data.
*/
if(!isset($_POST['submit']))
{
    // note that our submit button's name is 'submit' 
    // We are checking whether submit button is pressed
	// This page should not be accessed directly. Need to submit the form.
	echo "error; you need to submit the form!".print_r($_POST,true);
    exit;
}

require_once "includes/formvalidator.php";
//Setup Validations
$validator = new FormValidator();
$validator->addValidation("fullname","req","Please fill in Name");
$validator->addValidation("email","req","Please fill in Email");
//Now, validate the form
if(false == $validator->ValidateForm())
{
    echo "<B>Validation Errors:</B>";

    $error_hash = $validator->GetErrors();
    foreach($error_hash as $inpname => $inp_err)
    {
        echo "<p>$inpname : $inp_err</p>\n";
    }
    exit;
}

$visitor_email='';
if(!empty($visitors_email_field))
{
    $visitor_email = $_POST[$visitors_email_field];
}

if(empty($email_from))
{
    $host = $_SERVER['SERVER_NAME'];
    $email_from ="forms@$host";
}

$fieldtable = '';
foreach ($_POST as $field => $value)
{
    if($field == 'submit')
    {
        continue;
    }
    if(is_array($value))
    {
        $value = implode(", ", $value);
    }
    $fieldtable .= "$field: $value\n";
}

$extra_info = "User's IP Address: ".$_SERVER['REMOTE_ADDR']."\n";

$email_body = "You have received a new form submission. Details below:\n$fieldtable\n $extra_info";
    
$headers = "From: $email_from \r\n";
$headers .= "Reply-To: $visitor_email \r\n";
//Send the email!
@mail(/*to*/$email_recipients, $email_subject, $email_body,$headers);

//Now send an auto-response to the user who submitted the form
if($enable_auto_response == true && !empty($visitor_email))
{
    $headers = "From: $email_from \r\n";
    @mail(/*to*/$visitor_email, $auto_response_subj, $auto_response,$headers);
}

//done. 
if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
{
    //This is an ajax form. So we return success as a signal of succesful processing
    echo "success";
}
else
{
    //This is not an ajax form. we redirect the user to a Thank you page
    header('Location: '.$thank_you_url);
}
?>