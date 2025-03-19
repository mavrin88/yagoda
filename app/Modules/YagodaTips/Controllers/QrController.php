<?php

namespace App\Modules\YagodaTips\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\YagodaTips\Helpers\AccessHelperGroups;
use App\Modules\YagodaTips\Models\Group;
use App\Modules\YagodaTips\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Illuminate\Support\Facades\Storage;

class QrController extends Controller
{
    public function index()
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $data = [
                'dinamic' => QrCode::where('type', 'dinamic')->where('group_id', $selectedGroupId)->get(),
                'static' => QrCode::where('type', 'static')->where('group_id', $selectedGroupId)->get()
            ];

            return Inertia::render('YagodaTips/Qr/Index', compact('data'));
        }
    }

    public function single($id)
    {
        if (AccessHelperGroups::hasAccessToGroup()) {

            $data = [
                'qrCode' => QrCode::findOrFail($id)
            ];

            return Inertia::render('YagodaTips/Qr/Single/Index', compact('data'));
        }
    }

    public function static($id)
    {
        if (AccessHelperGroups::hasAccessToGroup()) {

            $data = [
                'qrCode' => QrCode::findOrFail($id)
            ];

            return Inertia::render('YagodaTips/Qr/Static/Single/Index', compact('data'));
        }
    }

    public function stand($id)
    {
        if (AccessHelperGroups::hasAccessToGroup()) {

            $data = [
                'qrCode' => QrCode::findOrFail($id)
            ];

            return Inertia::render('YagodaTips/Qr/Single/Index', compact('data'));
        }
    }

    public function addNewForName()
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $group = Group::find($selectedGroupId);
            $qrCount = QrCode::where('group_id', $selectedGroupId)->count();

            $random = 'https://yagoda-pay.ru/order/' . Str::random(6) . Str::upper(Str::random(6)) . Str::random(8);

            $qrCode = QrCode::create([
                'name' => $group->name . '_' . $qrCount + 1,
                'link' => $random,
                'type' => 'dinamic',
                'group_id' => $selectedGroupId
            ]);

            $data = [
                'qrCode' => $qrCode,
                'group' => $group
            ];

            return Inertia::render('YagodaTips/Qr/Single/Index', compact('data'));
        }
    }

    public function addNewForScan()
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $data = [];

            return Inertia::render('YagodaTips/Qr/Static/Add/Index', compact('data'));
        }
    }

    public function checkUniqueness(Request $request)
    {
        $link = $request->input('link');

        $isUnique = QrCode::where('link', $link)->doesntExist();

        return response()->json(['unique' => $isUnique]);
    }

    public function generatePdf($id)
    {
        $qrCode = QrCode::findOrFail($id);

        if (!$qrCode->link) {
            return response()->json(['error' => 'Ссылка для QR-кода не найдена'], 400);
        }

        $qrCodeImage = QrCodeGenerator::format('png')->size(300)->generate($qrCode->link);

        $filePath = 'qr_codes/qr_code_' . $id . '.png';
        Storage::disk('public')->put($filePath, $qrCodeImage);
        $qrCodeUrl = Storage::disk('public')->url($filePath);

        $data = [
            'qr_url' => $qrCodeUrl, // URL изображения QR-кода
            'link' => $qrCode->link, // Исходная ссылка
        ];

        $pdf = Pdf::loadView('qr_code', $data);

        // Storage::disk('public')->delete($filePath);

        return $pdf->stream('qr_code_' . $id . '.pdf');
    }

    public function deleteQrCode($id)
    {
        $qrCode = QrCode::findOrFail($id);

        $qrCode->delete();

        return redirect()->route('tips.qr.index')->with('success', 'QR-код успешно удален.');
    }

    public function updateName($id, Request $request)
    {
        $qrCode = QrCode::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $qrCode->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->back()->with('success', 'Имя QR-кода успешно обновлено.');
    }

    public function stands()
    {
        return Inertia::render('YagodaTips/Qr/Stands/Index');
    }

    public function saveQrCode(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|string|max:255|unique:qr_codes,link',
            'type' => 'required|string|in:static,dinamic',
        ]);

        if (empty($validated['name'])) {
            return response()->json([
                'status' => false,
                'message' => 'Имя не указано',
                'link' => $validated['link'],
            ], 200);
        }

        $selectedGroupId = Session::get('selected_organization_id');

        if (!$selectedGroupId) {
            return response()->json([
                'status' => false,
                'message' => 'Группа не выбрана',
            ], 400);
        }

        $qrCode = QrCode::create([
            'name' => $validated['name'],
            'link' => $validated['link'],
            'type' => $validated['type'],
            'group_id' => $selectedGroupId,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'QR-код успешно сохранен',
            'qrCode' => $qrCode,
        ], 201);
    }

    public function link()
    {
        dd('yagoda-tips::link');
    }
    public function settings()
    {
        dd('yagoda-tips::settings');
    }
    public function pay()
    {
        dd('yagoda-tips::pay');
    }
}
