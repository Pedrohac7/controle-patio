<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../cliente/ClienteDAO.class.php';

$clienteDAO = new ClienteDAO($pdo);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $idcliente = filter_var($_GET['id'], FILTER_VALIDATE_INT);

        if ($idcliente === false || $idcliente <= 0) {
            http_response_code(400);

            echo json_encode([
                'message' => 'ID do cliente inválido'
            ]);

            exit;
        }

        $cliente = $clienteDAO->buscarPorId($idcliente);

        if ($cliente === null) {
            http_response_code(404);

            echo json_encode([
                'message' => 'Cliente não encontrado'
            ]);

            exit;
        }

        echo json_encode([
            'idcliente' => $cliente->getIdcliente(),
            'tipo' => $cliente->getTipo(),
            'nome' => $cliente->getNome(),
            'cpf' => $cliente->getCpf(),
            'cnpj' => $cliente->getCnpj(),
            'telefone' => $cliente->getTelefone(),
        ]);

        exit;
    }

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

if (
    $_SERVER['REQUEST_METHOD'] === 'PUT'
    || $_SERVER['REQUEST_METHOD'] === 'PATCH'
) {
    $idcliente = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

    if ($idcliente === false || $idcliente === null || $idcliente <= 0) {
        http_response_code(400);

        echo json_encode([
            'message' => 'ID do cliente inválido'
        ]);

        exit;
    }

    if ($clienteDAO->buscarPorId($idcliente) === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Cliente não encontrado'
        ]);

        exit;
    }

    $dados = json_decode(file_get_contents('php://input'), true);

    if (!isset($dados['tipo'], $dados['nome'])) {
        http_response_code(400);

        echo json_encode([
            'message' => 'Tipo e nome são obrigatórios'
        ]);

        exit;
    }

    $cliente = new Cliente(
        $dados['tipo'],
        $dados['nome'],
        $dados['cpf'] ?? null,
        $dados['cnpj'] ?? null,
        $dados['telefone'] ?? null
    );

    $cliente->setIdcliente($idcliente);

    $clienteDAO->atualizar($cliente);

    echo json_encode([
        'message' => 'Cliente atualizado com sucesso'
    ]);

    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $idcliente = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

    if ($idcliente === false || $idcliente === null || $idcliente <= 0) {
        http_response_code(400);

        echo json_encode([
            'message' => 'ID do cliente inválido'
        ]);

        exit;
    }

    if ($clienteDAO->buscarPorId($idcliente) === null) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Cliente não encontrado'
        ]);

        exit;
    }

    $clienteDAO->excluir($idcliente);

    http_response_code(204);

    exit;
}

http_response_code(405);

echo json_encode([
    'message' => 'Método não permitido'
]);
