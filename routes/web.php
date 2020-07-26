<?php

use App\Http\Controllers\Account\ApiTokenController;
use App\Http\Controllers\Account\DeactivateController;
use App\Http\Controllers\Account\DeleteController;
use App\Http\Controllers\Account\PasswordController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\ProfileImageController;
use App\Http\Controllers\Account\Subscription\SubscriptionCancelController;
use App\Http\Controllers\Account\Subscription\SubscriptionCardController;
use App\Http\Controllers\Account\Subscription\SubscriptionInvoiceController;
use App\Http\Controllers\Account\Subscription\SubscriptionResumeController;
use App\Http\Controllers\Account\Subscription\SubscriptionSwapController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\LogEntryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\Subscription\PlanController;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\Teamwork\AuthController;
use App\Http\Controllers\Teamwork\TeamController;
use App\Http\Controllers\Teamwork\TeamMemberController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [PageController::class, 'welcome']);

Auth::routes(['verify' => true]);

Route::group(['as' => 'subscription.', 'middleware' => ['auth.register', 'subscription.inactive']], function () {
    Route::get('subscription', [SubscriptionController::class, 'index'])->name('index');
    Route::post('subscription', [SubscriptionController::class, 'store'])->name('store');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/setup', [SetupController::class, 'show'])->name('setup');
    Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');

    Route::get('session', [SessionController::class, 'index'])->name('session.index');
    Route::delete('session', [SessionController::class, 'destroy'])->name('session.destroy');

    Route::delete('device', [DeviceController::class, 'destroy'])->name('device.destroy');

    /**
     * Manage roles
     */
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::get('roles/{role}/delete', [RoleController::class, 'delete'])->name('roles.delete');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    /**
     * Impersonate user
     */
    Route::post('impersonate', [ImpersonateController::class, 'store'])->name('user.impersonate');
    Route::delete('impersonate', [ImpersonateController::class, 'destroy']);

    /**
     * Log
     */
    Route::get('logs', [LogEntryController::class, 'index'])->name('logs.index');

    /**
     * Teamwork
     */
    Route::group(['prefix' => 'teams', 'namespace' => 'Teamwork'], function () {
        Route::get('/', [TeamController::class, 'index'])->name('teams.index');
        Route::get('edit/{id}', [TeamController::class, 'edit'])->name('teams.edit');
        Route::put('edit/{id}', [TeamController::class, 'update'])->name('teams.update');

        Route::group(['middleware' => ['subscription.team']], function () {
            Route::get('members/{id}', [TeamMemberController::class, 'show'])->name('teams.members.show');
            Route::get('members/resend/{invite_id}', [TeamMemberController::class, 'resendInvite'])->name('teams.members.resend_invite');
            Route::post('members/{id}', [TeamMemberController::class, 'invite'])->name('teams.members.invite');
            Route::delete('members/{id}/{user_id}', [TeamMemberController::class, 'destroy'])->name('teams.members.destroy');
        });

        Route::get('accept/{token}', [AuthController::class, 'acceptInvite'])->name('teams.accept_invite');
    });


    /**
     * User Settings
     */
    Route::group(['namespace' => 'Account', 'as' => 'user_settings.', 'prefix' => 'settings'], function () {
        /**
         * Profile
         */
        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

        /**
         * Change password
         */
        Route::get('password', [PasswordController::class, 'show'])->name('password.show');
        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        /**
         * Profile image
         */
        Route::get('profile/image', [ProfileImageController::class, 'show'])->name('image.show');
        Route::put('profile/image', [ProfileImageController::class, 'update'])->name('image.update');
        Route::delete('profile/image', [ProfileImageController::class, 'destroy'])->name('image.destroy');

        /**
         * Two-factor authentication
         */
        Route::get('two-factor/create', [TwoFactorController::class, 'create'])->name('two-factor.create');
        Route::post('two-factor', [TwoFactorController::class, 'store'])->name('two-factor.store');
        Route::get('two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
        Route::delete('two-factor', [TwoFactorController::class, 'destroy'])->name('two-factor.destroy');

        /**
         * API token
         */
        Route::get('api-token', [ApiTokenController::class, 'show'])->name('api_token.show');
        Route::put('api-token', [ApiTokenController::class, 'update'])->name('api_token.update');

        /**
         * Deactivate account
         */
        Route::get('deactivate', [DeactivateController::class, 'index'])->name('deactivate.index');
        Route::post('deactivate', [DeactivateController::class, 'store'])->name('deactivate.store');

        /**
         * Delete Account
         */
        Route::get('delete-account', [DeleteController::class, 'show'])->name('delete.show');
        Route::delete('delete-account', [DeleteController::class, 'destroy'])->name('delete.destroy');

        /**
         * Subscriptions
         */
        Route::group([
            'prefix' => 'subscription',
            'namespace' => 'Subscription',
            'middleware' => ['subscription.owner']
        ], function () {
            /**
             * Cancel
             */
            Route::group(['middleware' => 'subscription.notcancelled'], function () {
                Route::get('/cancel', [SubscriptionCancelController::class, 'index'])->name('subscription.cancel.index');
                Route::post('/cancel', [SubscriptionCancelController::class, 'store'])->name('subscription.cancel.store');
            });

            /**
             * Resume
             */
            Route::group(['middleware' => 'subscription.cancelled'], function () {
                Route::get('/resume', [SubscriptionResumeController::class, 'index'])->name('subscription.resume.index');
                Route::post('/resume', [SubscriptionResumeController::class, 'store'])->name('subscription.resume.store');
            });

            /**
             * Swap
             */
            Route::group(['middleware' => 'subscription.notcancelled'], function () {
                Route::get('/swap', [SubscriptionSwapController::class, 'index'])->name('subscription.swap.index');
                Route::post('/swap', [SubscriptionSwapController::class, 'store'])->name('subscription.swap.store');
            });

            /**
             * Card
             */
            Route::group(['middleware' => 'subscription.customer'], function () {
                Route::get('/card', [SubscriptionCardController::class, 'index'])->name('subscription.card.index');
                Route::post('/card', [SubscriptionCardController::class, 'store'])->name('subscription.card.store');
            });

            /**
             * Invoices
             */
            Route::group(['middleware' => 'subscription.customer'], function () {
                Route::get('/invoices', [SubscriptionInvoiceController::class, 'index'])->name('subscription.invoices.index');
                Route::get('/invoices/{invoice}', [SubscriptionInvoiceController::class, 'show'])->name('subscription.invoices.show');
            });
        });
    });
});

/**
 * Plans
 */
Route::group(['as' => 'plans.'], function () {
    Route::get('plans/', [PlanController::class, 'index'])->name('index');
});
