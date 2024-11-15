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
