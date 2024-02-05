<?php
require '../includes/menu.php';
require '../conexoes/conexao_pdo.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<style>
    .closed {
        background-color: #c0c0c0;
        border-radius: 3px;
        /* Adiciona bordas arredondadas */
        border: 1px solid #d3d3d3;
        /* Adiciona contorno preto */

        color: black;
    }

    #closed {
        background-color: #c0c0c0;
        border-radius: 3px;
        /* Adiciona bordas arredondadas */
        border: 1px solid #d3d3d3;
        /* Adiciona contorno preto */

        color: black;
    }
</style>

<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <br>
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            <?php
                            // Consulta para selecionar os chamados do solicitante atual
                            $stmt_chamados = $pdo->prepare(
                                "SELECT c.id as id, c.assuntoChamado as assunto, p.nome as atendente, c.relato_inicial, DATE_FORMAT(c.data_abertura, '%d/%m/%Y %H:%i') as data_abertura, s.service as service, ise.item as itemService, c.in_execution as inExecution

                            FROM chamados as c
                            LEFT JOIN usuarios as u ON u.id = c.atendente_id
                            LEFT JOIN pessoas as p ON p.id = u.pessoa_id
                            LEFT JOIN contract_service as cser ON cser.id = c.service_id
                            LEFT JOIN service as s ON s.id = cser.service_id
                            LEFT JOIN contract_iten_service as cis ON cis.id = c.iten_service_id
                            LEFT JOIN iten_service as ise ON ise.id = cis.iten_service                            
                            WHERE c.solicitante_id = :solicitante_id AND status_id =3 
                            ORDER BY c.id DESC"
                            );
                            $stmt_chamados->bindParam(':solicitante_id', $_SESSION['id']);
                            $stmt_chamados->execute();

                            // Verifica se há resultados
                            if ($stmt_chamados->rowCount() > 0) {
                                // Itera sobre os resultados para exibir cada chamado em um acordeão
                                while ($row_chamado = $stmt_chamados->fetch(PDO::FETCH_ASSOC)) {

                            ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-heading<?= $row_chamado['id']; ?>">
                                            <button class="accordion-button collapsed closed" id="closed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?php echo $row_chamado['id']; ?>" aria-expanded="false" aria-controls="flush-collapse<?php echo $row_chamado['id']; ?>">
                                                <?php echo '#' . $row_chamado['id'] . ' ' . $row_chamado['assunto']; ?>
                                            </button>

                                        </h2>
                                        <div id="flush-collapse<?php echo $row_chamado['id']; ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading<?php echo $row_chamado['id']; ?>" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">
                                                <strong>Data Abertura:</strong>
                                                <?php echo $row_chamado['data_abertura']; ?><br>

                                                <strong>Atendente:</strong>
                                                <?php echo $row_chamado['atendente'] !== null ? $row_chamado['atendente'] : 'Sem Atendente'; ?><br>

                                                <strong>Serviço:</strong>
                                                <?php echo $row_chamado['service']; ?><br>

                                                <strong>Item:</strong>
                                                <?php echo $row_chamado['itemService'] !== null ? $row_chamado['itemService'] : 'Sem Item'; ?><br><br>


                                                <strong>Relato Abertura:</strong><br>
                                                <?php echo nl2br($row_chamado['relato_inicial']); ?><br>

                                                <br>
                                                <div class="text-center">

                                                    <a href="/chamados/visualizar_chamado.php?id=<?= $row_chamado['id']; ?>" class="btn btn-sm btn-danger">Ver Chamado</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }
                            } else {
                                // Se não houver chamados, exibe uma mensagem indicando isso
                                ?>
                                <div class="alert alert-info" role="alert">
                                    Nenhum chamado encontrado.
                                </div>
                            <?php
                            }
                            ?>
                        </div><!-- End Accordion without outline borders -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
require '../includes/footer.php';
?>