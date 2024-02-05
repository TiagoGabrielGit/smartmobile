<?php
require '../includes/menu.php';
require '../conexoes/conexao_pdo.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


try {
    $equipe_usuario = $pdo->prepare("SELECT * FROM equipes_integrantes WHERE integrante_id = :usuario_id");
    $equipe_usuario->bindParam(':usuario_id', $_SESSION['id']);
    $equipe_usuario->execute();
    $row_equipe = $equipe_usuario->fetch(PDO::FETCH_ASSOC);
    $equipe_id = $row_equipe['equipe_id'];
} catch (PDOException $e) {
    // Lidar com exceções, se houver
    echo "Erro ao executar a consulta: " . $e->getMessage();
}
?>
<main id="main" class="main">
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <form method="POST" action="processa/abrir_chamado.php">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"><b>Tipo de Chamado*</b></label>
                            <div class="col-sm-10">
                                <select name="chamado" class="form-select" aria-label="Default select example" required>
                                    <option disabled selected value="">Selecione uma opção</option>
                                    <?php
                                    $stmt_tipos_chamados = $pdo->prepare("SELECT tc.id, tc.tipo, tc.mascara
    FROM chamados_autorizados_mobile_by_equipe AS came
    LEFT JOIN tipos_chamados as tc ON came.tipo_id = tc.id
    WHERE came.equipe_id = :equipe_id AND tc.mobile = 1 AND tc.active = 1");

                                    $stmt_tipos_chamados->bindParam(':equipe_id', $equipe_id);

                                    $stmt_tipos_chamados->execute();
                                    if ($stmt_tipos_chamados->rowCount() > 0) {
                                        // Iterar sobre os resultados da consulta e criar as opções do select
                                        while ($row_tipos_chamados = $stmt_tipos_chamados->fetch(PDO::FETCH_ASSOC)) {
                                            $optionValue = $row_tipos_chamados['id'];
                                            $optionText = $row_tipos_chamados['tipo'];
                                            $optionDataMask = isset($row_tipos_chamados['mascara']) ? $row_tipos_chamados['mascara'] : ''; // Verifica se a chave 'mascara' está definida

                                            echo "<option value='$optionValue' data-mascara='$optionDataMask'>$optionText</option>";
                                        }
                                    } else {
                                        echo "Nenhum tipo de chamado encontrado.";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"><b>Serviço*</b></label>
                                <div class="col-sm-10">
                                    <select class="form-select" aria-label="Default select example" id="servico" name="servico" required>
                                        <option disabled selected value="">Selecione uma opção</option>
                                        <?php
                                        try {
                                            $stmt_servicos = $pdo->prepare("SELECT
                                    c.id as contractID, cs.id as contractServiceID, s.service as service
                                    FROM contract_service as cs
                                    LEFT JOIN contract as c ON cs.contract_id = c.id
                                    LEFT JOIN service as s ON s.id = cs.service_id
                                    WHERE c.empresa_id = :empresa_id AND c.active = 1 AND cs.active = 1
                                    ORDER BY s.service ASC");

                                            $empresa_id = $_SESSION['empresa_id'];
                                            $stmt_servicos->bindParam(':empresa_id', $empresa_id);

                                            $stmt_servicos->execute();

                                            if ($stmt_servicos->rowCount() > 0) {
                                                while ($row_servicos = $stmt_servicos->fetch(PDO::FETCH_ASSOC)) { ?>

                                                    <option value="<?= $row_servicos['contractServiceID'] ?>"><?= $row_servicos['service'] ?></option>
                                        <?php }
                                            } else {
                                                echo "<option value=''>Nenhum serviço encontrado.</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "<option value=''>Erro ao executar a consulta: " . $e->getMessage() . "</option>";
                                        }
                                        ?>
                                    </select>

                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"><b>Item</b></label>
                                <div class="col-sm-10">
                                    <select class="form-select" aria-label="Default select example" id="item" name="item">
                                        <option disabled selected value="">Selecione o serviço</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="relato_chamado" class="col-sm-2 col-form-label">Descreva</label>
                                <div class="col-sm-10">
                                    <textarea name="relato" id="relato_chamado" class="form-control" rows="10" required></textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-sm btn-danger">Abrir Chamado</button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
require '../includes/footer.php';
?>

<script>
    document.getElementById('servico').addEventListener('change', function() {
        var servicoID = this.value;
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var items = JSON.parse(xhr.responseText);
                    var itemSelect = document.getElementById('item');
                    // Limpa todas as opções existentes
                    itemSelect.innerHTML = '';

                    // Adiciona novas opções, se houver
                    if (items.length > 0) {
                        items.forEach(function(item) {
                            var option = document.createElement('option');
                            option.value = item.id;
                            option.textContent = item.item;
                            itemSelect.appendChild(option);
                        });
                    }
                } else {
                    console.error('Erro ao carregar os itens.');
                }
            }
        };
        xhr.open('GET', 'busca_itens.php?serviceID=' + servicoID);
        xhr.send();
    });

    document.querySelector('[name="chamado"]').addEventListener('change', function() {
        var mascara = this.options[this.selectedIndex].getAttribute('data-mascara');
        if (mascara) {
            document.getElementById('relato_chamado').value = mascara;
        } else {
            document.getElementById('relato_chamado').value = '';
        }
    });
</script>