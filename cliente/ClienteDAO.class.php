<?php

require_once __DIR__ . '/Cliente.model.php';

class ClienteDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listar(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM cliente');

        $clientes = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cliente = new Cliente(
                $row['tipo'],
                $row['nome'],
                $row['cpf'],
                $row['cnpj'],
                $row['telefone']
            );

            $cliente->setIdcliente($row['idcliente']);

            if ($row['data_cadastro'] !== null) {
                $cliente->setDataCadastro(
                    new DateTime($row['data_cadastro'])
                );
            }

            $clientes[] = $cliente;
        }

        return $clientes;
    }

    public function inserir(Cliente $cliente): void
    {
        $sql = '
            INSERT INTO cliente (
                tipo,
                nome,
                cpf,
                cnpj,
                telefone
            )
            VALUES (
                :tipo,
                :nome,
                :cpf,
                :cnpj,
                :telefone
            )
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':tipo' => $cliente->getTipo(),
            ':nome' => $cliente->getNome(),
            ':cpf' => $cliente->getCpf(),
            ':cnpj' => $cliente->getCnpj(),
            ':telefone' => $cliente->getTelefone()
        ]);
    }
}