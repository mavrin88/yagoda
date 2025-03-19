<?php

namespace App\Modules\YagodaTips\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Modules\YagodaTips\Helpers\AccessHelperGroups;
use App\Modules\YagodaTips\Models\Group;
use App\Modules\YagodaTips\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class TipDistributionController extends Controller
{
    public function index()
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $group = Group::find($selectedGroupId);

            return Inertia::render('YagodaTips/TipsDistribution/Index', compact('group'));
        }
    }

    public function save(Request $request)
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $validator = Validator::make($request->all(), [
                'masterPercentage' => 'required|numeric|min:0|max:100',
                'adminPercentage' => 'required|numeric|min:0|max:100',
                'staffPercentage' => 'required|numeric|min:0|max:100',
                'organizationPercentage' => 'required|numeric|min:0|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'errors' => $validator->errors()
                    ], 422);
            }

            $totalPercentage = $request->masterPercentage +
                $request->adminPercentage +
                $request->staffPercentage +
                $request->organizationPercentage;

            if ($totalPercentage !== 100) {
                return response()->json(
                    [
                        'errors' => [
                            'Total percentage must be 100%'
                        ]
                    ], 422);
            }

            try {
                DB::transaction(function () use ($selectedGroupId, $request) {
                    $group = Group::findOrFail($selectedGroupId);
                    $group->update([
                        'master_percentage' => $request->masterPercentage,
                        'admin_percentage' => $request->adminPercentage,
                        'staff_percentage' => $request->staffPercentage,
                        'organization_percentage' => $request->organizationPercentage
                    ]);
                });

                return response()->json(
                    [
                        'message' => 'Tip distribution saved successfully'
                    ]
                );
            } catch (\Exception $e) {
                return response()->json(
                    [
                        'errors' => [
                            'An error occurred while saving the tip distribution'
                        ]
                    ], 500);
            }
        }
    }

    public function showMessageTipDistribution(Request $request)
    {
        Group::where('id', $request->groupId)->update([
            'show_distributions_message' => true
        ]);
    }
}
