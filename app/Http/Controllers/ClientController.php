<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function searchClients(Request $request)
    {
        $phone = $request->query('phone');
        $organizationId = $request->query('organizationId');

        if (!$phone || strlen($phone) < 5) {
            return response()->json([]);
        }

        $clients = Client::where('phone', 'like', "%$phone%")->get();

        return response()->json($clients);
    }

    public function saveClient(Request $request)
    {
        $validatedData = $request->validate([
            'phone' => 'required|string|max:18',
            'name' => 'string|max:255|nullable',
            'organization_id' => 'nullable|exists:organizations,id',
        ]);

        $phoneDigits = $validatedData['phone'];

        $clientExists = Client::where('phone', 'like', "%$phoneDigits%")->first();

        if ($clientExists) {

            $clientExists->update([
                'name' => $validatedData['name'] ?? null,
            ]);

            return response()->json([
                'message' => 'Клиент усешно обновлен',
                'client' => $clientExists,
            ], 200);
        }

        $client = Client::create([
            'phone' => $phoneDigits,
            'name' => $validatedData['name'] ?? null,
            'organization_id' => $validatedData['organization_id'],
        ]);

        return response()->json([
            'message' => 'Клиент успешно создан',
            'client' => $client,
        ], 201);
    }
}
