<?php
require "../includes/menu.php";
require '../conexoes/conexao_pdo.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $id = $_GET['id'];

    // Consulta para selecionar o chamado com base no ID
    $stmt_chamado = $pdo->prepare(
        "SELECT c.solicitante_id, c.id, tc.tipo, c.relato_inicial
        FROM chamados as c
        LEFT JOIN tipos_chamados as tc ON tc.id = c.tipochamado_id                            
        WHERE c.id = :id"
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
                    <div class="card">
                        <div style="margin-top: 10px;" class="card-body">
                            <div class="text-center">
                                <?= '<b>#' . $chamado['id'] . ' - ' . $chamado['tipo'] . '</b>' ?>
                            </div>
                            <br>
                            <strong>Relato Abertura:</strong><br>
                            <?php echo nl2br($chamado['relato_inicial']);?><br>

                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div style="margin-top: 10px;" class="card-body">
                            <form method="POST" action="processa/relatar.php">

                                <input hidden readonly name="id_chamado" value="<?= $chamado['id'] ?>"></input>
                                <div class="row mb-3">
                                    <label for="relato_chamado" class="col-sm-2 col-form-label"><b>Descreva o
                                            relato</b></label>
                                    <div class="col-sm-10">
                                        <textarea name="relato" class="form-control" rows="10" required></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-sm btn-danger">Relatar</button>
                                    </div>
                                </div>
                            </form>
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
            <?php } ?>
        </div>
    </section>
</main>

<?php
require "../includes/footer.php"
    ?>