<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = 'localhost';
$dbname = $_ENV['DB_NAME_EXT'];
$username = $_ENV['USERNAME'];
$password = $_ENV['DB_PASSWORD_SELENA'];

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Підключення не вдалося: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $message = $conn->real_escape_string(trim($_POST['message']));

    $sql = "INSERT INTO ext_messages (name, email, phone, message) VALUES ('$name', '$email', '$phone', '$message')";

    if ($conn->query($sql) === TRUE) {
        $to = 'mark.digital.net.100@gmail.com';
        $subject = 'Новий запит з ext.pp.ua';
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $mailBody = "Ім'я: $name\n";
        $mailBody .= "Email: $email\n";
        $mailBody .= "Phone: $phone\n";
        $mailBody .= "Повідомлення:\n$message\n";

        if (mail($to, $subject, $mailBody, $headers)) {
            echo "Ваш запит був успішно надісланий! Ви будете перенаправлені на головну сторінку через 5 секунд.";
        } else {
            echo "Не вдалося надіслати листа. Спробуйте ще раз.";
        }
        
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'https://ext.pp.ua/';
                }, 5000);
            </script>";
    } else {
        echo "Помилка при збереженні даних." . "<a href='https://ext.pp.ua/'>Повернутися на головну сторінку</a>";
    }
}

$conn->close();
?>