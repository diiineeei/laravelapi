<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Hour;
use App\Models\Employees;
use Illuminate\Support\Facades\Validator;

class APIController extends Controller
{
    /**
     * Importa dados da API para o banco de dados
     *
     * @return string
     */
    public function dataImportAPI(): string
    {
        // Cria uma instância do cliente GuzzleHttp
        $client = new Client();

        // Faz uma requisição GET para a URL da API
        $response = $client->get('https://63zs5guqxkzp3oxyxtzmdwrypa0bvonh.lambda-url.sa-east-1.on.aws/');

        // Decodifica a resposta em formato JSON para um array associativo
        $data = json_decode($response->getBody(), true);

        // Define as regras de validação para os dados importados
        $rules = [
            '*.id' => 'required|integer',
            '*.funcionario' => 'required|string',
            '*.matricula' => 'required|string',
            '*.tipo' => 'required|in:CLT,PJ',
            '*.data_admissao' => 'required|date_format:d/m/Y',
        ];

        // Cria um validador com base nas regras e nos dados importados
        $validator = Validator::make($data, $rules);

        // Verifica se a validação falhou
        if ($validator->fails()) {
            // Se a validação falhar, você pode retornar uma resposta de erro ou realizar outras ações
            return "Erro de validação: " . $validator->errors()->first();
        }

        // Percorre os dados importados e insere no banco de dados
        foreach ($data as $item) {
            $formattedDate = date('Y-m-d', strtotime($item['data_admissao']));

            DB::table('employees')->insert([
                'id' => $item['id'],
                'employees' => $item['funcionario'],
                'matricula' => $item['matricula'],
                'tipo' => $item['tipo'],
                'data_admissao' => $formattedDate
            ]);
        }

        // Retorna uma mensagem de sucesso
        return "Dados importados com sucesso!";
    }

    /**
     * Obtém os funcionários paginados
     *
     * @param Request $request
     * @param int $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployees(Request $request, $page = 1): \Illuminate\Http\JsonResponse
    {
        // Obtém os funcionários paginados do banco de dados
        $employees = DB::table('employees')->paginate(10, ['*'], 'page', $page);

        // Cria uma resposta com os dados paginados
        $response = [
            'total' => $employees->total(),
            'per_page' => $employees->perPage(),
            'current_page' => $employees->currentPage(),
            'data' => $employees->items()
        ];

        // Retorna a resposta em formato JSON
        return response()->json($response);
    }

    /**
     * Atualiza o valor da hora de trabalho de um funcionário
     *
     * @param Request $request
     * @param string $matricula
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateHourValue(Request $request, $matricula): \Illuminate\Http\JsonResponse
    {
        // Obtém o valor da hora de trabalho do corpo da requisição
        $hourValue = $request->input('hour_value');

        // Atualiza o valor da hora de trabalho no banco de dados com base na matrícula do funcionário
        DB::table('employees')->where('matricula', $matricula)->update(['hour_value' => $hourValue]);

        // Retorna uma resposta de sucesso
        return response()->json(['message' => 'Valor da hora de trabalho atualizado com sucesso']);
    }

    /**
     * Armazena as horas trabalhadas de um funcionário
     *
     * @param Request $request
     * @param string $matricula
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeHours(Request $request, $matricula): \Illuminate\Http\JsonResponse
    {
        // Valida os dados da requisição
        $validatedData = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer',
            'total_hours' => 'required|numeric',
        ]);

        // Encontra o funcionário pelo número de matrícula
        $employees = Employees::where('matricula', $matricula)->first();

        // Verifica se o funcionário foi encontrado
        if (!$employees) {
            return response()->json(['message' => 'Funcionário não encontrado'], 404);
        }

        // Cria uma nova instância do modelo Hour
        $hours = new Hour;
        $hours->year = $validatedData['year'];
        $hours->month = $validatedData['month'];
        $hours->total_hours = $validatedData['total_hours'];

        // Relaciona o funcionário com as horas trabalhadas
        $hours->employees()->associate($employees);
        $hours->save();

        // Retorna uma resposta de sucesso
        return response()->json(['message' => 'Horas cadastradas com sucesso']);
    }

    /**
     * Obtém o valor total e as horas totais de um funcionário pelo número de matrícula e mês
     *
     * @param string $matricula
     * @param int $mes
     * @return \Illuminate\Http\JsonResponse
     */
    public function getValueByMatriculaAndMonth($matricula, $mes): \Illuminate\Http\JsonResponse
    {
        // Buscar o funcionário pelo número de matrícula
        $employees = Employees::where('matricula', $matricula)->first();

        // Verifica se o funcionário foi encontrado
        if (!$employees) {
            return response()->json(['message' => 'Funcionário não encontrado'], 404);
        }

        // Buscar as horas do funcionário no mês especificado
        $horas = Hour::where('employees_id', $employees->id)
            ->where('month', $mes)
            ->get();

        // Calcular o valor total e as horas totais
        $totalValue = 0;
        $totalHours = 0;

        foreach ($horas as $hora) {
            $totalValue += $hora->total_hours * $employees->hour_value;
            $totalHours += $hora->total_hours;
        }

        // Retorna os dados em formato JSON
        return response()->json([
            'name' => $employees->employees,
            'registry' => $employees->matricula,
            'total_value' => $totalValue,
            'total_hours' => $totalHours
        ]);
    }
}
