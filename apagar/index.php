<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Exemplo prático de &lt;template&gt;</title>
  <style>
    body { font-family: sans-serif; max-width: 400px; margin: 40px auto; }
    .tarefa {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px;
      margin-top: 8px;
      background-color: #f4f4f4;
      border-radius: 5px;
    }
    button { cursor: pointer; }
  </style>
</head>
<body>

  <h2>Minhas Tarefas</h2>
  
  <!-- Formulário simples para criar novas tarefas -->
  <input type="text" id="input-tarefa" placeholder="Digite uma nova tarefa...">
  <button id="btn-adicionar">Adicionar</button>

  <!-- Onde os clones do template serão inseridos -->
  <div id="lista-tarefas"></div>

  <!-- 1. A TAG TEMPLATE (Invisível na página) -->
  <template id="molde-tarefa">
    <div class="tarefa">
      <span class="texto-tarefa"></span>
      <button class="btn-remover">Excluir</button>
    </div>
  </template>

  <script>
    // Selecionando os elementos principais
    const input = document.getElementById("input-tarefa");
    const btnAdicionar = document.getElementById("btn-adicionar");
    const lista = document.getElementById("lista-tarefas");
    const template = document.getElementById("molde-tarefa");

    // Função para criar um novo item a partir do template
    function adicionarTarefa(texto) {
      // 2. Clona todo o conteúdo interno do <template>
      const clone = template.content.cloneNode(true);

      // 3. Modifica os elementos dentro do clone
      const span = clone.querySelector(".texto-tarefa");
      const btnRemover = clone.querySelector(".btn-remover");
      const divTarefa = clone.querySelector(".tarefa");

      span.textContent = texto;

      // Adiciona ação ao botão de excluir daquele item específico
      btnRemover.addEventListener("click", () => {
        divTarefa.remove();
      });

      // 4. Coloca o clone pronto dentro da página
      lista.appendChild(clone);
    }

    // Evento de clique no botão "Adicionar"
    btnAdicionar.addEventListener("click", () => {
      if (input.value.trim() !== "") {
        adicionarTarefa(input.value);
        input.value = ""; // Limpa o campo
        input.focus();
      }
    });

    // Criando dois exemplos iniciais ao carregar a página
    adicionarTarefa("Estudar JavaScript");
    adicionarTarefa("Testar a tag <template>");
  </script>

</body>
</html>
