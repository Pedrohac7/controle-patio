<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../cliente/ClienteDAO.class.php';

$clienteDAO = new ClienteDAO($pdo);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $clientes = $clienteDAO->listar();

    echo json_encode(
        array_map(
            fn(Cliente $cliente) => [
                'idcliente' => $cliente->getIdcliente(),
                'tipo' => $cliente->getTipo(),
                'nome' => $cliente->getNome(),
                'cpf' => $cliente->getCpf(),
                'cnpj' => $cliente->getCnpj(),
                'telefone' => $cliente->getTelefone(),
            ],
            $clientes
        )
    );

    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);

    $cliente = new Cliente(
        $dados['tipo'],
        $dados['nome'],
        $dados['cpf'] ?? null,
        $dados['cnpj'] ?? null,
        $dados['telefone'] ?? null
    );

    $clienteDAO->inserir($cliente);

    http_response_code(201);

    echo json_encode([
        'message' => 'Cliente criado com sucesso'
    ]);

    exit;
}

http_response_code(405);

echo json_encode([
    'message' => 'Método não permitido'
]);