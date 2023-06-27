<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

}
