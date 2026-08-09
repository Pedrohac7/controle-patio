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
            $clientes[] = $this->criarClienteAPartirDaLinha($row);
        }

        return $clientes;
    }

    public function buscarPorId(int $idcliente): ?Cliente
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM cliente WHERE idcliente = :idcliente'
        );

        $stmt->execute([
            ':idcliente' => $idcliente
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->criarClienteAPartirDaLinha($row);
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

    public function atualizar(Cliente $cliente): void
    {
        if ($cliente->getIdcliente() === null) {
            throw new InvalidArgumentException(
                'O cliente precisa ter um ID para ser atualizado.'
            );
        }

        $sql = '
            UPDATE cliente
            SET
                tipo = :tipo,
                nome = :nome,
                cpf = :cpf,
                cnpj = :cnpj,
                telefone = :telefone
            WHERE idcliente = :idcliente
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':idcliente' => $cliente->getIdcliente(),
            ':tipo' => $cliente->getTipo(),
            ':nome' => $cliente->getNome(),
            ':cpf' => $cliente->getCpf(),
            ':cnpj' => $cliente->getCnpj(),
            ':telefone' => $cliente->getTelefone()
        ]);
    }

    private function criarClienteAPartirDaLinha(array $row): Cliente
    {
        $cliente = new Cliente(
            $row['tipo'],
            $row['nome'],
            $row['cpf'],
            $row['cnpj'],
            $row['telefone']
        );

        $cliente->setIdcliente((int) $row['idcliente']);

        if ($row['data_cadastro'] !== null) {
            $cliente->setDataCadastro(
                new DateTime($row['data_cadastro'])
            );
        }

        return $cliente;
    }
}
