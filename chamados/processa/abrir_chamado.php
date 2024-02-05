<?php
session_start();

// Verifica se o usuário está autenticado
if (isset($_SESSION['id'])) {
    // Verifica se o formulário foi submetido via método POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verifica se todas as variáveis do formulário estão definidas e não estão vazias
        if (!empty($_POST['servico']) && !empty($_POST['chamado']) && !empty($_POST['item']) && !empty($_POST['relato'])) {
            // Inclui o arquivo de conexão com o banco de dados
            require "../../conexoes/conexao_pdo.php";

            try {
                // Obtém o tipo do chamado
                $id_tipo_chamado = $_POST['chamado'];
                $tipo_chamado_query = $pdo->prepare("SELECT tipo FROM tipos_chamados WHERE id = :id_tipo_chamado");
                $tipo_chamado_query->bindParam(':id_tipo_chamado', $id_tipo_chamado);
                $tipo_chamado_query->execute();
                $tipo_chamado_result = $tipo_chamado_query->fetch(PDO::FETCH_ASSOC);

                $assuntoChamado = $tipo_chamado_result['tipo'] . ' - ' . $_SESSION['nome_usuario'];

                // Prepara a instrução SQL de inserção
                $stmt = $pdo->prepare("INSERT INTO chamados (assuntoChamado, relato_inicial, tipochamado_id, solicitante_id, empresa_id, data_abertura, seconds_worked, status_id, atendente_id, service_id, iten_service_id, in_execution, in_execution_atd_id) VALUES (:assuntoChamado, :relato_inicial, :tipochamado_id, :solicitante_id, :empresa_id, NOW(), '0', '1', '0', :service_id, :iten_service_id, '0', '0')");

                $stmt->bindParam(':assuntoChamado', $assuntoChamado);
                $stmt->bindParam(':relato_inicial', $_POST['relato']);
                $stmt->bindParam(':tipochamado_id', $_POST['chamado']);
                $stmt->bindParam(':solicitante_id', $_SESSION['id']);
                $stmt->bindParam(':empresa_id', $_SESSION['empresa_id']);
                $stmt->bindParam(':service_id', $_POST['servico']);
                $stmt->bindParam(':iten_service_id', $_POST['item']);

                // Executa a instrução SQL
                if ($stmt->execute()) {
                    $ultimo_id = $pdo->lastInsertId();
                    // Redireciona para a página de visualização do chamado usando o ID inserido
                    header("Location: /chamados/visualizar_chamado.php?id=$ultimo_id");
                    exit();
                } else {
                    header('Location: /index.php');
                    exit();
                }
            } catch (PDOException $e) {
                echo "Erro ao executar a consulta: " . $e->getMessage();
            }
        } else {
            echo "Todos os campos do formulário devem ser preenchidos.";
        }
    } else {
        // Se o método de requisição não for POST, redireciona para a página inicial
        header('Location: /index.php');
        exit();
    }
} else {
    // Se o usuário não estiver autenticado, redireciona para a página de login
    header('Location: /index.php');
    exit();
}
