<?php 
    	include "class.phpmailer.php";
		
	
	$name    	=	isset($_POST['name']) 		?	$_POST['name'] 			: '';
	$mobile	    =	isset($_POST['mobile']) 	?	$_POST['mobile'] 		: '';
	$email  	=	isset($_POST['email'])		?	$_POST['email'] 		: '';
	$message	=	isset($_POST['message'])	?	$_POST['message']		: '';
   	$company	=	isset($_POST['company'])	?	$_POST['company'] 		: '';
	
	
	$body		=	'';
	$body 	   .= '<b>Name :</b>  '.$name.'</h3><br />';
	if(!empty($company))  $body .= '<b>Company:</b>  '.$company.'<br />';
	
	$body      .= '<h3>Message:</h3> '.$message.'<br /><br /><br />Thanks,<br />'.$name.',<br />'.$mobile;

	$subject	=	"Enquiry from Monarch Form";
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPDebug = 0;
	$mail->SMTPAuth = FALSE;
	$mail->SMTPSecure = "tls";
	$mail->Port     = 587;  
	$mail->Username = "Your SMTP UserName";
	$mail->Password = "Your SMTP Password";
	$mail->Host     = "localhost";
	$mail->Mailer   = "smtp";
	$mail->SetFrom($email, $name);
	$mail->AddReplyTo($email, $name);
	$mail->AddAddress("gggurpreets@gmail.com");	
	$mail->Subject = $subject;
	$mail->WordWrap = 80;
	$mail->MsgHTML($body);

	if(is_array($_FILES)) {
		$mail->AddAttachment($_FILES['attachmentFile']['tmp_name'],$_FILES['attachmentFile']['name']); 
	}
	
	$mail->IsHTML(true);

	
	if(!$mail->Send()) {
		
		echo 1;
		
		
	} else {
		
		echo 0;
	}
?>