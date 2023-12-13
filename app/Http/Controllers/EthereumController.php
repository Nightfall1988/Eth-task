<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class EthereumController extends Controller
{
    public function fetchRecentTransactions()
    {
        $minValue = 9000000;

        $infuraApiKey = '0e7f70f496f143e5b023da30c994537a';

        $infuraEndpoint = "https://mainnet.infura.io/v3/{$infuraApiKey}";

        $client = new Client();

        // Calculate the timestamp for 10 minutes ago
        $timestampLast10Minutes = time() - (10 * 60);

        // JSON-RPC request payload for eth_getLogs without specifying address
        $data = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getLogs',
            'params' => [
                [
                    'topics' => [], // You can filter by topics if needed
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

        // Make the JSON-RPC request to Infura
        $response = $client->post($infuraEndpoint, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($data),
        ]);

        // Decode the JSON response
        $result = json_decode($response->getBody(), true);

        // Check for errors
        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 500);
        }

        // Extract the transactions
        $transactions = $result['result'];

        
        // Filter transactions by value
        $filteredTransactions = array_filter($transactions, function ($transaction) use ($minValue) {
            $value = hexdec($transaction['data']);
            return $value >= $minValue;
        });

        foreach ($filteredTransactions as $transaction) {
            var_dump($transaction['topics']);
            echo "<br>";
            echo "<br>";
            echo "<br>";
        }
        return response()->json(['transactions' => $filteredTransactions]);
    
    }
        // dd($response->body());
        // Process $response->body()
        


        // Your Infura API Key
        // $infuraApiKey = '6971161eadfe4f60942b748751d70e8d';

        // // Ethereum address to fetch transactions for
        // $ethereumAddress = '0x42782224d937c894Fe9A70bb7DCdFF01ABbd82D2';

        // // Infura endpoint URL
        // $infuraUrl = "https://mainnet.infura.io/v3/$infuraApiKey";

        // // Create a Guzzle HTTP client
        // $client = new Client();

        // try {
        //     // Fetch recent transactions from Infura
        //     $response = $client->post("$infuraUrl?module=account&action=txlist&address=$ethereumAddress");

        //     // Decode the JSON response
        //     $transactions = json_decode($response->getBody(), true);

        //     // Process and return the transactions
        //     return $this->processTransactions($transactions);
        // } catch (\Exception $e) {
        //     // Handle exceptions (e.g., connection error)
        //     return response()->json(['error' => $e->getMessage()], 500);
        // }
    

    private function processTransactions(array $transactions)
    {
        // Your processing logic goes here
        // Extract relevant information from $transactions array

        // For example, you can return the raw transactions for now
        return response()->json(['transactions' => $transactions]);
    }
}
