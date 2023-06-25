# LaravelAPI

Teste programador backend

Você foi contratado para desenvolver um aplicativo de uma incorporadora. O que eles buscam é saber o salário dos funcionários, sendo que eles são horistas (ganham por hora trabalhada). Os funcionários do sistema são cadastrados a partir da integração com um sistema interno.

Desenvolva um backend, utilizando PHP e Laravel, que tenha as seguintes funcionalidades:

## 1. Caminho para recuperar a lista de operários

**GET /employees**

resposta:
~~~json
{
    "total": 100,
    "per_page": 10,
    "current_page": 1,
    "data": [
        {
        "name": "Fulano",
        "registry": "KX1234"
        },
    …
    ]
}
~~~

## 2. Caminho para cadastrar o valor hora de um operário

**POST /value/<MATRICULA>**

payload:
~~~json
{
  "hour_value": 30.50
}
~~~
## 3. Caminho que traz o valor a ser pago para um determinado profissional, em determinado mês.

**GET /value/<matricula>/<mes>**
~~~json
{
    "name": "Fulano",
    "registry": "KX1234",
    "total_value": 366,
    "total_hours": 1
}
~~~

## 4. Funcionalidade para integrar os dados dos profissionais da API que vem do parceiro.

Os dados dos profissionais a serem cadastrados estão disponíveis na URL: GET https://63zs5guqxkzp3oxyxtzmdwrypa0bvonh.lambda-url.sa-east-1.on.aws/ . Estes dados irão mudar constantemente, preveja momentos para a atualização destes dados junto à API.

A resposta será uma lista de funcionários e matrículas

~~~json
[
    {
        "id": 1
        "funcionario": "Fulano",
        "matricula": "KX1234",
        "tipo": "CLT",
        "data_admissao": "10/12/2021"
    }
]
~~~

## 5. Caminho para cadastrar as horas de um operário no mês

**POST /hours/<MATRICULA>**
payload:
~~~json
{
    "year": "2022",
    "month": "10",
    "total_hours": 12
}
~~~

Será avaliada a qualidade e legibilidade geral do código, a modelagem de dados utilizada para o armazenados, o uso de estruturas aderentes aos padrões de desenvolvimento do Laravel, a existência de testes de unidade e/ou de feature, adoção de boas práticas de desenvolvimento, e a resiliência geral do código.
Na segunda-feira faremos uma rodada conjunta de avaliação do código.