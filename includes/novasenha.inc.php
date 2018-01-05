<?php
session_start();

if(isset($_POST['submit']) && isset($_SESSION['u_id'])){
        include_once 'dbh.inc.php';
        $uid = mysqli_real_escape_string($conn, $_SESSION['u_uid']);
        $pwd = mysqli_real_escape_string($conn, $_POST['senhaantiga']);
        $newpwd = mysqli_real_escape_string($conn, $_POST['senhanova']);
        $newhashedpwd = password_hash($newpwd, PASSWORD_DEFAULT);
        //recebe da sql a tabela de usuários
        $sql = "SELECT * FROM users WHERE user_uid='$uid'";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if ($resultCheck < 1) {
            //echo $_SESSION['u_uid']; die();
            header("Location: ../home.php?newpassword=noUserFound");
            exit();
        } else {
            //faz a váriavel row receber a linha do resultado da pesquisa
            if ($row = mysqli_fetch_assoc($result)) {
                //desembaralhando a senha e comparando
                $hashedPwdCheck = password_verify($pwd, $row['user_pwd']);
                //se a senha estiver incorreta
                if ($hashedPwdCheck == false) {
                    header("Location: ../home.php?newpassword=error");
                    exit();
                } elseif ($hashedPwdCheck == true) {
                    $sql = "UPDATE users SET user_pwd='$newhashedpwd' WHERE user_uid='$uid'";
                    mysqli_query($conn, $sql);
                    $mensagemLog = "O usuário ".$uid." mudou de senha";
                    $sqlLogRegister = "INSERT INTO log(description) VALUES '$mensagemLog'";
                    header("Location: ../home.php?newpassword=sucess");
                    exit();
                }
            }
        }
//se a url foi acessada diretamente
} else {
    header("Location: ../home.php?login=error");
    exit();
}
?>
