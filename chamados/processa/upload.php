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
            mkdir($targetDirectory, 0755, true); // Crie o diretório se não existir
        }

        $uploadedFile = $_FILES['fileInput']; // Arquivo enviado
        $fileName = $uploadedFile['name'];
        $targetPath = $targetDirectory . $fileName; // Caminho completo para o arquivo de destino

        // Verifique se o arquivo é uma imagem, arquivo de texto ou PDF (adapte as verificações de tipo conforme necessário)
        $fileType = pathinfo($targetPath, PATHINFO_EXTENSION);
        $allowedTypes = array('jpg', 'jpeg', 'png', 'txt', 'pdf');

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
                header("Location: /chamados/visualizar_chamado.php?id=$chamadoID");
                exit;
            } else {
                header("Location: /chamados/visualizar_chamado.php?id=$chamadoID");
                exit;
            }
        } else {
            header("Location: /chamados/visualizar_chamado.php?id=$chamadoID");
            exit;
        }
    }
}
