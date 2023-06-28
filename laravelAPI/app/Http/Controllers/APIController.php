<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Hour;
use App\Models\employees;

class APIController extends Controller
{
    public function dataImportAPI(): string
    {
        $client = new Client();
        $response = $client->get('https://63zs5guqxkzp3oxyxtzmdwrypa0bvonh.lambda-url.sa-east-1.on.aws/');
        $data = json_decode($response->getBody(), true);

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


        return "Dados importados com sucesso!";
    }

    public function getEmployees(Request $request, $page = 1): \Illuminate\Http\JsonResponse
    {
        $employees = DB::table('employees')->paginate(10, ['*'], 'page', $page);

        $response = [
            'total' => $employees->total(),
            'per_page' => $employees->perPage(),
            'current_page' => $employees->currentPage(),
            'data' => $employees->items()
        ];

        return response()->json($response);
    }

    public function updateHourValue(Request $request, $matricula): \Illuminate\Http\JsonResponse
    {
        $hourValue = $request->input('hour_value');

        DB::table('employees')->where('matricula', $matricula)->update(['hour_value' => $hourValue]);

        return response()->json(['message' => 'Valor da hora de trabalho atualizado com sucesso']);
    }


    public function storeHours(Request $request, $matricula): \Illuminate\Http\JsonResponse
    {
        $requestData = $request->all();

        // Encontra o funcionário pelo número de matrícula
        $employees = Employees::where('matricula', $matricula)->first();

        if (!$employees) {
            return response()->json(['message' => 'Funcionário não encontrado'], 404);
        }

        $hours = new Hour;
        $hours->year = $requestData['year'];
        $hours->month = $requestData['month'];
        $hours->total_hours = $requestData['total_hours'];

        // Relaciona o funcionário com as horas
        $hours->employees()->associate($employees);
        $hours->save();

        return response()->json(['message' => 'Horas cadastradas com sucesso']);
    }
    public function getValueByMatriculaAndMonth($matricula, $mes): \Illuminate\Http\JsonResponse
    {
        // Buscar o funcionário pelo número de matrícula
        $employees = Employees::where('matricula', $matricula)->first();

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

        return response()->json([
            'name' => $employees->employees,
            'registry' => $employees->matricula,
            'total_value' => $totalValue,
            'total_hours' => $totalHours
        ]);
    }
}
