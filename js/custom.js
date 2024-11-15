$(function() {
    const operacaoSelect = document.getElementById('operacao');
    const pesquisaLabel = document.getElementById('pesquisaLabel');

    // Função para atualizar o texto da label
    function atualizarLabel() {
        if (operacaoSelect.value === "0") {
            pesquisaLabel.textContent = "Pesquisar por funcionário";
        } else if (operacaoSelect.value === "1") {
            pesquisaLabel.textContent = "Pesquisar por fornecedor";
        }
    }

    // Evento que chama a função ao mudar o select
    operacaoSelect.addEventListener('change', atualizarLabel);



        //PASSA PARA A URI APENAS OS CAMPOS QUE ESTIVEREM PREENCHIDOS
        document.getElementById('searchForm').addEventListener('submit', function(event) {
            var produto = document.getElementById('produto');
            var setor = document.getElementById('setor');
            var funcionario = document.getElementById('funcionario');
            var data_inicio = document.getElementById('data_inicio');
            var data_final = document.getElementById('data_final');
            var agrupar = document.getElementById('agrupar');
            var pesquisa = document.getElementById('pesquisa'); // Adicionando a referência ao input 'pesquisa'

            // Remove os campos do formulário se estiverem vazios
            if (!produto.value.trim()) {
                produto.removeAttribute('name');
            }
            if (!setor.value.trim()) {
                setor.removeAttribute('name');
            }
            if (!funcionario.value.trim()) {
                funcionario.removeAttribute('name');
            }
            if (!data_inicio.value.trim()) {
                data_inicio.removeAttribute('name');
            }
            if (!data_final.value.trim()) {
                data_final.removeAttribute('name');
            }
            if (!agrupar.value.trim()) {
                agrupar.removeAttribute('name');
            }

        });


        //LIMPA O FORMULÁRIO ATRÁS DO BOTÃO 
        document.getElementById('limparCampos').addEventListener('click', function() {
            document.getElementById('setor').value = '';
            document.getElementById('produto').value = '';
            document.getElementById('funcionario').value = '';
            document.getElementById('data_inicio').value = '';
            document.getElementById('data_final').value = '';
            document.getElementById('agrupar').checked = false;

            // Limpa a URI (parâmetros da URL)
            var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.pushState({
                path: newUrl
            }, '', newUrl); // Remove os parâmetros da URL sem recarregar a página
            // Recarrega a página com a URL limpa
            window.location.reload();
        });

        // Função para verificar o estado do checkbox e ajustar os inputs
        function toggleInputs() {
            const agruparCheckbox = document.getElementById('agrupar');
            //const setorInput = document.getElementById('setor');
            const funcionarioInput = document.getElementById('funcionario');

            if (agruparCheckbox.checked) {
                // Limpa e desabilita os inputs se o checkbox estiver selecionado

                funcionarioInput.value = '';


                funcionarioInput.disabled = true;
            } else {
                // Habilita os inputs se o checkbox não estiver selecionado

                funcionarioInput.disabled = false;
            }
        }

        // Chama a função no carregamento da página para verificar o estado inicial
        window.addEventListener('load', toggleInputs);

        // Adiciona o evento de mudança no checkbox
        document.getElementById('agrupar').addEventListener('change', toggleInputs);

    
});
