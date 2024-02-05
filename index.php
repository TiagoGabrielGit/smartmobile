<?php
require "includes/menu.php";
require "conexoes/conexao_pdo.php";

$stmt_chamados_abertos = $pdo->prepare("SELECT COUNT(c.id) FROM chamados as c WHERE c.solicitante_id = :solicitante_id AND status_id != 3 ORDER BY c.id DESC");
$stmt_chamados_abertos->bindParam(':solicitante_id', $_SESSION['id']);
$stmt_chamados_abertos->execute();

if ($stmt_chamados_abertos->rowCount() > 0) {
    $quantidade_chamados_abertos = $stmt_chamados_abertos->fetchColumn();
} else {
    $quantidade_chamados_abertos = "0";
}

$stmt_chamados_fechados = $pdo->prepare("SELECT COUNT(c.id) FROM chamados as c WHERE c.solicitante_id = :solicitante_id AND status_id = 3 ORDER BY c.id DESC");
$stmt_chamados_fechados->bindParam(':solicitante_id', $_SESSION['id']);
$stmt_chamados_fechados->execute();

if ($stmt_chamados_fechados->rowCount() > 0) {
    $quantidade_chamados_fechados = $stmt_chamados_fechados->fetchColumn();
} else {
    $quantidade_chamados_fechados = "0";
}


?>

<main id="main" class="main">
    <section class="section dashboard">
        <div class="col-xxl-4 col-xl-12">
            <a href="/chamados/novo_chamado.php"
                class="card info-card customers-card d-flex justify-content-center align-items-center bg-info"
                style="height: 80px;"> <!-- Ajuste a altura conforme necessário -->
                <div class="card-body text-center" style="padding-top: 30px;">
                    <h5 class="card-title">Novo Chamado</h5>
                </div>
            </a>
        </div>

        <div class="col-xxl-4 col-xl-12">
            <a href="/chamados/chamados_abertos.php"
                class="card info-card customers-card d-flex justify-content-center align-items-center bg-warning"
                style="height: 80px;"> <!-- Ajuste a altura conforme necessário -->
                <div class="card-body text-center" style="padding-top: 30px;">
                    <h5 class="card-title">Chamados Abertos<span> | <?= $quantidade_chamados_abertos ?> </span></h5>

                </div>
            </a>
        </div>
        <div class="col-xxl-4 col-xl-12">
            <a href="/chamados/chamados_fechados.php"
                class="card info-card customers-card d-flex justify-content-center align-items-center bg-secondary"
                style="height: 80px;"> <!-- Ajuste a altura conforme necessário -->
                <div class="card-body text-center" style="padding-top: 30px;">
                    <h5 class="card-title">Chamados Fechados<span> | <?= $quantidade_chamados_fechados ?> </span></h5>

                </div>
            </a>
        </div>
    </section>
</main>
<?php
require "includes/footer.php";

?>