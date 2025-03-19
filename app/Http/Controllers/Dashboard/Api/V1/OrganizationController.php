<?php

namespace App\Http\Controllers\Dashboard\Api\V1;

use App\Helpers\AccessHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Organization\EditOrganizationRequest;
use App\Http\Responses\SuccessResponse;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{

    public function getOrganizations(Request $request) {
        if (AccessHelper::hasAccessToOrganization()) {
            //        dd($request->all());
            $dateFilter = $request->input('dateFilter', FALSE);
            $revenueFilter = $request->input('revenueFilter', FALSE);
            $startDate = $request->input('date.startDate', NULL);
            $endDate = $request->input('date.endDate', NULL);
            $minRevenue = $request->input('minRevenue', NULL);
            $maxRevenue = $request->input('maxRevenue', NULL);
            $statusFilter = $request->input('status', NULL);

            $organizations = Organization::query()->orderBy('id', 'DESC')->with('activityType')->withTrashed();

            //        if ($dateFilter && $startDate && $endDate) {
            //            $organizations->whereBetween('registration_date', [$startDate, $endDate]);
            //        }
            //
            //        if ($revenueFilter && $minRevenue !== null && $maxRevenue !== null) {
            //            $organizations->whereBetween('revenue', [$minRevenue, $maxRevenue]);
            //        }

            if ($statusFilter && is_array($statusFilter) && count($statusFilter) > 0) {
                // Применяем фильтрацию по всем статусам в массиве
                $organizations->whereIn('status', $statusFilter);
            }

            $organizations = $organizations->paginate(200);

            $data = $organizations->getCollection();
            return response()->json([
                'data'         => $data,  // Данные организаций
                'current_page' => $organizations->currentPage(), // Текущая страница
                'last_page'    => $organizations->lastPage(), // Общее количество страниц
                'total'        => $organizations->total(), // Общее количество записей
            ], 200);
            //
            //        return response()->json($data, $organizations->currentPage(),200);
        }
    }

    public function checkAgreementNumberExist($agency_agreement_number, $organizationId): bool
    {
        return Organization::where('agency_agreement_number', $agency_agreement_number)
            ->where('id', '!=', $organizationId)
            ->exists();
    }

    public function saveOrganization(EditOrganizationRequest $request)
    {
        $organization = Organization::find($request->organizationId);

        if ($organization) {
            if (isset($request->status)) {
                if ($organization->status !== 'deleted'){
                    $organization->status = $request->status;
                }
            }

            if (isset($request->comment)) {
                $organization->comment = $request->comment;
            }
            if (isset($request->agency_agreement_number)) {
                if ($this->checkAgreementNumberExist($request->agency_agreement_number,$request->organizationId)){
                    return response()->json(['exists' => true]);
                }
                $organization->agency_agreement_number = $request->agency_agreement_number;
            }
            if (isset($request->agency_agreement_date)) {
                $organization->agency_agreement_date = Carbon::parse($request->agency_agreement_date)->toDateString();
            }

            $organization->save();

            return new SuccessResponse('Организация обновлена успешно');
        }
    }
}
