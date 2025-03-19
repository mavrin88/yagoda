<?php

use App\Http\Controllers\FormController;
use App\Modules\YagodaTips\Controllers\CoordinatorController;
use App\Modules\YagodaTips\Controllers\GroupController;
use App\Modules\YagodaTips\Controllers\MasterController;
use App\Modules\YagodaTips\Controllers\MasterShiftController;
use App\Modules\YagodaTips\Controllers\OrderController;
use App\Modules\YagodaTips\Controllers\QrController;
use App\Modules\YagodaTips\Controllers\StaffController;
use App\Modules\YagodaTips\Controllers\TipDistributionController;
use App\Modules\YagodaTips\Controllers\TipsStatisticController;
use App\Modules\YagodaTips\Controllers\ClientController;
use Illuminate\Support\Facades\Route;
use App\Modules\YagodaTips\Controllers\SettingController;


Route::get('/tips', [ClientController::class, 'tipsLanding'])->name('tips.landing');

Route::prefix('tips')->middleware('auth')->group(function () {

    // ГРУППЫ
    Route::get('/get_group', [GroupController::class, 'getGroup'])->name('tips.group.index');

    Route::get('/group', [GroupController::class, 'index'])->name('tips.group.get_group');
    Route::post('/group', [GroupController::class, 'storeSettings'])->name('tips.group.storeSettings');
    Route::post('/setTips', [GroupController::class, 'setTips'])->name('tips.group.setTips');
    Route::get('/group-json', [GroupController::class, 'getData'])->name('tips.group.getData');
    Route::get('/groups_settings', [GroupController::class, 'settings'])->name('tips.settings');
    Route::get('/map_links', [GroupController::class, 'mapLinks'])->name('tips.mapLinks');
    Route::post('/save-links-settings', [GroupController::class, 'saveLinksSettings']);

    // НАСТРОЙКИ
    Route::post('/settings/save', [SettingController::class, 'save'])->name('tips.settings.save');

    Route::get('/qr', [QrController::class, 'index'])->name('tips.qr.index');
    Route::get('/qr/show/{id}', [QrController::class, 'single'])->name('tips.qr.single');
    Route::get('/qr/static/{id}', [QrController::class, 'static'])->name('tips.qr.static');
    Route::get('/qr/stand/{id}', [QrController::class, 'stand'])->name('tips.qr.stand');
    Route::get('/qr_add/name', [QrController::class, 'addNewForName'])->name('tips.qr.addNewForName');
    Route::get('/qr_add/scan', [QrController::class, 'addNewForScan'])->name('tips.qr.addNewForScan');
    Route::post('/qr/check_uniqueness', [QrController::class, 'checkUniqueness'])->name('tips.qr.checkUniqueness');
    Route::post('/qr/save', [QrController::class, 'saveQrCode'])->name('tips.qr.saveQrCode');
    Route::get('/qr/{id}/pdf', [QrController::class, 'generatePdf'])->name('tips.qr.pdf');
    Route::delete('/qr/{id}', [QrController::class, 'deleteQrCode'])->name('tips.qr.deleteQrCode');
    Route::patch('/qr/{id}', [QrController::class, 'updateName'])->name('tips.qr.update');
    Route::get('/qr/stands', [QrController::class, 'stands'])->name('tips.qr.stands');


    Route::get('/qr/link', [QrController::class, 'link'])->name('tips.qr.link');
    Route::get('/qr/settings', [QrController::class, 'settings'])->name('tips.qr.settings');
    Route::get('/qr/pay', [QrController::class, 'pay'])->name('tips.qr.pay');





    Route::get('/master', [MasterController::class, 'index'])->name('tips.master.index');
    Route::get('/master/qr', [MasterController::class, 'qrList'])->name('tips.master.qr');
    Route::get('/master/qr/{id}', [MasterController::class, 'single'])->name('tips.master.qr.single');
    Route::get('/master/qr-stand/{id}', [MasterController::class, 'singleStand'])->name('tips.master.qr.singleStand');
    Route::get('/master/add-qr', [MasterController::class, 'add'])->name('tips.master.addQr');
    Route::get('/master/stands', [MasterController::class, 'stands'])->name('tips.master.stands');


    // КООРДИНАТОР
    Route::get('/coord', [CoordinatorController::class, 'index'])->name('tips.coord');
    Route::get('/coord/get-tips', [CoordinatorController::class, 'getTips'])->name('tips.coord.getTips');
    Route::get('/coord/stands', [CoordinatorController::class, 'stands'])->name('tips.coord.stands');

    // СОТРУДНИКИ В СМЕНЕ
    Route::get('/staff_shift', [MasterShiftController::class, 'index'])->name('tips.mastersShift');
    Route::post('/save_masters_shift', [MasterShiftController::class, 'saveMastersShift'])->name('tips.saveMastersShift');

    // СОТРУДНИКИ
    Route::get('/get_workforce', [StaffController::class, 'getWorkforce'])->name('tips.get_workforce');

    Route::get('/workforce', [StaffController::class, 'workforce'])->name('tips.workforce');
    Route::post('/storeUser', [StaffController::class, 'storeUser'])->name('tips.storeUser');
    Route::get('/administrators', [StaffController::class, 'administrators'])->name('tips.administrators');
    Route::delete('/administrators/{id}', [StaffController::class, 'deleteAdministrator'])->name('tips.administrators.delete');
    Route::get('/masters', [StaffController::class, 'masters'])->name('tips.masters');
    Route::delete('/masters/{id}', [StaffController::class, 'deleteMaster'])->name('tips.masters.delete');
    Route::get('/staff', [StaffController::class, 'staff'])->name('tips.staff');
    Route::delete('/staff/{id}', [StaffController::class, 'deleteStaff'])->name('tips.staff.delete');

    // ЧАЕВЫЕ РАСПРЕДЕЛЕНИЕ
    Route::get('/tip_distribution', [TipDistributionController::class, 'index']);
    Route::post('/tip_distribution', [TipDistributionController::class, 'save']);
    Route::post('/tip_distribution/show_distributions_message', [TipDistributionController::class, 'tips.showMessageTipDistribution']);

    // СТАТИСТИКА ЧАЕВЫХ
    Route::post('/tip_statistic', [TipsStatisticController::class, 'index']);
    Route::get('/coord/tips', [TipsStatisticController::class, 'tips'])->name('tips.coord.tips');
    Route::post('/coord/tips/show/', [TipsStatisticController::class, 'tipsOne'])->name('tips.coord.tipsOne');

    // Формы
    Route::post('form/order-stand', [FormController::class, 'orderStand'])->name('form.orderStand');

    // КЛИЕНТ
    Route::get('/pay/{link}', [ClientController::class, 'handleLink']);
    Route::post('/create-order', [OrderController::class, 'createOrder']);
    Route::post('/make_payment', [ClientController::class, 'makePayment']);
});
