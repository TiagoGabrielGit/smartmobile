<?php
// Verifica se a solicitação foi feita via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['serviceID'])) {
    // Extrai o ID do serviço da solicitação
    $serviceID = $_GET['serviceID'];

    // Inclua o arquivo de configuração do banco de dados
    require '../conexoes/conexao_pdo.php';

    try {
        // Prepara a consulta SQL para obter os itens com base no ID do serviço
        $stmt = $pdo->prepare("
            SELECT
                cis.id as idContractItemService,
                ise.item as item
            FROM
                contract_iten_service as cis
            LEFT JOIN
                contract_service as cs ON cis.contract_service_id = cs.id
            LEFT JOIN
                iten_service as ise ON ise.id = cis.iten_service
            WHERE
                cis.active = 1
            AND
                cs.active = 1
            AND
                cs.id = :serviceID
            ORDER BY
                ise.item ASC
        ");
        $stmt->bindParam(':serviceID', $serviceID);
        $stmt->execute();

        // Inicializa um array para armazenar os itens
        $items = array();

        // Verifica se há resultados
        if ($stmt->rowCount() > 0) {
            $items = array(array('idContractItemService' => '', 'item' => 'Selecione o serviço'));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $items[] = $row;
            }
        } else {
            // Se não houver itens disponíveis, define um item especial indicando isso
            $items[] = array('idContractItemService' => '', 'item' => 'Nenhum item encontrado');
        }

        // Retorna os itens como JSON
        echo json_encode($items);
    } catch (PDOException $e) {
        // Em caso de erro, retorna uma mensagem de erro
        echo json_encode(array('error' => 'Erro ao executar a consulta: ' . $e->getMessage()));
    }
} else {
    // Se a solicitação não for feita via AJAX ou se o ID do serviço não estiver definido, retorna uma mensagem de erro
    echo json_encode(array('error' => 'Solicitação inválida.'));
}
?>