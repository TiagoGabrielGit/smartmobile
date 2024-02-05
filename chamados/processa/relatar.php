<?php
session_start();
if (isset($_SESSION['id'])) {

    $relator = $_SESSION['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verifica se o ID do chamado foi enviado via POST
        if (isset($_POST['id_chamado'])) {
            // Recebe o ID do chamado
            $id_chamado = $_POST['id_chamado'];

            // Verifica se o relato foi enviado via POST
            if (isset($_POST['relato'])) {
                // Recebe o relato
                $relato = $_POST['relato'];
                require "../../conexoes/conexao_pdo.php";

                try {
                    // Prepara a consulta SQL
                    $stmt = $pdo->prepare("INSERT INTO chamado_relato (chamado_id, relator_id, relato, relato_hora_inicial, relato_hora_final, seconds_worked, private) VALUES (:id_chamado, :relator_id, :relato, now(), now(), '0', '1')");

                    // Bind dos parâmetros
                    $stmt->bindParam(':id_chamado', $id_chamado);
                    $stmt->bindParam(':relator_id', $_SESSION['id']);

                    $stmt->bindParam(':relato', $relato);

                    // Executa a consulta
                    $stmt->execute();

                    // Redireciona o usuário de volta para a página de detalhes do chamado
                    header("Location: /chamados/visualizar_chamado.php?id=$id_chamado");
                    exit; // Termina o script
                } catch (PDOException $e) {
                    echo "Erro: " . $e->getMessage();
                }
            } else {
                echo "O relato não foi enviado.";
            }
        } else {
            echo "O ID do chamado não foi enviado.";
        }
    } else {
        echo "Este arquivo deve ser acessado via método POST.";
    }
} else {
    header('Location: /index.php');
    exit();
}
?>