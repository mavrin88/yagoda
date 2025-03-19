<?php

namespace App\Http\Controllers;

use App\Models\MasterShift;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class UserAccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return Inertia::render('UserAccount/Index', compact('user'));
    }

    public function updatePhone(User $user)
    {
        $user = Auth::user();

        $user->phone = request('phone');
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Номер успешно изменён.',
        ]);
    }

    public function delete(User $user)
    {
        $organiazations = Organization::where('phone', $user->phone)->count();

        $selectedOrganizationId = Session::get('selected_organization_id');

        if ($organiazations > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Удалить аккаунт невозможно. К вашему номеру привязана организация. Замените в организации ваш номер на другой. И после этого вы сможете удалить свой аккаунт.',
            ]);
        }

        $user->first_name = 'Без имени';
        $user->last_name = null;
        $user->photo_path = null;
        $user->card_number = null;
        $user->status = 'deleted';

        $totalDeletedUsers = User::where('status', 'deleted')->count();

        $user->phone = '+0' . str_pad($totalDeletedUsers + 1, 9, '0', STR_PAD_LEFT);

        $user->save();

        DB::table('organization_user')
            ->where('user_id', $user->id)
            ->where('organization_id', $selectedOrganizationId)
            ->delete();

        MasterShift::where('organization_id', $selectedOrganizationId)->where('user_id', $user->id)->delete();

        Auth::logout();
        Session::flush();

        return response()->json([
            'message' => 'Аккаунт успешно удалён.',
            'success' => true,
        ]);
    }
}
