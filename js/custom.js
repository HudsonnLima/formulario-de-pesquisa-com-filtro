const agruparCheckbox = document.getElementById('agrupar');
const operacaoSelect = document.getElementById('operacao');
const pesquisaLabel = document.getElementById('pesquisaLabel');
const pesquisaInput = document.getElementById('pesquisaInput');


// Função para verificar o estado do checkbox e ajustar os inputs
function toggleInputs() {
    const pesquisaInput = document.getElementById('pesquisaInput');

    if (agruparCheckbox.checked) {
        pesquisaInput.value = ''; // Limpa o valor
        pesquisaInput.disabled = true; // Desabilita o campo
    } else {
        pesquisaInput.disabled = false; // Habilita o campo
    }
}

// Chama a função no carregamento da página para verificar o estado inicial
window.addEventListener('load', toggleInputs);
// Adiciona o evento de mudança no checkbox
document.getElementById('agrupar').addEventListener('change', toggleInputs);


// Função para atualizar o texto da label e o atributo name
function atualizarLabelEName() {
    if (operacaoSelect.value === "0") {
        pesquisaLabel.textContent = "Pesquisar por funcionário";
        pesquisaInput.name = "funcionario";
    } else if (operacaoSelect.value === "1") {
        pesquisaLabel.textContent = "Pesquisar por fornecedor";
        pesquisaInput.name = "fornecedor";
    }
}

// Executa a função assim que o DOM estiver carregado
document.addEventListener('DOMContentLoaded', atualizarLabelEName);

// Evento que chama a função ao mudar o select
operacaoSelect.addEventListener('change', atualizarLabelEName);


//PASSA PARA A URI APENAS OS CAMPOS QUE ESTIVEREM PREENCHIDOS
document.getElementById('searchForm').addEventListener('submit', function (event) {
    var produto = document.getElementById('produto');
    var setor = document.getElementById('setor');
    var pesquisaInput = document.getElementById('pesquisaInput');
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
    if (!pesquisaInput.value.trim()) {
        pesquisaInput.removeAttribute('name');
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
document.getElementById('limparCampos').addEventListener('click', function () {
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
});



