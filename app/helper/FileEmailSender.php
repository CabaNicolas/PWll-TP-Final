<?php

class FileEmailSender
{

    public function sendEmail($to, $subject, $mesage){

        $emailData = "To: $to\nSubject: $subject\nMessage:\n$mesage\n\n";
        file_put_contents('emails.txt', $emailData, FILE_APPEND);
    }
}