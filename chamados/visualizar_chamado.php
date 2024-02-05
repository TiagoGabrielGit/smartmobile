<?php
require '../includes/menu.php';
require '../conexoes/conexao_pdo.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $id = $_GET['id'];

    // Verifica se o ID é um número inteiro válido
    if (!ctype_digit($id)) {
        throw new Exception("ID inválido.");
    }

    // Consulta para selecionar o chamado com base no ID
    $stmt_chamado = $pdo->prepare(
        "SELECT c.status_id, c.id as id, c.assuntoChamado as assunto, p.nome as atendente, c.relato_inicial, DATE_FORMAT(c.data_abertura, '%d/%m/%Y %H:%i') as data_abertura, s.service as service, ise.item as itemService, c.in_execution as inExecution, cs.status_chamado, c.solicitante_id as solicitante_id, tc.tipo
        FROM chamados as c
        LEFT JOIN usuarios as u ON u.id = c.atendente_id
        LEFT JOIN pessoas as p ON p.id = u.pessoa_id
        LEFT JOIN contract_service as cser ON cser.id = c.service_id
        LEFT JOIN service as s ON s.id = cser.service_id
        LEFT JOIN contract_iten_service as cis ON cis.id = c.iten_service_id
        LEFT JOIN iten_service as ise ON ise.id = cis.iten_service
        LEFT JOIN chamados_status as cs ON cs.id = c.status_id
        LEFT JOIN tipos_chamados as tc ON tc.id = c.tipochamado_id                            
        WHERE c.id = :id 
        ORDER BY c.id DESC"
    );
    $stmt_chamado->bindParam(':id', $id);
    $stmt_chamado->execute();

    // Fetch the result
    $chamado = $stmt_chamado->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}

?>

<main id="main" class="main">
    <section class="section">
        <div class="row">
            <?php
            if ($chamado['solicitante_id'] == $_SESSION['id']) { ?>

                <div class="col-lg-12">
                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <button title="Anexos" type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalAnexos"><i class="bi bi-paperclip"></i></button>
                            <?php
                            if ($chamado['status_id'] != 3) { ?>

                                <a href="/chamados/relatar.php?id=<?= $chamado['id'] ?>" class="btn btn-sm btn-info">Relatar</a>

                            <?php
                            } ?>
                        </div>
                    </div>
                    <div style="margin-top: 10px;" class="card">
                        <div style="margin-top: 10px;" class="card-body">
                            <?= '<b>#' . $chamado['id'] . ' - ' . $chamado['tipo'] . '</b>' ?><br><br>

                            <strong>Data Abertura:</strong>
                            <?php echo $chamado['data_abertura']; ?><br>

                            <strong>Atendente:</strong>
                            <?php echo $chamado['atendente'] !== null ? $chamado['atendente'] : 'Sem Atendente'; ?><br>

                            <strong>Serviço:</strong>
                            <?php echo $chamado['service']; ?><br>

                            <strong>Item:</strong>
                            <?php echo $chamado['itemService'] !== null ? $chamado['itemService'] : 'Sem Item'; ?><br>

                            <strong>Status:</strong>
                            <?php echo $chamado['status_chamado']; ?><br><br>

                            <strong>Relato Abertura:</strong><br>
                            <?php echo nl2br($chamado['relato_inicial']); ?><br>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <br>
                            <div class="accordion accordion-flush" id="accordionFlushExample">

                                <?php
                                $relatos = $pdo->prepare(
                                    "SELECT cr.id as id, DATE_FORMAT(cr.relato_hora_inicial, '%d/%m/%Y %H:%i') as hora_inicial, p.nome as relator, cr.relato
                            FROM chamado_relato as cr
                            LEFT JOIN usuarios as u ON u.id = cr.relator_id
                            LEFT JOIN pessoas as p ON p.id = u.pessoa_id
                            WHERE
                            cr.private = 1
                            AND
                            cr.chamado_id = :id_chamado
                            ORDER BY cr.id desc"
                                );

                                $relatos->bindParam(':id_chamado', $id);
                                $relatos->execute();

                                while ($row_relatos = $relatos->fetch(PDO::FETCH_ASSOC)) {

                                ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-heading<?= $row_relatos['id']; ?>">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?php echo $row_relatos['id']; ?>" aria-expanded="false" aria-controls="flush-collapse<?php echo $row_relatos['id']; ?>">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <?= '<b>#' . $row_relatos['id'] . ' - ' . $row_relatos['hora_inicial'] . '</b>' ?>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <?= $row_relatos['relator'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </button>

                                        </h2>
                                        <div id="flush-collapse<?php echo $row_relatos['id']; ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading<?php echo $row_relatos['id']; ?>" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">
                                                <strong>Relato</strong><br>
                                                <?php echo nl2br($row_relatos['relato']); ?>


                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <br>
                                <span> <b>Este não é um chamado aberto por este usuário.</b></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            ?>
        </div>
    </section>
</main>

<div class="modal fade" id="modalAnexos" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Anexos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">


                <?php
                if ($chamado['status_id'] != 3) { ?>


                    <form action="processa/upload.php" method="POST" id="uploadForm" enctype="multipart/form-data">
                        <input id="uploadChamadoID" name="uploadChamadoID" value="<?= $chamado['id'] ?> " hidden="" readonly="">
                        <div class="col-lg-12 row">
                            <div class="col-8">
                                <input title="Permitido: jpg, jpeg, png, txt, pdf" required="" class="form-control" type="file" name="fileInput" id="fileInput" multiple="">
                            </div>
                            <div class="col-4" style="margin-top: 5px;">
                                <button class="btn btn-sm btn-danger" type="submit">Enviar</button>
                            </div>
                        </div>
                    </form>
                <?php }
                ?>
                <?php
                $host = $_SERVER['HTTP_HOST'];
                // Separa o nome do domínio do subdomínio
                $parts = explode('.', $host);
                // Remove o primeiro elemento (subdomínio) do array
                $domain = implode('.', array_slice($parts, -2));
                $domain = 'smartcontrol.' . $domain;

                $targetDirectory = '../../' . $domain . '/uploads/chamados/chamado' . $id . '/'; // Diretório de destino



                if (file_exists($targetDirectory)) {
                    $files = scandir($targetDirectory);
                    if ($files !== false) {
                        echo '<br><ul>';
                        foreach ($files as $file) {
                            if ($file != '.' && $file != '..') {
                                // Exiba os arquivos como links para download
                                echo '<li><a href="' . $targetDirectory . $file . '" target="_blank">' . $file . '</a></li>';
                            }
                        }
                        echo '</ul>';
                    } else {
                        echo '<br>Nenhum arquivo encontrado.';
                    }
                } else {
                    echo '<br>Nenhum arquivo encontrado.<br>';
                } ?>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php
require '../includes/footer.php';
?>