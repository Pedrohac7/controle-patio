<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../cliente/ClienteDAO.class.php';

$clienteDAO = new ClienteDAO($pdo);

$clientes = $clienteDAO->listar();

foreach ($clientes as $cliente) {
    echo 'ID: ' . $cliente->getIdcliente() . '<br>';
    echo 'Nome: ' . $cliente->getNome() . '<br>';
    echo 'Tipo: ' . $cliente->getTipo() . '<br>';
    echo 'CPF: ' . ($cliente->getCpf() ?? '-') . '<br>';
    echo 'CNPJ: ' . ($cliente->getCnpj() ?? '-') . '<br>';
    echo 'Telefone: ' . ($cliente->getTelefone() ?? '-') . '<br>';
    echo '<hr>';
}