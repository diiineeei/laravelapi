<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Hour;
use App\Models\Funcionario;

class APIController extends Controller
{
    public function consumeAPI()
    {
        $client = new Client();
        $response = $client->get('https://63zs5guqxkzp3oxyxtzmdwrypa0bvonh.lambda-url.sa-east-1.on.aws/');
        $data = json_decode($response->getBody(), true);

        foreach ($data as $item) {
            $formattedDate = date('Y-m-d', strtotime($item['data_admissao']));

            DB::table('funcionarios')->insert([
                'id' => $item['id'],
                'funcionario' => $item['funcionario'],
                'matricula' => $item['matricula'],
                'tipo' => $item['tipo'],
                'data_admissao' => $formattedDate
            ]);
        }


        return "Dados importados com sucesso!";
    }

    public function getFuncionarios(Request $request, $page = 1)
    {
        $funcionarios = DB::table('funcionarios')->paginate(10, ['*'], 'page', $page);

        $response = [
            'total' => $funcionarios->total(),
            'per_page' => $funcionarios->perPage(),
            'current_page' => $funcionarios->currentPage(),
            'data' => $funcionarios->items()
        ];

        return response()->json($response);
    }

    public function updateHourValue(Request $request, $matricula)
    {
        $hourValue = $request->input('hour_value');

        DB::table('funcionarios')->where('matricula', $matricula)->update(['hour_value' => $hourValue]);

        return response()->json(['message' => 'Valor da hora de trabalho atualizado com sucesso']);
    }


    public function storeHours(Request $request, $matricula)
    {
        $requestData = $request->all();

        // Encontra o funcionário pelo número de matrícula
        $funcionario = Funcionario::where('matricula', $matricula)->first();

        if (!$funcionario) {
            return response()->json(['message' => 'Funcionário não encontrado'], 404);
        }

        $hours = new Hour;
        $hours->year = $requestData['year'];
        $hours->month = $requestData['month'];
        $hours->total_hours = $requestData['total_hours'];

        // Relaciona o funcionário com as horas
        $hours->funcionario()->associate($funcionario);
        $hours->save();

        return response()->json(['message' => 'Horas cadastradas com sucesso']);
    }
    public function getValueByMatriculaAndMonth($matricula, $mes)
    {
        // Buscar o funcionário pelo número de matrícula
        $funcionario = Funcionario::where('matricula', $matricula)->first();

        if (!$funcionario) {
            return response()->json(['message' => 'Funcionário não encontrado'], 404);
        }

        // Buscar as horas do funcionário no mês especificado
        $horas = Hour::where('funcionario_id', $funcionario->id)
            ->where('month', $mes)
            ->get();

        // Calcular o valor total e as horas totais
        $totalValue = 0;
        $totalHours = 0;

        foreach ($horas as $hora) {
            $totalValue += $hora->total_hours * $funcionario->hour_value;
            $totalHours += $hora->total_hours;
        }

        return response()->json([
            'name' => $funcionario->funcionario,
            'registry' => $funcionario->matricula,
            'total_value' => $totalValue,
            'total_hours' => $totalHours
        ]);
    }
}
