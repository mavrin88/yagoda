<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
//use App\Models\Role;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Index');
    }

    public function dashboard_roles(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Roles/Index');
    }

    public function dashboard_organizations(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Organizations/Index', [
            'filters' => Request::all('search', 'trashed'),
            'organizations' => Auth::user()->account->organizations()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($organization) => [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'full_name' => $organization->full_name,
                    'phone' => $organization->phone,
                    'contact_name' => $organization->contact_name,
                    'contact_phone' => $organization->contact_phone,
                    'inn' => $organization->inn,
                    'legal_address' => $organization->legal_address,
                    'registration_number' => $organization->registration_number,
                    'acquiring_fee' => $organization->acquiring_fee,
                    'photo_path' => $organization->photo_path,
                    'tips_1' => $organization->tips_1,
                    'tips_2' => $organization->tips_2,
                    'tips_3' => $organization->tips_3,
                    'tips_4' => $organization->tips_4,
                    'deleted_at' => $organization->deleted_at,
                ]),
        ]);
    }

    public function dashboard_tests(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Tests/Index');
    }
    public function dashboard_categories(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Categories/Index');
    }

    public function dashboard_users(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Users/Index');
    }

    public function dashboard_service_settings(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Settings/Index');
    }
}
