<?php

class Cliente
{
    private ?int $idcliente = null;
    private string $tipo;
    private string $nome;
    private ?string $cpf;
    private ?string $cnpj;
    private ?string $telefone;
    private ?DateTime $data_cadastro = null;

    public function __construct(
        string $tipo,
        string $nome,
        ?string $cpf,
        ?string $cnpj,
        ?string $telefone
    ) {
        $this->tipo = $tipo;
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->cnpj = $cnpj;
        $this->telefone = $telefone;
    }

    public function getIdcliente(): ?int
    {
        return $this->idcliente;
    }

    public function setIdcliente(int $idcliente): void
    {
        $this->idcliente = $idcliente;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(?string $cpf): void
    {
        $this->cpf = $cpf;
    }

    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    public function setCnpj(?string $cnpj): void
    {
        $this->cnpj = $cnpj;
    }

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function setTelefone(?string $telefone): void
    {
        $this->telefone = $telefone;
    }

    public function getDataCadastro(): ?DateTime
    {
        return $this->data_cadastro;
    }

    public function setDataCadastro(DateTime $data_cadastro): void
    {
        $this->data_cadastro = $data_cadastro;
    }
}