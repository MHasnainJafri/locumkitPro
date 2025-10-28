<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\JobController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\configController;
use App\Http\Controllers\admin\FinanceController;
use App\Http\Controllers\admin\PackageController;
use App\Http\Controllers\admin\paymentController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\FeedbackController;
use App\Http\Controllers\admin\questionController;
use App\Http\Controllers\admin\auth\logincontroller;
use App\Http\Controllers\admin\BlogController;
use App\Http\Controllers\admin\pkgresourceController;
use App\Http\Controllers\admin\UserFeedbackController;
use App\Http\Controllers\admin\FeedbackQuestionController;
use App\Http\Controllers\admin\IndustryNewsController;
use App\Http\Controllers\admin\PageController;
use App\Http\Controllers\NotificationController;
use App\Models\Finance;
use App\Models\IndustryNews;
use App\Models\Notification;
use App\Models\UserFeedback;
use Beta\Microsoft\Graph\IndustryData\Model\IndustryDataConnector;

Route::get('admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login'); // Adjust the controller method as needed



// Add more routes as needed


// routes/admin.php

Route::post('tax/update' , [FinanceController::class , 'taxupdate'])->name('tax.update');

Route::group(['middleware' => ['auth', 'check.role'], "prefix" => "admin"], function () {
    Route::get('finance/record', [FinanceController::class, 'record'])->name('finance.record');
    Route::get('finance/profitandloss/{id}/{year}', [FinanceController::class, 'profitAndLoss'])->name('finance.profit.loss');
    Route::post('finance/save_profit_loss', [FinanceController::class, 'profitAndLossSave'])->name('save.profitloss.data');
    Route::post('finance/save_balance_sheet', [FinanceController::class, 'balanceSheetSave'])->name('save.balancesheet.data');
    Route::get('finance/balancesheet/{id}/{year}', [FinanceController::class, 'balanceSheet'])->name('finance.balancesheet');
    Route::get('finance/transactions/{id}/{year}', [FinanceController::class, 'transactions'])->name('finance.alltransactions');
    Route::get('finance/supplierlist', [FinanceController::class, 'supplierList'])->name('finance.supplierlist');
    Route::get('taxsetting', [FinanceController::class, 'taxList'])->name('tax.list');
    Route::get('tax/create', [FinanceController::class, 'taxCreate'])->name('tax.create');
    Route::post('tax/store', [FinanceController::class, 'taxStore'])->name('tax.store');
    Route::get('tax/edit/{id}', [FinanceController::class, 'taxEdit'])->name('tax.edit');
    Route::get('tax/delete/{id}', [FinanceController::class, 'taxdelete'])->name('tax.delete');
    // Route::post('tax/update', [FinanceController::class, 'taxupdate'])->name('tax.update');
    // Route::post('tax/update', function() {
    //     return true;
    // })->name('tax.update');
    
    Route::post('nitax/create', [FinanceController::class, 'nitaxStore'])->name('nitaxCreate');
    Route::post('nitax/edit', [FinanceController::class, 'nitaxUpdate'])->name('nitax-edit');
    
    Route::get('nitaxsetting', [FinanceController::class, 'niTaxList'])->name('nitax.list');
    Route::get('nitax/create', [FinanceController::class, 'niTaxCreate'])->name('nitax.create');
    Route::get('nitax/edit/{id}', [FinanceController::class, 'niTaxEdit'])->name('nitax.edit');
    Route::get('nitax/delete/{id}', [FinanceController::class, 'nitaxdelete'])->name('nitax.delete');
    Route::get('report/singleEmployerJobReport/{id}', [UserController::class, 'singleEmployerJobReport'])->name('report.singleEmployerJobReport');
    Route::get('report/singleLocumJobReport/{id}', [UserController::class, 'singleLocumJobReport'])->name('report.singleLocumJobReport');
    Route::get('report/blockuser', [UserController::class, 'getBlockUsers'])->name('report.blockuserreport');
    Route::post('report/blockuser/export', [UserController::class, 'getBlockUsersExport'])->name('report.blockuserreport.export');
    Route::get('report/newUsers', [UserController::class, 'getNewUsers'])->name('report.new-user');
    Route::post('report/newUsers/export', [UserController::class, 'getNewUsersExport'])->name('report.newuserreport.export');
    Route::get('report/leaverUsers', [UserController::class, 'getLeaverUsers'])->name('report.leaverUser');
    Route::post('report/leaverUsers/export', [UserController::class, 'getLeaverUsersExport'])->name('report.leaverUser.export');
    Route::get('report/lastlogin', [UserController::class, 'getLastLogin'])->name('report.lastlogin');
    Route::post('report/lastlogin/export', [UserController::class, 'getLastLoginUsersExport'])->name('report.lastlogin.export');
    Route::get('report/EmployerJobReport', [UserController::class, 'getEmployerJobReport'])->name('report.EmployerJobReport');
    Route::post('report/EmployerJobReportExport', [UserController::class, 'getEmployerJobReportExport'])->name('report.EmployerJobReport.export');
    Route::get('report/locumjobReport', [UserController::class, 'getLocumReport'])->name('report.locumjobReport');
    Route::post('report/locumjobReport/Export', [UserController::class, 'getLocumReportExport'])->name('report.locumjobReport.export');
    Route::get('report/privatelocumReport', [UserController::class, 'privatelocumReport'])->name('report.privatelocumReport');
    Route::post('report/privatelocumReport/export', [UserController::class, 'privatelocumReportExport'])->name('report.privatelocumReport.export');
    Route::get('report/locumPrivatejobReport', [UserController::class, 'getLocumPraivateJobs'])->name('report.locumPrivatejobReport');
    Route::post('report/locumPrivatejobReport/Export', [UserController::class, 'getLocumPraivateJobsExport'])->name('report.locumPrivatejobReport.export');

    Route::get('Blog/Post', [BlogController::class, 'BlogPost'])->name('blog.post');
    Route::get('Blog/Index', [BlogController::class, 'index'])->name('blog.index');
    Route::get('Blog/Create', [BlogController::class, 'Create'])->name('Blog.Create');
    Route::post('Blog/Store', [BlogController::class, 'Store'])->name('admin.blog.store');
    Route::get('Blog/edit/{id}', [BlogController::class, 'edit'])->name('blog.edit.{id}');
    Route::post('Blog/update/{id}', [BlogController::class, 'update'])->name('admin.blog.update');

    Route::get('Page/index', [PageController::class, 'index'])->name('admin.page.index');
    Route::get('Page/create', [PageController::class, 'create'])->name('admin.page.create');
    Route::post('Page/store', [PageController::class, 'store'])->name('admin.page.store');
    Route::post('Page/update', [PageController::class , 'updatePage'])->name('admin.page.update');
    Route::post('Page/delete', [PageController::class , 'deletePage'])->name('admin.page.delete_page');


    Route::get('Industry/Index', [IndustryNewsController::class, 'index'])->name('industry.index');
    Route::get('Industry/Create', [IndustryNewsController::class, 'create'])->name('IndustryNews.Create');
    Route::post('Industry/Store', [IndustryNewsController::class, 'store'])->name('Industry.store');
    Route::get('Industry/edit/{id}', [IndustryNewsController::class, 'edit'])->name('Industry.edit.{id}');
    Route::post('Industry/update/{id}', [IndustryNewsController::class, 'update'])->name('admin.industry.update');



    Route::resource('dashboard', AdminController::class)->names('admin.dashboard');
    Route::resource('users', UserController::class)->names('admin.users');
    Route::resource('jobs', JobController::class)->names('admin.jobs');
    Route::resource('category', CategoryController::class)->names('admin.category');
    Route::resource('package', PackageController::class)->names('admin.package'); // Corrected 'pacakge' to 'package'
    Route::resource('finance', FinanceController::class)->names('admin.finance');
    Route::resource('feedback', FeedbackController::class)->names('admin.feedback');
    Route::resource('feedbackQuestion', FeedbackQuestionController::class)->names('admin.feedbackquestion');
    Route::resource('userfeedback', UserFeedbackController::class)->names('admin.userfeedback');
    Route::resource('config', ConfigController::class)->names('admin.config');
    Route::resource('roles', RoleController::class)->names('admin.roles');
    Route::resource('pacakgeResource', pkgresourceController::class)->names('admin.pkgresource');
    Route::resource('question', questionController::class)->names('admin.question');

    Route::get('/email/newsletter', [ConfigController::class, 'EmailNewsletter'])->name('email.newsletter');
    Route::post('/email/newsletter/Email', [ConfigController::class, 'EmailNewsletterEmail'])->name('email.newsletter.email');
    Route::post('/email/newsletter/EmailManager', [ConfigController::class, 'EmailNewsletterEmailManager'])->name('email.newsletter.emailManager');
    Route::post('/email/newsletter/EmailNotificationSetting', [configController::class, 'NotiSetting'])->name('admin.config.NotificationSetting');
    Route::get('/email/newsletter/NotificationSettings', [ConfigController::class, 'NotificationSettings'])->name('email.newsletter.notificationsetting');
    Route::post('/category/create', [CategoryController::class, 'categoryCreate'])->name('category.create');
    Route::post('/category/update/{id}', [CategoryController::class, 'categoryUpdate'])->name('categories.update');
    // Route::post('/admin/pkgresource/destroy/{id}', [pkgresourceController::class, 'packageDestroy'])->name('admin.pkgresource.destroy');

    Route::get('/viewQuestionindex', [questionController::class, 'index'])->name('viewQuestionindex');
    Route::get('/viewQuestioncreate', [questionController::class, 'create'])->name('viewQuestioncreate');
    Route::get('/payment-history', [paymentController::class, 'index'])->name('payment.History');
    Route::DELETE('/payment.Delete/{id}', [paymentController::class, 'paymentDelete'])->name('payment.Delete');

    Route::get('/feedback.Edit/{id}', [FeedbackController::class, 'FeedbackEdit'])->name('feedback.Edit');
    Route::post('/Feedback.update/{id}', [FeedbackController::class, 'FeedbackUpdate'])->name('Feedback.update');

    Route::DELETE('/feedback/del/{id}', [FeedbackController::class, 'feedbackDel'])->name('feedback.del');

    Route::get('/listSupplier', [FinanceController::class, 'listSupplier'])->name('listSupplier');

    Route::get('disputeFeedback',[UserFeedbackController::class, 'disputeFeedback'])->name('disputefeedback.list');
    Route::get('disputeFeedback/edit/{id}',[UserFeedbackController::class, 'disputeFeedbackEdit'])->name('disputefeedback.edit');

    // Route::get('/users', 'AdminController@users')->name('admin.users');
    Route::get('category/toggleStatus/{id}', [CategoryController::class, 'toggleStatus'])->name('admin.category.toggleStatus');
});
