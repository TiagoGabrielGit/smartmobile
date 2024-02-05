<?php
session_start();

if (isset($_SESSION['id'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $chamadoID = $_POST['uploadChamadoID'];

        $host = $_SERVER['HTTP_HOST'];
        // Separa o nome do domínio do subdomínio
        $parts = explode('.', $host);
        // Remove o primeiro elemento (subdomínio) do array
        $domain = implode('.', array_slice($parts, -2));
        $domain = 'smartcontrol.' . $domain;

        $targetDirectory = '../../../' . $domain . '/uploads/chamados/chamado' . $chamadoID . '/'; // Diretório de destino

        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory, 0777, true); // Crie o diretório se não existir
        }

        $uploadedFile = $_FILES['fileInput']; // Arquivo enviado
        $fileName = $uploadedFile['name'];
        $fileSize = $uploadedFile['size']; // Tamanho do arquivo
        $targetPath = $targetDirectory . $fileName; // Caminho completo para o arquivo de destino

        // Mensagens de depuração
        echo "Target Directory: $targetDirectory<br><br>";
        echo "Uploaded File: $fileName<br><br>";
        echo "File Size: $fileSize bytes<br><br>";
        echo "Target Path: $targetPath<br><br>";

        $fileType = pathinfo($targetPath, PATHINFO_EXTENSION);
        $allowedTypes = array('jpg', 'jpeg', 'png', 'txt', 'pdf');

        // Verifique se o tipo de arquivo é permitido
        if (!in_array($fileType, $allowedTypes)) {
            echo "Tipo de arquivo não permitido.";
            exit;
        }

        // Verifique se o arquivo foi movido com sucesso para o destino
        if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
            header("Location: /chamados/visualizar_chamado.php?id=$chamadoID");
            exit;
        } else {
            // Se houver erro ao mover o arquivo, exiba o código de erro
            echo "<br>Erro ao mover o arquivo para o destino. Código de erro: " . $uploadedFile['error'] . "<br>" . $fileSize . "bytes";
            exit;
        }
    }
}
