<?php

namespace App\Http\Controllers;

use App\Http\Requests\Bills\SaveSelecteddBillRequest;
use App\Http\Responses\SuccessResponse;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class BillController extends Controller
{
    public function index()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $bills = Bill::with('categories')
            ->where('organization_id', $selectedOrganizationId)
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('SuperAdmin/Bills/Index', compact('bills'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->only('name', 'number', 'bik', 'bank_name');
        $errors = NULL;

        $selectedOrganizationId = Session::get('selected_organization_id');

        $bill = Bill::where('name', $request->name)->where('organization_id', $selectedOrganizationId)->first();

        if ($bill) {
            $errors = 'Такое название уже существует';
        } else {
            Bill::create([
                'name' => $data['name'],
                'number' => $data['number'],
                'bik' => $data['bik'],
                'bank_name' => $data['bank_name'],
                'organization_id' => $selectedOrganizationId,
            ]);
        }

        $user->update([
            'first_add_organization' => false,
        ]);

        $bills = Bill::where('organization_id', $selectedOrganizationId)->orderBy('id', 'desc')->get();

        return Inertia::render('SuperAdmin/Bills/Index', compact('bills', 'errors'));
    }

    public function update(Request $request, Bill $bill)
    {
        $data = $request->only('name', 'number', 'bik', 'bank_name');

        $errorsArr = [];

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20|min:20',
            'bik' => 'required|string|max:9|min:9',
            'bank_name' => 'required|string|max:255',
        ]);


        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorsArr = $errors->all();

            return response()->json([
                'success' => FALSE,
                'message' => 'Ошибки',
                'errors' => $errorsArr
            ]);

        } else {


            $selectedOrganizationId = Session::get('selected_organization_id');

            $billAnother = Bill::where('name', $data['name'])->where('id', '<>', $bill->id)->where('organization_id', $selectedOrganizationId)->first();

            if ($billAnother) {
                $errorsArr['name'] = 'Такое название уже существует';

                return response()->json([
                    'success' => FALSE,
                    'message' => 'Ошибки',
                    'errors' => $errorsArr
                ]);

            } else {
                $bill->update($data);

                return response()->json([
                    'success' => TRUE,
                    'message' => 'Форма успешно сохранена',
                    'errors' => []
                ]);
            }
        }
    }

    public function edit(Bill $bill)
    {
        return Inertia::render('SuperAdmin/Bills/Components/BillsEdit', compact('bill'));
    }

    public function destroy(Request $request)
    {
        $bill = Bill::find($request->billId);

        $selectedOrganizationId = Session::get('selected_organization_id');

        $countBillsInOrganization = Bill::where('organization_id', $selectedOrganizationId)->count();

        if (!$bill) {
            return response()->json([
                'status' => 'error',
                'message' => 'Счет не найден.'
            ], 404);
        }

        if ($countBillsInOrganization == 1) {
            return response()->json([
                'status' => 'errorCount',
                'message' => ''
            ]);
        }

        //if ($bill->categories->isEmpty()) {
            $bill->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Счет успешно удален.'
            ]);
//        } else {
//            return response()->json([
//                'status' => 'error',
//                'message' => 'Пока счет привязан к одному или нескольким разделам каталога, его удалить нельзя. Отвяжите этот счет от каталога и повторите попытку.'
//            ]);
//        }
    }

    public function saveSelectedBill(SaveSelecteddBillRequest $request): SuccessResponse
    {
        $user = auth()->user();
        $billId = $request->selectedBill['id'];
        $existingPivot = $user->bills()->wherePivot('bill_id', $billId)->first();

        if (!$existingPivot) {
            $user->bills()->wherePivot('organization_id', Session::get('selected_organization_id'))->detach();
            $user->bills()->attach($billId, ['organization_id' => Session::get('selected_organization_id'), 'user_id' => $user->id]);
        }

        return new SuccessResponse('Счет успешно сохранен');
    }
}
