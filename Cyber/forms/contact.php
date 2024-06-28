<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir o PHPMailer usando o autoloader ou manualmente
require '../assets/vendor/PHPMailer/src/Exception.php';
require '../assets/vendor/PHPMailer/src/PHPMailer.php';
require '../assets/vendor/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitização
    $name = htmlspecialchars(strip_tags(trim($_POST['name'])), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(strip_tags(trim($_POST['subject'])), ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(strip_tags(trim($_POST['message'])), ENT_QUOTES, 'UTF-8');

    // Validação do e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Por favor, forneça um endereço de e-mail válido.";
        exit;
    }

    // Verificação dos campos
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {

        $mail = new PHPMailer(true); // Cria uma instância do PHPMailer

        // Configurações do servidor
        $mail->isSMTP();                                       // Define o envio como SMTP
        $mail->Host       = 'smtp.hostinger.com';               // Servidor SMTP da Hostinger
        $mail->SMTPAuth   = true;                               // Habilita a autenticação SMTP
        $mail->Username   = 'cybercompany@smpsistema.com.br';   // Seu endereço de e-mail da Hostinger
        $mail->Password   = 'Senac@agencia02';                  // Sua senha da Hostinger
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;        // Habilita criptografia TLS
        $mail->Port       = 465;                                // Porta TCP para TLS

        // Remetente e destinatário
        $mail->setFrom('cybercompany@smpsistema.com.br', $name); // Remetente
        $mail->addAddress('biel_nando2012@hotmail.com');        // Destinatário

        // Conteúdo do e-mail
        $mail->isHTML(true);                                    // Define o formato do e-mail como HTML
        $mail->Subject = $subject;                              // Assunto do e-mail
        $mail->Body    = "Nome: $name<br>Email: $email<br><br>Mensagem:<br>$message"; // Corpo do e-mail em HTML
        $mail->AltBody = "Nome: $name\nEmail: $email\n\nMensagem:\n$message";         // Alternativa em texto simples

        // Tentativa de envio do e-mail
        if ($mail->send()) {
            // Se chegou aqui, o e-mail foi enviado com sucesso
            echo "E-mail enviado com sucesso!";
        } else {
            // Se houve algum erro, exibe a mensagem de erro
            echo "Falha ao enviar o e-mail. Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        echo "Por favor, preencha todos os campos corretamente.";
    }
} else {
    echo "Método de solicitação inválido.";
}
?>
