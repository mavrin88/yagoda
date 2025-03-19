<?php

    use App\Http\Controllers\AdminController;
    use App\Http\Controllers\AnonymousController;
    use App\Http\Controllers\Dashboard\IndexController;
    use App\Http\Controllers\Auth\AuthenticatedSessionController;
    use App\Http\Controllers\BillController;
    use App\Http\Controllers\ClientsController;
    use App\Http\Controllers\ContactsController;
    use App\Http\Controllers\FormController;
    use App\Http\Controllers\ImagesController;
    use App\Http\Controllers\EmployeeController;
    use App\Http\Controllers\MasterController;
    use App\Http\Controllers\MasterShiftController;
    use App\Http\Controllers\OrderController;
    use App\Http\Controllers\OrganizationsController;

    use App\Http\Controllers\PageController;
    use App\Http\Controllers\ProductController;
    use App\Http\Controllers\QrCodeController;
    use App\Http\Controllers\ReportsController;
    use App\Http\Controllers\RootAdminController;
    use App\Http\Controllers\SettingController;
    use App\Http\Controllers\StaffController;
    use App\Http\Controllers\SuperAdminController;
    use App\Http\Controllers\UserAccountController;
    use App\Http\Controllers\UsersController;
    use App\Http\Controllers\CategoryController;
    use App\Http\Middleware\EnsureRegistrationComplete;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Cookie;
    use Symfony\Component\HttpFoundation\Response;


    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

//    Route::get('/clear-wrong-xsrf', function () {
//        Cookie::queue(Cookie::forget('XSRF-TOKEN', '/', 'yagoda.team'));
//        Cookie::queue(Cookie::forget('yagodateam_session', '/', 'yagoda.team'));
//        Cookie::queue(Cookie::forget('yagodateam_session', '/', '.yagoda.team'));
//        Cookie::queue(Cookie::forget('yagoda_cookie_session', '/', 'yagoda.team'));
//        Cookie::queue(Cookie::forget('yagoda_cookie_session', '/', '.yagoda.team'));
//        //    Cookie::queue(Cookie::forget('XSRF-TOKEN', '/', '.yagoda.team'));
//
//        return response()->json(['message' => 'Wrong XSRF-TOKEN deleted']);
//    });

    Route::get('PaySuccess', [ClientsController::class, 'PaySuccess'])->name('PaySuccess');

    Route::post('saveEmailInPaySuccess', [ClientsController::class, 'saveEmailInPaySuccess'])->name('saveEmailInPaySuccess');

    // Auth

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login')->middleware('guest');

    Route::get('login/newOrganization', [AuthenticatedSessionController::class, 'index'])->name('login.newOrganization')->middleware('guest');

    Route::post('checkUserExists', [AuthenticatedSessionController::class, 'checkUserExists'])->name('login.checkUserExists')->middleware('guest');
//    Route::post('checkUserExists', [SmsConfirmationController::class, 'checkUserExists'])->name('checkUserExists');

    Route::post('verifySmsCode', [AuthenticatedSessionController::class, 'verifySmsCode'])->name('login.verifySmsCode')->middleware('guest');

    Route::post('getNewSmsCode', [AuthenticatedSessionController::class, 'getNewSmsCode'])->name('login.getNewSmsCode')->middleware('guest');

    Route::post('getNewSmsCodeEditNumber', [AuthenticatedSessionController::class, 'getNewSmsCodeEditNumber']);
    Route::post('verifySmsCodeEditNumber', [AuthenticatedSessionController::class, 'verifySmsCodeEditNumber']);

    Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Административная панель
    Route::get('/dashboard', [IndexController::class, 'index'])->name('dashboard')->middleware('auth');

    Route::get('connect', [AnonymousController::class, 'connect'])->name('landing.connect')->middleware('guest');

    // Orders
    Route::get('qr-code', function () {
        $count = request('count', 1);
        $urls = [];

        for ($i = 0; $i < $count; $i++) {
            $url = 'https://yagoda-pay.ru/order/' . Str::random(6) . Str::upper(Str::random(6)) . Str::random(8);
            $urls[] = $url;
        }

        return view('qrcode', compact('urls'));
    });

    // Отдельный маршрут для отображения формы
    Route::get('qr-code/form', function () {
        return view('qrcode');
    });

    Route::get('order/{link}', [ClientsController::class, 'handleLink'])->name('qr-code.handle-link');

    Route::post('client/makePayment', [ClientsController::class, 'makePayment'])->name('makePayment');

    Route::post('client/makePaymentTest', [ClientsController::class, 'makePayment'])->name('makePaymentTest');

    //Route::post('check_user_exists', [AuthenticatedSessionController::class, 'checkUserExists'])
    //    ->name('login.checkUserExists')
    //    ->middleware('guest');
    //
    //Route::post('verifi_sms', [AuthenticatedSessionController::class, 'verifiSms'])
    //    ->name('login.verifiSms')
    //    ->middleware('guest');
    //
    //Route::post('login', [AuthenticatedSessionController::class, 'store'])
    //    ->name('login.store')
    //    ->middleware('guest');


    // USER SETTINGS

    Route::get('registration', [AuthenticatedSessionController::class, 'registration'])->name('registration')->middleware('auth');

    Route::get('user_settings', [AuthenticatedSessionController::class, 'userSettings'])->name('user_settings')->middleware('auth');

    Route::post('user_settingss', [AuthenticatedSessionController::class, 'userUpdate'])->name('user_settingss.update')->middleware('auth');

    //Route::post('/user_update', [AuthenticatedSessionController::class, 'userUpdate'])
    //    ->name('user_update.update')
    //    ->middleware('auth');

    Route::post('/user_update_account', [AuthenticatedSessionController::class, 'userUpdate'])->middleware('auth');

    Route::post('super_admin/settings/validateCardNumber', [SuperAdminController::class, 'validateCardNumber'])
        ->name('super_admin.validateCardNumber')
        ->middleware('auth');

    Route::middleware([EnsureRegistrationComplete::class])->group(function () {

        Route::get('organizations/choose', [OrganizationsController::class, 'choose'])->name('organizations.choose')->middleware('auth');

        // EMPLOYEE
        Route::get('/employee/statistics', [EmployeeController::class, 'index'])->name('employee')->middleware('auth');

        // USER ACCOUNT
        Route::get('user_account', [UserAccountController::class, 'index'])->name('user_account')->middleware('auth');

        Route::put('user_account/{user}', [UserAccountController::class, 'updatePhone'])->name('user_account.update')->middleware('auth');

        Route::delete('user_account/{user}', [UserAccountController::class, 'delete'])->name('user_account.delete')->middleware('auth');


        Route::get('root_admin', [RootAdminController::class, 'index'])->name('root_admin')->middleware('auth');

        Route::get('root_admin/settings', [RootAdminController::class, 'settings'])->name('root_settings')->middleware('auth');

        Route::get('root_admin/orders', [RootAdminController::class, 'orders'])->name('root_orders')->middleware('auth');

        Route::get('root_admin/tochka_statistics', [RootAdminController::class, 'tochkaStatistics'])->name('tochka_statistics')->middleware('auth');

        Route::get('root_admin/click', [RootAdminController::class, 'click'])->name('click')->middleware('auth');


        Route::post('/settings/save', [SettingController::class, 'save']);

        Route::get('/check-inn', [SettingController::class, 'checkInn']);


        // ПЕРСОНАЛ

        Route::get('staff', [StaffController::class, 'staff'])->name('staff')->middleware('auth');

        Route::get('admin_staff', [StaffController::class, 'admin_staff'])->name('admin_staff')->middleware('auth');

        Route::post('storeUser', [StaffController::class, 'storeUser'])->name('storeUser.store')->middleware('auth');

        Route::post('deleteArchivedRole', [StaffController::class, 'deleteArchivedRole'])->name('deleteArchivedRole')->middleware('auth');

        Route::delete('staff/{id}', [StaffController::class, 'deleteStaff'])->name('staff.delete')->middleware('auth');

        Route::get('masters', [StaffController::class, 'masters'])->name('super_admin.masters')->middleware('auth');

        Route::get('admin_masters', [StaffController::class, 'admin_masters'])->name('super_admin.admin_masters')->middleware('auth');

        Route::delete('masters/{id}', [StaffController::class, 'deleteMaster'])->name('super_admin.masters.delete')->middleware('auth');

        Route::get('administrators', [StaffController::class, 'administrators'])->name('super_admin.administrators')->middleware('auth');

        Route::delete('administrators/{id}', [StaffController::class, 'deleteAdministrator'])->name('super_admin.administrators.delete')->middleware('auth');

        Route::get('/super_admin/workforce', [StaffController::class, 'workforce'])->name('super_admin.workforce')->middleware('auth');

        Route::get('/admin/workforce', [StaffController::class, 'adminWorkforce'])->name('super_admin.adminWorkforce')->middleware('auth');

        // SUPER ADMIN

        Route::get('super_admin/bills', [BillController::class, 'index'])->name('super_admin.bills')->middleware('auth');

        Route::post('super_admin/bills', [BillController::class, 'store'])->name('super_admin.storeBills')->middleware('auth');

        //Route::delete('super_admin/bills/{bill}', [BillController::class, 'destroy'])
        //    ->name('super_admin.deleteBills')
        //    ->middleware('auth');

        Route::post('super_admin/bills/delete', [BillController::class, 'destroy'])->name('super_admin.deleteBills')->middleware('auth');

        Route::get('super_admin/bills/{bill}', [BillController::class, 'edit'])->name('super_admin.editBills')->middleware('auth');

        Route::put('super_admin/bills/{bill}', [BillController::class, 'update'])->name('super_admin.updateBills')->middleware('auth');

        Route::get('super_admin/organization', [SuperAdminController::class, 'organization'])->name('super_admin.organization')->middleware('auth');

        Route::get('super_admin/settings', [SuperAdminController::class, 'settings'])->name('super_admin.settings')->middleware('auth');

        Route::get('super_admin/map_links', [SuperAdminController::class, 'mapLinks'])->name('super_admin.map_links')->middleware('auth');

        Route::get('super_admin/other_settings', [SuperAdminController::class, 'otherSettings'])->name('super_admin.other_settings')->middleware('auth');

        Route::post('super_admin/other_settings', [SuperAdminController::class, 'otherSettingsUpdate'])->name('super_admin.other_settings_update')->middleware('auth');

        Route::post('super_admin/settings', [SuperAdminController::class, 'storeSettings'])->name('super_admin.storeSettings')->middleware('auth');

        Route::post('super_admin/settings/tips', [SuperAdminController::class, 'setTips'])->name('super_admin.setTips')->middleware('auth');

        //Route::post('super_admin/settings/validateCardNumber', [SuperAdminController::class, 'validateCardNumber'])
        //    ->name('super_admin.validateCardNumber')
        //    ->middleware('auth');

        Route::get('super_admin/qr_codes', [QrCodeController::class, 'index'])->name('super_admin.qr_codes')->middleware('auth');

        Route::post('super_admin/save_qr_code', [QrCodeController::class, 'store'])->name('super_admin.save_qr_code')->middleware('auth');

        Route::delete('super_admin/qr_codes/{qr_code}', [QrCodeController::class, 'destroy'])->name('super_admin.deleteQrCode')->middleware('auth');

        Route::post('super_admin/qr_codes/check_uniqueness', [QrCodeController::class, 'checkUniqueness'])
            ->name('super_admin.checkUniqueness')
            ->middleware('auth');

        Route::get('generateHideQrCode', [QrCodeController::class, 'generateHideQrCode'])->name('generateHideQrCode')->middleware('auth');

        Route::get('generateQrCode', [QrCodeController::class, 'generateHideQrCode'])->name('generateHQrCode')->middleware('auth');

        //Route::post('super_admin/settings/organization', [SuperAdminController::class, 'storeSettingsOrganization'])
        //    ->name('super_admin.storeSettingsOrganization')
        //    ->middleware('auth');
        //
        //Route::post('super_admin/settings/organization_contact', [SuperAdminController::class, 'storeSettingsOrganizationContact'])
        //    ->name('super_admin.storeSettingsOrganizationContact')
        //    ->middleware('auth');

        Route::get('super_admin/services_products', [CategoryController::class, 'index'])->name('super_admin.services_products')->middleware('auth');

        Route::get('super_admin/categories', [CategoryController::class, 'showCategories'])->name('super_admin.categories')->middleware('auth');

        Route::get('super_admin/add_category', [CategoryController::class, 'addCategory'])->name('super_admin.addCategory')->middleware('auth');

        Route::get('super_admin/edit_category/{category}', [CategoryController::class, 'editCategory'])->name('super_admin.editCategory')->middleware('auth');

        Route::post('super_admin/save_category', [CategoryController::class, 'saveCategory'])->name('super_admin.saveCategory')->middleware('auth');

        Route::post('super_admin/category/store', [CategoryController::class, 'store'])->name('super_admin.store')->middleware('auth');

        Route::post('super_admin/delete_category', [CategoryController::class, 'delete'])->name('super_admin.delete')->middleware('auth');

        Route::get('super_admin/category_products/{category}', [ProductController::class, 'index'])->name('super_admin.catalog_products')->middleware('auth');

        Route::get('super_admin/add_product/{category}', [ProductController::class, 'addProduct'])->name('super_admin.addProduct')->middleware('auth');

        Route::get('super_admin/edit_product/{product}', [ProductController::class, 'editProduct'])->name('super_admin.editProduct')->middleware('auth');

        Route::post('super_admin/save_product/', [ProductController::class, 'saveProduct'])->name('super_admin.saveProduct')->middleware('auth');

        Route::post('super_admin/product/store/', [ProductController::class, 'store'])->name('super_admin.storeProduct')->middleware('auth');

        Route::post('super_admin/hide_product', [ProductController::class, 'hide'])->name('super_admin.hide_product')->middleware('auth');

        Route::get('super_admin/tips/', [SuperAdminController::class, 'tips'])->name('super_admin.tips')->middleware('auth');

        Route::post('super_admin/filter_tips_statistics/', [SuperAdminController::class, 'filterTipsStatistics'])
            ->name('super_admin.tips_statistics')
            ->middleware('auth');


        Route::get('super_admin/orders_statistics/', [SuperAdminController::class, 'ordersStatistics'])
            ->name('super_admin.orders_statistics')
            ->middleware('auth');

        Route::post('super_admin/filter_orders_statistics/', [SuperAdminController::class, 'filterOrdersStatistics'])
            ->name('super_admin.orders_statistics_filters')
            ->middleware('auth');

        Route::get('super_admin/reports/', [SuperAdminController::class, 'getReports'])->name('super_admin.reports')->middleware('auth');

        Route::get('super_admin/useful_videos', [SuperAdminController::class, 'UsefulVideos'])->name('super_admin.useful_videos')->middleware('auth');

        Route::get('super_admin/after_registration', [SuperAdminController::class, 'AfterRegistration'])
            ->name('super_admin.after_registration')
            ->middleware('auth');


        Route::get('tip_distribution', [SuperAdminController::class, 'tipDistribution'])
            //    ->name('super_admin.tips')
            ->middleware('auth');

        Route::post('tip_distribution', [SuperAdminController::class, 'saveTipDistribution'])
            //    ->name('super_admin.tips_store')
            ->middleware('auth');

        Route::post('tip_distribution/show_message', [SuperAdminController::class, 'showMessageTipDistribution'])
            //    ->name('super_admin.tips_store')
            ->middleware('auth');


        // Admin

        // Orders

        Route::get('orders', [OrderController::class, 'index'])->name('orders')->middleware('auth');

        Route::post('orders/filter', [OrderController::class, 'filter'])->name('orders_filter')->middleware('auth');

        Route::get('orders/{id}', [OrderController::class, 'single'])->name('ordersSingle')->middleware('auth');

        Route::post('orders/{id}/order_comment', [OrderController::class, 'orderCommentPost'])->middleware('auth');

        Route::get('orders/{id}/order_comment', [OrderController::class, 'orderCommentGet'])->middleware('auth');


        Route::post('admin/save_order', [OrderController::class, 'saveOrder'])->name('saveOrder')->middleware('auth');

        Route::post('admin/edit_order', [OrderController::class, 'editOrder'])->name('editOrder')->middleware('auth');

        Route::post('admin/save_qr_code_in_oder', [OrderController::class, 'saveQrCodeOrder'])->name('saveQrCodeOrder')->middleware('auth');

        Route::post('admin/delete_order', [OrderController::class, 'deleteOrder'])->name('deleteOrder')->middleware('auth');

        Route::post('/checkQrCodeStatus', [OrderController::class, 'checkQrCodeStatus'])->name('checkQrCodeStatus')->middleware('auth');

        Route::get('admin/statistics', [AdminController::class, 'statistics'])->name('statistics')->middleware('auth');


        Route::get('/', [AdminController::class, 'indexAdmin'])->name('main');


        Route::get('staff_shift', [MasterShiftController::class, 'index'])->name('mastersShift')->middleware('auth');

        Route::get('admin/masters_shift_in_order', [MasterShiftController::class, 'mastersShiftInOrder'])->name('mastersShiftInOrder')->middleware('auth');

        Route::post('admin/save_masters_shift', [MasterShiftController::class, 'saveMastersShift'])->name('saveMastersShift')->middleware('auth');

        Route::get('admin/bill', [AdminController::class, 'bill'])->name('bill')->middleware('auth');

        Route::get('admin/choice_master', [AdminController::class, 'ChoiceMaster'])->name('ChoiceMaster')->middleware('auth');

        Route::get('admin/choice_services', [AdminController::class, 'ChoiceServices'])->name('ChoiceServices')->middleware('auth');

        Route::get('admin/qr_order', [AdminController::class, 'order'])->name('order')->middleware('auth');

        Route::get('admin/order_success', [AdminController::class, 'OrderSuccess'])->name('OrderSuccess')->middleware('auth');


        // Master

        Route::post('master/statistics', [MasterController::class, 'statistics'])->name('MasterStatistics')->middleware('auth');

        Route::post('master/getFilterMaster', [MasterController::class, 'getFilterStatistics'])->name('getFilterStatistics')->middleware('auth');


        // Users

        Route::get('users', [UsersController::class, 'index'])->name('users')->middleware('auth');

        Route::get('users/create', [UsersController::class, 'create'])->name('users.create')->middleware('auth');

        Route::post('users', [UsersController::class, 'store'])->name('users.store')->middleware('auth');

        Route::get('users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit')->middleware('auth');

        Route::put('users/{user}', [UsersController::class, 'update'])->name('users.update')->middleware('auth');

        Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('users.destroy')->middleware('auth');

        Route::put('users/{user}/restore', [UsersController::class, 'restore'])->name('users.restore')->middleware('auth');

        Route::get('organizations/create', [OrganizationsController::class, 'create'])->name('organizations.create')->middleware('auth');

        Route::post('organizations', [OrganizationsController::class, 'store'])->name('organizations.store')->middleware('auth');

        Route::get('organizations/{organization}/edit', [OrganizationsController::class, 'edit'])->name('organizations.edit')->middleware('auth');

        Route::put('organizations/{organization}', [OrganizationsController::class, 'update'])->name('organizations.update')->middleware('auth');

        Route::delete('organizations/{organization}', [OrganizationsController::class, 'destroy'])->name('organizations.destroy')->middleware('auth');

        Route::put('organizations/{organization}/restore', [OrganizationsController::class, 'restore'])->name('organizations.restore')->middleware('auth');
        //
        Route::get('organizations/createNewOrganization', [OrganizationsController::class, 'createNewOrganization'])
            ->name('organizations.createNewOrganization')
            ->middleware('auth');

        Route::post('super_admin/deleteNewOrganization', [OrganizationsController::class, 'deleteNewOrganization'])
            ->name('organizations.deleteNewOrganization')
            ->middleware('auth');

        Route::post('organizations/selectOrganization', [OrganizationsController::class, 'selectOrganization'])
            ->name('organizations.selectOrganization')
            ->middleware('auth');

        Route::post('organizations/delete', [OrganizationsController::class, 'deleteOrganization'])
            ->name('organizations.deleteOrganization')
            ->middleware('auth');

        Route::post('organizations/comp_source', [OrganizationsController::class, 'compSource'])
            ->name('organizations.comp_source')
            ->middleware('auth');

        Route::get('organizations/switch-organization/{id}', [OrganizationsController::class, 'switchOrganization'])
            ->middleware('auth')
            ->name('organizations.switchOrganization');


        //region Административная панель
        // Админка Organizations

        Route::get('dashboard/organizations', [IndexController::class, 'dashboard_organizations'])->name('dashboard.organizations')->middleware('auth');
        Route::post('dashboard/checkUserExists', [AuthenticatedSessionController::class, 'checkUserExists'])->name('dashboard.checkUserExists')->middleware('auth');
        Route::post('dashboard/verifySmsCode', [AuthenticatedSessionController::class, 'verifySmsCode'])->name('dashboard.verifySmsCode')->middleware('auth');

        // Админка Roles
        Route::get('dashboard/roles', [IndexController::class, 'dashboard_roles'])->name('dashboard.roles')->middleware('auth');

        // Админка Tests
        Route::get('dashboard/tests', [IndexController::class, 'dashboard_tests'])->name('dashboard.tests')->middleware('auth');

        // Админка Category
        Route::get('dashboard/categories', [IndexController::class, 'dashboard_categories'])->name('dashboard.categories')->middleware('auth');

        // Админка Users
        Route::get('dashboard/users', [IndexController::class, 'dashboard_users'])->name('ddashboard.users')->middleware('auth');

        Route::get('dashboard/service_settings', [IndexController::class, 'dashboard_service_settings'])->name('dashboard.service_settings')->middleware('auth');

        // Dashboard Organization
        Route::get('dashboard/organizations/all', [App\Http\Controllers\Dashboard\Api\V1\OrganizationController::class, 'getOrganizations']);
        Route::post('dashboard/organization/save', [App\Http\Controllers\Dashboard\Api\V1\OrganizationController::class, 'saveOrganization']);
        //endregion

        // Contacts

        Route::get('contacts', [ContactsController::class, 'index'])->name('contacts')->middleware('auth');

        Route::get('contacts/create', [ContactsController::class, 'create'])->name('contacts.create')->middleware('auth');

        Route::post('contacts', [ContactsController::class, 'store'])->name('contacts.store')->middleware('auth');

        Route::get('contacts/{contact}/edit', [ContactsController::class, 'edit'])->name('contacts.edit')->middleware('auth');

        Route::put('contacts/{contact}', [ContactsController::class, 'update'])->name('contacts.update')->middleware('auth');

        Route::delete('contacts/{contact}', [ContactsController::class, 'destroy'])->name('contacts.destroy')->middleware('auth');

        Route::put('contacts/{contact}/restore', [ContactsController::class, 'restore'])->name('contacts.restore')->middleware('auth');

        // Reports

        Route::get('reports', [ReportsController::class, 'index'])->name('reports')->middleware('auth');

        // Images

        Route::get('/img/{path}', [ImagesController::class, 'show'])->where('path', '.*')->name('image');


        // Services
        Route::post('form/connect', [FormController::class, 'frontConnect'])->name('form.connect')->middleware('guest');
        Route::post('form/front-modal', [FormController::class, 'frontModal'])->name('form.modal')->middleware('guest');
        Route::post('form/front-footer', [FormController::class, 'frontFooter'])->name('form.footer')->middleware('guest');


        // Pages
        Route::get('pages/{slug}', [PageController::class, 'single'])->name('pages.single');


        // Pages
        Route::get('api/pages/{slug}', [PageController::class, 'singleApi'])->name('pages.singleApi');

        Route::post('/export-products', [SuperAdminController::class, 'exportToExcel']);

    });

    Route::get('test', [ClientsController::class, 'test'])->name('test');


