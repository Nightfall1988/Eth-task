<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class EthereumController extends Controller
{
    public function fetchRecentTransactions()
    {
        $minValue = 10000;

        $infuraApiKey = '0e7f70f496f143e5b023da30c994537a';

        $infuraEndpoint = "https://mainnet.infura.io/v3/{$infuraApiKey}";

        $client = new Client();

        $timestampLast10Minutes = time() - (10 * 60);

        $data = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getLogs',
            'params' => [
                [
                    'topics' => [], 
                    'fromBlock' => '0xCB3D',
                    'toBlock' => '0x7B737',
                    'mined' => true,
                    'limit' => 5,
                    'timeFilter' => [
                        'from' => '0x' . dechex($timestampLast10Minutes),
                        'to' => '0x' . dechex(time()),
                    ],
                ],
            ],
            'id' => 1,
        ];

        $response = $client->post($infuraEndpoint, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($data),
        ]);

        $result = json_decode($response->getBody(), true);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 500);
        }

        $transactions = $result['result'];

                $filteredTransactions = array_filter($transactions, function ($transaction) use ($minValue) {
            $value = hexdec($transaction['data']);
            return $value >= $minValue;
        });

        foreach ($filteredTransactions as $transaction) {
            var_dump($transaction);
            echo "<br>";
        }
        return response()->json(['transactions' => $filteredTransactions]);
    
    }
    
    private function processTransactions(array $transactions)
    {
        return response()->json(['transactions' => $transactions]);
    }
}
