<?php
require "conexoes/conexao_pdo.php";

session_start(); // Inicie a sessão antes de usar $_SESSION

function isMobileDevice()
{
  // Array de strings que indicam dispositivos móveis
  $mobileDevices = array(
    'Android', 'iPhone', 'iPad', 'BlackBerry', 'Windows Phone'
  );

  // Obtém o User-Agent do navegador
  $userAgent = $_SERVER['HTTP_USER_AGENT'];

  // Verifica se o User-Agent contém uma das strings de dispositivos móveis
  foreach ($mobileDevices as $device) {
    if (strpos($userAgent, $device) !== false) {
      return true;
    }
  }

  return false;
}

// Verifica se o acesso está sendo feito através de um dispositivo móvel
if (!isMobileDevice()) {
  echo "<p style='color:red;'>Error: Acesso permitido apenas através de dispositivos móveis.</p>";
  exit; // Encerra a execução do código
} else {
  if (empty($_POST['email']) || empty($_POST['senha'])) {
    echo "<p style='color:red;'>Erro: Por favor, preencha todos os campos obrigatórios.</p>";
  } else {
    try {
      // Prepara a consulta
      $sql_code =
        "SELECT u.id as id, p.nome as nome, p.email as email, u.senha as senha, u.empresa_id as empresa_id, u.active as active, u.reset_password as reset_password, u.mobile
      FROM usuarios as u
      LEFT JOIN pessoas as p ON p.id = u.pessoa_id
      LEFT JOIN perfil as pe ON u.perfil_id = pe.id
      WHERE p.email = :email AND u.senha = :senha";

      $stmt = $pdo->prepare($sql_code);

      // Bind dos parâmetros
      $email = $_POST['email'];
      $senha = md5($_POST['senha']);
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':senha', $senha);

      // Executa a consulta
      $stmt->execute();

      // Obtém o resultado
      $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
      $quantidade_linhas = $stmt->rowCount();

      if ($quantidade_linhas == 1) {
        if ($usuario['active'] == "1" && $usuario['mobile'] == "1") {
          $_SESSION['id'] = $usuario['id'];
          $usuario_id = $_SESSION['id'];
          $_SESSION['nome_usuario'] = $usuario['nome'];
          $_SESSION['empresa_id'] = $usuario['empresa_id'];

          $ip_address = $_SERVER['REMOTE_ADDR']; // Obter o endereço IP do usuário

          // Preparar a consulta de inserção
          $insert_log = "INSERT INTO log_acesso (usuario_id, ip_address, horario) VALUES (:usuario_id, :ip_address, NOW())";
          $stmt_insert = $pdo->prepare($insert_log);
          $stmt_insert->bindParam(':usuario_id', $usuario_id);
          $stmt_insert->bindParam(':ip_address', $ip_address);

          // Executar a consulta de inserção
          $stmt_insert->execute();

          echo "<p style='color:green;'>Code001: Acesso permitido.</p>";
        } else if ($usuario['active'] == "1" && $usuario['mobile'] == "0") {
          echo "<p style='color:red;'>Error: Usuário não habilitado para acesso mobile.</p>";
        } else {
          echo "<p style='color:red;'>Error: Usuário inativo.</p>";
        }
      } else {
        // Usuário não encontrado
        echo "<p style='color:red;'>Error: Usuário ou senha incorretos.</p>";
      }
    } catch (PDOException $e) {
      echo "Erro ao executar a consulta: " . $e->getMessage();
    }
  }
}
