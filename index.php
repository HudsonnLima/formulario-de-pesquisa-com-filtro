<?php
include_once "app/db.php";

// Captura os dados da URL com filtro seguro
$dados = filter_input_array(INPUT_GET, FILTER_DEFAULT);
$empresa_id = 1;
$operacao = isset($_GET['operacao']) ? $_GET['operacao'] : '0'; // Default para '0'
$params = []; // Inicializando a variável $params como um array vazio
$countParams = [];
$limit = 50;
// Verifica a página atual na URL, caso contrário, define como 1
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaAtual = max(1, $paginaAtual); // Garante que a página seja no mínimo 1
// Calcula o offset para a query SQL
$offset = ($paginaAtual - 1) * $limit;

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <?php

        if ($operacao == 0) {

            if (isset($_GET['agrupar']) && $_GET['agrupar'] == 1) :

                // Consulta de contagem total de registros
                $countQuery = "SELECT COUNT(DISTINCT saida.produto_id) AS total_linhas
                FROM est_saidas AS saida
                INNER JOIN produtos AS produto ON produto.produto_id = saida.produto_id
                WHERE 1=1";

                //CONSULTA SAÍDA AGRUPADA

                $query = "SELECT saida.setor_id, saida.produto_id, SUM(saida.quantidade) AS quantidade, 
                 produto.produto, AVG(saida.preco) AS preco_media, SUM(saida.preco) AS preco_total,
     
                 (
                     SELECT fornecedor.fornecedor_razao
                     FROM est_entradas AS entrada
                     INNER JOIN fornecedor ON fornecedor.fornecedor_id = entrada.fornecedor_id
                     WHERE entrada.produto_id = saida.produto_id
                     ORDER BY entrada.cad_data DESC
                     LIMIT 1
                 ) AS fornecedor_fantasia
 
 
                 FROM est_saidas AS saida
                 INNER JOIN produtos AS produto ON produto.produto_id = saida.produto_id
                 
                 WHERE 1=1";

            else:

                // Consulta de contagem total de registros
                $countQuery = "SELECT COUNT(*) 
                FROM est_saidas AS saida 
                INNER JOIN produtos AS produto ON produto.produto_id = saida.produto_id
                INNER JOIN funcionarios AS funcionario ON funcionario.funcionario_id = saida.funcionario_id
                INNER JOIN setor AS setores ON setores.setor_id = saida.setor_id
                WHERE 1=1";



                //CONSULTA SAÍDA
                $query = "SELECT produto.produto_id, produto.produto, saida.preco AS preco, 
                funcionario.setor_id, funcionario.funcionario_nome, setores.setor_id, setores.setor_nome,
                saida.funcionario_id, saida.operacao, saida.setor_id AS setor, saida.produto_id, saida.cad_data, saida.empresa_id, saida.quantidade 
                FROM est_saidas AS saida
                INNER JOIN produtos AS produto ON produto.produto_id = saida.produto_id
                INNER JOIN funcionarios AS funcionario ON funcionario.funcionario_id = saida.funcionario_id
                INNER JOIN setor AS setores ON setores.setor_id = saida.setor_id
                WHERE 1=1";

            endif;
        } elseif ($operacao == 1) {

            if (isset($_GET['agrupar']) && $_GET['agrupar'] == 1) :


                // Consulta de contagem total de registros
                $countQuery = "SELECT COUNT(DISTINCT entrada.produto_id) AS total_linhas
                FROM est_entradas AS entrada
                INNER JOIN produtos AS produto ON produto.produto_id = entrada.produto_id
                WHERE 1=1";

                //CONSULTA SAÍDA AGRUPADA
                $query = "SELECT entrada.setor_id, entrada.produto_id, SUM(entrada.quantidade) AS quantidade, 
                 produto.produto, AVG(entrada.preco) AS preco_media, SUM(entrada.preco) AS preco_total, entrada.fornecedor_id, fornecedor.fornecedor_fantasia
                 FROM est_entradas AS entrada
                 INNER JOIN produtos AS produto ON produto.produto_id = entrada.produto_id
                 INNER JOIN fornecedor ON fornecedor.fornecedor_id = entrada.fornecedor_id
                 WHERE 1=1";

            else:

                // Consulta de contagem total de registros
                $countQuery = "SELECT COUNT(*) 
                FROM est_entradas AS entrada 
                INNER JOIN produtos AS produto ON produto.produto_id = entrada.produto_id
                INNER JOIN fornecedor AS forn ON forn.fornecedor_id = entrada.fornecedor_id
                INNER JOIN setor AS setores ON setores.setor_id = entrada.setor_id
                WHERE 1=1";

                //CONSULTA ENTRADA
                $query = "SELECT produto.produto_id, produto.produto, entrada.preco AS preco, 
                setores.setor_id, setores.setor_nome, forn.fornecedor_fantasia, forn.fornecedor_id,
                entrada.fornecedor_id, entrada.operacao, entrada.setor_id AS setor, entrada.produto_id, entrada.cad_data, entrada.empresa_id, entrada.quantidade
                FROM est_entradas AS entrada
                INNER JOIN produtos AS produto ON produto.produto_id = entrada.produto_id
                INNER JOIN fornecedor AS forn ON forn.fornecedor_id = entrada.fornecedor_id
                INNER JOIN setor AS setores ON setores.setor_id = entrada.setor_id
                WHERE 1=1";

            endif;
        }

        // FILTRO EMPRESA
        if (isset($empresa_id) && !empty($empresa_id)) {
            if ($operacao == 0) {
                $query .= " AND saida.empresa_id = :empresa_id";
                $countQuery .= " AND saida.empresa_id = :empresa_id";
            }
            if ($operacao == 1) {
                $query .= " AND entrada.empresa_id = :empresa_id";
                $countQuery .= " AND entrada.empresa_id = :empresa_id";
            }
            $params[':empresa_id'] = $empresa_id;
            $countParams[':empresa_id'] = $empresa_id;
        }

        // FILTRO SETOR
        if (isset($dados['setor_id']) && !empty($dados['setor_id'])) {
            $setor_id = $dados['setor_id'];
            if ($operacao == 0) { // Operação de saída
                $query .= " AND saida.setor_id = :setor_id";
                $countQuery .= " AND saida.setor_id = :setor_id";
            } elseif ($operacao == 1) { // Operação de entrada
                $query .= " AND entrada.setor_id = :setor_id";
                $countQuery .= " AND entrada.setor_id = :setor_id";
            }
            $params[':setor_id'] = $setor_id;
            $countParams[':setor_id'] = $setor_id;
        }

        // FILTRO PRODUTO
        if (isset($dados['produto']) && !empty($dados['produto'])) {
            $produto = "%" . trim($dados['produto']) . "%"; // Busca por similaridade
            $query .= " AND produto.produto LIKE :produto";
            $countQuery .= " AND produto.produto LIKE :produto";
            $params[':produto'] = $produto;
            $countParams[':produto'] = $produto;
        }

        //FILTRO FUNCIONÁRIO
        if (isset($dados['funcionario']) && !empty($dados['funcionario'])) {
            $funcionario = "%" . trim($dados['funcionario']) . "%"; // Busca por similaridade
            $query .= " AND funcionario.funcionario_nome LIKE :funcionario";
            $params[':funcionario'] = $funcionario;
            $countQuery .= " AND funcionario.funcionario_nome LIKE :funcionario";
            $countParams[':funcionario'] = $funcionario;
        }

        //FILTRO FORNECEDOR
        if (isset($dados['fornecedor']) && !empty($dados['fornecedor'])) {
            $fornecedor = "%" . trim($dados['fornecedor']) . "%"; // Busca por similaridade
            $query .= " AND forn.fornecedor_razao LIKE :fornecedor OR forn.fornecedor_fantasia LIKE :fornecedor";
            $params[':fornecedor'] = $fornecedor;
            $countQuery .= " AND forn.fornecedor_razao LIKE :fornecedor";
            $countParams[':fornecedor'] = $fornecedor;
        }

        // FILTRO DATA INICIAL
        if (isset($dados['data_inicio']) && !empty($dados['data_inicio'])) {
            $data_inicio = $dados['data_inicio'];
            $params[':data_inicio'] = $data_inicio;
            $countParams[':data_inicio'] = $data_inicio;

            if ($operacao == 0) { // Operação de saída
                $query .= " AND saida.cad_data >= :data_inicio";
                $countQuery .= " AND saida.cad_data >= :data_inicio";
            } elseif ($operacao == 1) { // Operação de entrada
                $query .= " AND entrada.cad_data >= :data_inicio";
                $countQuery .= " AND entrada.cad_data >= :data_inicio";
            }
        }

        // FILTRO DATA FINAL
        if (isset($dados['data_final']) && !empty($dados['data_final'])) {
            $data_final = $dados['data_final'];
            $params[':data_final'] = $data_final;
            $countParams[':data_final'] = $data_final;

            if ($operacao == 0) { // Operação de saída
                $query .= " AND saida.cad_data <= :data_final";
                $countQuery .= " AND saida.cad_data <= :data_final";
            } elseif ($operacao == 1) { // Operação de entrada
                $query .= " AND entrada.cad_data <= :data_final";
                $countQuery .= " AND entrada.cad_data <= :data_final";
            }
        }

        //SAÍDAS
        if ($operacao == 0) {
            if (isset($_GET['agrupar']) && $_GET['agrupar'] == 1) {
                $query .= " GROUP BY saida.produto_id ORDER BY saida.cad_data DESC LIMIT :offset, :limit";
            } else {
                $query .= " ORDER BY saida.cad_data DESC LIMIT :offset, :limit";
            }

            //ENTRADAS
        } elseif ($operacao == 1) {

            if (isset($_GET['agrupar']) and $_GET['agrupar'] == 1) {
                $query .= " GROUP BY entrada.produto_id ORDER BY entrada.cad_data DESC LIMIT :offset, :limit";
            } else {
                $query .= " ORDER BY entrada.cad_data DESC LIMIT :offset, :limit";
            }
        }


        // Preparação e execução da consulta de contagem total de registros
        $countStmt = $pdo->prepare($countQuery);
        foreach ($countParams as $countKey => $countValue) {
            $countStmt->bindValue($countKey, $countValue);
        }
        $countStmt->execute();
        $totalRegistros = $countStmt->fetchColumn();

        // Cálculo do total de páginas para paginação
        $totalPaginas = ceil($totalRegistros / $limit);

        // Preparação e execução da consulta principal
        $stmt = $pdo->prepare($query);

        // Vinculação dos parâmetros principais
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Parâmetros para paginação
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


        ?>

        <form id="searchForm" method="GET" class="row g-2">
            <div class="form-group col-md-3">
                <input type="hidden" class="form-control" id="empresa_id" name="empresa_id" value="<?php if (isset($empresa_id)) echo htmlspecialchars($empresa_id); ?>">

                <div class="form-floating">
                    <select class="form-select" name="operacao" id="operacao">
                        <option value="0" <?php if (isset($dados['operacao']) && $dados['operacao'] == 0) echo 'selected="selected"'; ?>> Saída </option>
                        <option value="1" <?php if (isset($dados['operacao']) && $dados['operacao'] == 1) echo 'selected="selected"'; ?>> Entrada </option>
                    </select>
                    <label for="floatingSelectGrid">Operação:</label>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-floating">
                    <select id="setor" name="setor_id" class="form-select">
                        <option value="">Selecione o setor</option>
                        <?php
                        // Consulta para obter os setores
                        $setorQuery = "SELECT setor_id, setor_nome FROM setor WHERE empresa_id = $empresa_id";
                        $stmtSetor = $pdo->query($setorQuery);
                        $setores = $stmtSetor->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <!-- Gerar opções dinamicamente a partir dos resultados da consulta -->
                        <?php foreach ($setores as $setor): ?>
                            <option value="<?php echo $setor['setor_id']; ?>" <?php if (isset($dados['setor_id']) && $dados['setor_id'] == $setor['setor_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($setor['setor_nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="floatingSelectGrid">Setor:</label>
                </div>
            </div>

            <div class="form-group col-md-6">
                <div class="form-floating">
                    <input class="form-control" id="produto" name="produto" value="<?php if (isset($dados['produto'])) echo htmlspecialchars($dados['produto']); ?>">
                    <label for="produto">Pesquisar por produto:</label>
                </div>
            </div>


            <?php $campo = isset($dados['funcionario']) ? 'funcionario' : (isset($dados['fornecedor']) ? 'fornecedor' : null);?>

            <div class="form-group col-md-4">
                <div class="form-floating">
                    <input class="form-control" id="pesquisaInput" name="funcionario" value="<?php if (isset($campo)) echo htmlspecialchars($dados[$campo]); ?>">
                    <label id="pesquisaLabel" for="atual">Pesquisar por funcionário:</label>
                </div>
                          
            </div>


            <div class="form-group col-md-4">
                <div class="form-floating">
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?php if (isset($dados['data_inicio'])) echo $dados['data_inicio']; ?>">
                    <label for="floatingInputGrid">Data Início:</label>
                </div>
            </div>

            <div class="form-group col-md-4">
                <div class="form-floating">
                    <input type="date" class="form-control" id="data_final" name="data_final" value="<?php if (isset($dados['data_final'])) echo $dados['data_final']; ?>">
                    <label for="floatingInputGrid">Data Final:</label>
                </div>
            </div>

            <div class="form-check">
                <input class="form-check-input" name="agrupar" type="checkbox" id="agrupar" value="1"
                    <?php echo (isset($_GET['agrupar']) && $_GET['agrupar'] == '1') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="agrupar">
                    Agrupar Produtos
                </label>
                          
            </div>



            <div class="col-12">
                <button type="submit" id="pesquisa" class="btn btn-primary">Pesquisar</button>
                <button type="button" id="limparCampos" class="btn btn-secondary">Limpar Campos</button>
            </div>
        </form>


        <!-- Exibir os resultados da consulta -->
        <?php if (!empty($results) and isset($dados['operacao'])): ?>

            <?php
            if ($totalRegistros == 1) {
                echo "<br/><div class='msg_pesquisa'>Sua pesquisa retornou: $totalRegistros registro.</div><br/>";
            } else {
                echo "<br/><div class='msg_pesquisa'>Sua pesquisa retornou: $totalRegistros registros.</div><br/>";
            }
            ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped ">
                    <thead class="table-dark">
                        <tr>
                            <?php if ($operacao == 0) { ?>
                                <th style="width: 600px;">Produto</th>
                                <?php if (isset($_GET['agrupar']) and $_GET['agrupar'] == 1) { ?>
                                    <th style="text-align: left; width: 550px;">Fornecedor</th>
                                <?php } else { ?>
                                    <th style="text-align: left; width: 500px;">Funcionário</th>
                                <?php } ?>
                                <th style="text-align: center; width: 150px;">Preço UN</th>
                                <th style="text-align: center; width: 80px;">QNT</th>
                                <th style="text-align: center; width: 150px;">Preço Total</th>

                                <?php if ($operacao == 0 and !isset($_GET['agrupar'])) { ?>
                                    <th style="text-align: center; width: 250px;">Setor</th>
                                    <th style="text-align: center;">Data</th>
                                <?php } ?>

                            <?php } elseif ($operacao == 1) { ?>
                                <th>Produto</th>
                                <th>Fornecedor</th>
                                <th style="text-align: center; ">Preço UN</th>
                                <th style="text-align: center;">QNT</th>
                                <th style="text-align: center; width: 150px;">Preço Total</th>
                                <?php if (!isset($_GET['agrupar'])) { ?>
                                    <th style="text-align: center;">Setor</th>
                                    <th style="text-align: center;">Data</th>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>

                            <tr>
                                <td><?php echo htmlspecialchars(substr($row['produto'], 0, 35) . ' - ' . $row['produto_id']); ?></td>

                                <?php if ($operacao == 0 and !isset($_GET['agrupar'])) { ?>
                                    <td><?php echo htmlspecialchars($row['funcionario_nome']); ?></td>
                                <?php } elseif ($operacao == 0 && isset($_GET['agrupar']) && $_GET['agrupar'] == 1) { ?>
                                    <td style="text-align: left;"><?php echo isset($row['fornecedor_fantasia']) ? htmlspecialchars(substr($row['fornecedor_fantasia'], 0, 35), ENT_QUOTES, 'UTF-8') : ' TTR VIDROS '; ?></td>

                                <?php } elseif ($operacao == 1) { ?>
                                    <td><?php echo htmlspecialchars(substr($row['fornecedor_fantasia'], 0, 30)); ?></td>
                                <?php } ?>


                                <?php if (isset($_GET['agrupar'])) { ?>
                                    <td align="center">R$: <?= number_format((($row['preco_media'])), 2, ",", "."); ?></td>
                                <?php } else { ?>
                                    <td align="center">R$: <?= number_format((($row['preco'])), 2, ",", "."); ?></td>
                                <?php } ?>

                                <td style="text-align: center;"><?php echo htmlspecialchars($row['quantidade']); ?></td>



                                <?php if (isset($_GET['agrupar'])) {
                                    $preco_total = $row['preco_media'] * $row['quantidade']; ?>
                                <?php } else {
                                    $preco_total = $row['preco'] * $row['quantidade']; ?>
                                <?php } ?>



                                <td style="text-align: center;">R$ <?php echo number_format($preco_total, 2, ',', '.'); ?></td>

                                <?php if (!isset($_GET['agrupar'])) { ?>
                                    <td style="text-align: center;"><?php echo htmlspecialchars($row['setor_nome']); ?></td>
                                    <td style="text-align: center;"><?= date('d/m/Y', strtotime($row['cad_data'])); ?></td>
                                <?php } ?>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($totalRegistros > $limit) { ?>
                    <div class="paginacao">
                        <ul class="pagination justify-content-center">
                            <?php if ($paginaAtual > 1): ?>
                                <!-- Link para a primeira página -->
                                <li class="page-item">
                                    <a class="page-link" href="<?= pegarUri(1) ?>">Primeira</a>
                                </li>
                            <?php endif; ?>

                            <?php
                            // Define o início e o fim da faixa de links a serem exibidos
                            $inicio = max(1, $paginaAtual - 4); // Mostra até 1 página anterior
                            $fim = min($totalPaginas, $paginaAtual + 4); // Mostra até 2 páginas posteriores

                            // Exibe os links para as páginas dentro do intervalo
                            for ($i = $inicio; $i <= $fim; $i++): ?>
                                <li class="page-item <?= ($i == $paginaAtual) ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= pegarUri($i) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($paginaAtual < $totalPaginas): ?>
                                <!-- Link para a última página -->
                                <li class="page-item">
                                    <a class="page-link" href="<?= pegarUri($totalPaginas) ?>">Última</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php } ?>



            </div>
        <?php
        elseif (empty($dados['operacao'])):

        else: ?>
            <div class='alert' style='text-align:center;'>
                <h6>Produto não encontrado!</h6>
            </div>
        <?php endif; ?>
    </div>

    <?php
    // Função para construir a URL mantendo os parâmetros existentes
    function pegarUri($pagina)
    {
        $urlAtual = $_SERVER['REQUEST_URI'];
        // Remove o parâmetro pagina atual, se houver
        $urlSemPagina = preg_replace('/&pagina=\d+/', '', $urlAtual);
        // Adiciona o parâmetro da nova página
        return $urlSemPagina . "&pagina=" . $pagina;
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="js/custom.js"></script>
</body>

</html>
