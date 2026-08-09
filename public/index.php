<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Pátio</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">
    <main id="app" class="container py-4 py-md-5">
        <header class="mb-4">
            <h1 class="h2 mb-1">Controle de Pátio</h1>
            <p class="text-body-secondary mb-0">Cadastro de clientes</p>
        </header>

        <div
            v-if="mensagem.texto"
            class="alert alert-dismissible fade show"
            :class="mensagem.tipo === 'erro' ? 'alert-danger' : 'alert-success'"
            role="alert"
        >
            {{ mensagem.texto }}
            <button type="button" class="btn-close" @click="limparMensagem"></button>
        </div>

        <section class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h4 mb-3">
                    {{ editando ? 'Editar cliente' : 'Novo cliente' }}
                </h2>

                <form @submit.prevent="salvarCliente">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select id="tipo" v-model="formulario.tipo" class="form-select" required>
                                <option value="F">Pessoa física</option>
                                <option value="J">Pessoa jurídica</option>
                            </select>
                        </div>

                        <div class="col-md-9">
                            <label for="nome" class="form-label">Nome</label>
                            <input id="nome" v-model.trim="formulario.nome" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label for="cpf" class="form-label">CPF</label>
                            <input id="cpf" v-model.trim="formulario.cpf" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label for="cnpj" class="form-label">CNPJ</label>
                            <input id="cnpj" v-model.trim="formulario.cnpj" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input id="telefone" v-model.trim="formulario.telefone" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-primary" :disabled="salvando">
                            {{ salvando ? 'Salvando...' : (editando ? 'Salvar alterações' : 'Cadastrar cliente') }}
                        </button>
                        <button v-if="editando" type="button" class="btn btn-outline-secondary" @click="cancelarEdicao">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 mb-0">Clientes cadastrados</h2>
                    <button class="btn btn-outline-primary btn-sm" @click="carregarClientes" :disabled="carregando">
                        {{ carregando ? 'Atualizando...' : 'Atualizar lista' }}
                    </button>
                </div>

                <div v-if="carregando" class="text-body-secondary">Carregando clientes...</div>

                <div v-else-if="clientes.length === 0" class="text-body-secondary">
                    Nenhum cliente cadastrado.
                </div>

                <div v-else class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Nome</th>
                                <th>CPF/CNPJ</th>
                                <th>Telefone</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="cliente in clientes" :key="cliente.idcliente">
                                <td>{{ cliente.idcliente }}</td>
                                <td>{{ cliente.tipo === 'F' ? 'Física' : 'Jurídica' }}</td>
                                <td>{{ cliente.nome }}</td>
                                <td>{{ cliente.cpf || cliente.cnpj || '-' }}</td>
                                <td>{{ cliente.telefone || '-' }}</td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary me-2" @click="editarCliente(cliente)">
                                        Editar
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" @click="excluirCliente(cliente)">
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    clientes: [],
                    carregando: false,
                    salvando: false,
                    editando: false,
                    mensagem: {
                        tipo: '',
                        texto: ''
                    },
                    formulario: this.criarFormulario()
                };
            },

            mounted() {
                this.carregarClientes();
            },

            methods: {
                criarFormulario() {
                    return {
                        idcliente: null,
                        tipo: 'F',
                        nome: '',
                        cpf: '',
                        cnpj: '',
                        telefone: ''
                    };
                },

                async carregarClientes() {
                    this.carregando = true;

                    try {
                        const resposta = await fetch('cliente.php');
                        this.clientes = await this.obterRespostaJson(resposta);
                    } catch (erro) {
                        this.mostrarMensagem('erro', erro.message);
                    } finally {
                        this.carregando = false;
                    }
                },

                async salvarCliente() {
                    this.salvando = true;

                    const dados = {
                        tipo: this.formulario.tipo,
                        nome: this.formulario.nome,
                        cpf: this.formulario.cpf || null,
                        cnpj: this.formulario.cnpj || null,
                        telefone: this.formulario.telefone || null
                    };

                    const metodo = this.editando ? 'PUT' : 'POST';
                    const url = this.editando
                        ? `cliente.php?id=${this.formulario.idcliente}`
                        : 'cliente.php';

                    try {
                        const resposta = await fetch(url, {
                            method: metodo,
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(dados)
                        });

                        const resultado = await this.obterRespostaJson(resposta);
                        this.mostrarMensagem('sucesso', resultado.message);
                        this.cancelarEdicao();
                        await this.carregarClientes();
                    } catch (erro) {
                        this.mostrarMensagem('erro', erro.message);
                    } finally {
                        this.salvando = false;
                    }
                },

                editarCliente(cliente) {
                    this.formulario = {
                        idcliente: cliente.idcliente,
                        tipo: cliente.tipo,
                        nome: cliente.nome,
                        cpf: cliente.cpf || '',
                        cnpj: cliente.cnpj || '',
                        telefone: cliente.telefone || ''
                    };
                    this.editando = true;
                    this.limparMensagem();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                cancelarEdicao() {
                    this.formulario = this.criarFormulario();
                    this.editando = false;
                },

                async excluirCliente(cliente) {
                    if (!window.confirm(`Excluir o cliente ${cliente.nome}?`)) {
                        return;
                    }

                    try {
                        const resposta = await fetch(`cliente.php?id=${cliente.idcliente}`, {
                            method: 'DELETE'
                        });

                        await this.obterRespostaJson(resposta);
                        this.mostrarMensagem('sucesso', 'Cliente excluído com sucesso');
                        await this.carregarClientes();
                    } catch (erro) {
                        this.mostrarMensagem('erro', erro.message);
                    }
                },

                async obterRespostaJson(resposta) {
                    const texto = await resposta.text();
                    const dados = texto ? JSON.parse(texto) : {};

                    if (!resposta.ok) {
                        throw new Error(dados.message || 'Não foi possível concluir a operação');
                    }

                    return dados;
                },

                mostrarMensagem(tipo, texto) {
                    this.mensagem = { tipo, texto };
                },

                limparMensagem() {
                    this.mensagem = { tipo: '', texto: '' };
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
