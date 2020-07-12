<?php

namespace Tests\Feature\TwoFactor;

use App\BackupCode;
use App\Events\TwoFactorDisabled;
use App\Events\TwoFactorEnabled;
use App\Events\TwoFactorRecoveryCodesDepleted;
use App\Events\TwoFactorRecoveryCodesGenerated;
use App\SafeDevice;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_has_two_factor_enabled()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        $this->assertTrue($user->isTwoFactorAuthEnabled());

        $user->disableTwoFactorAuth();

        $this->assertFalse($user->isTwoFactorAuthEnabled());
    }

    public function test_disables_two_factor_authentication()
    {
        $events = Event::fake();

        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        $user->disableTwoFactorAuth();
        $this->assertFalse($user->isTwoFactorAuthEnabled());

        $events->assertDispatched(TwoFactorDisabled::class, function ($event) use ($user) {
            return $user->is($event->user);
        });
    }

    public function test_enables_two_factor_authentication()
    {
        $events = Event::fake();

        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();

        $user->enableTwoFactorAuth();
        $this->assertTrue($user->isTwoFactorAuthEnabled());

        $events->assertDispatched(TwoFactorEnabled::class, function ($event) use ($user) {
            return $user->is($event->user);
        });
    }

    public function test_creates_two_factor_authentication()
    {
        $events = Event::fake();

        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();

        $this->assertFalse($user->isTwoFactorAuthEnabled());

        $this->assertDatabaseHas('users', [
            ['tfa_enabled_at', null],
        ]);

        $events->assertNotDispatched(TwoFactorEnabled::class);
    }

    public function test_creates_two_factor_flushes_old_auth()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        factory(BackupCode::class, 3)->create([
            'user_id' => $user->id
        ]);

        factory(SafeDevice::class, 3)->create([
            'user_id' => $user->id
        ]);

        $this->assertNotEmpty($user->backupCodes);
        $this->assertNotEmpty($user->safeDevices);
        $this->assertNotNull($user->tfa_enabled_at);

        $user->createTwoFactorAuth();


        $this->assertEmpty($user->fresh()->backupCodes);
        $this->assertEmpty($user->fresh()->safeDevices);
        $this->assertNull($user->fresh()->tfa_enabled_at);
    }

    public function test_rewrites_when_creating_two_factor_authentication()
    {
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        $this->assertTrue($user->isTwoFactorAuthEnabled());

        $old = $user->tfa_shared_secret;

        $user->createTwoFactorAuth();

        $this->assertFalse($user->isTwoFactorAuthEnabled());
        $this->assertNotEquals($old, $user->tfa_shared_secret);
    }

    public function test_new_user_confirms_two_factor_successfully()
    {
        $event = Event::fake();

        /** @var User $user */
        $user = factory(User::class)->create();

        Carbon::setTestNow($now = Carbon::create(2020, 01, 01, 18, 30));

        $user->createTwoFactorAuth();

        $code = $user->makeTwoFactorCode();

        $this->assertTrue($user->confirmTwoFactorAuth($code));
        $this->assertTrue($user->isTwoFactorAuthEnabled());
        $this->assertFalse($user->validateTwoFactorCode($code));

        Cache::getStore()->flush();
        $this->assertTrue($user->validateTwoFactorCode($code));

        $this->assertEquals($now, $user->tfa_enabled_at);

        $event->assertDispatched(TwoFactorRecoveryCodesGenerated::class, function ($event) use ($user) {
            return $user->is($event->user);
        });
    }

    public function test_confirms_twice_but_doesnt_change_the_secret()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        $event = Event::fake();

        $old_now = $user->tfa_enabled_at;

        Carbon::setTestNow(Carbon::create(2020, 01, 01, 18, 30));

        $secret = $user->tfa_shared_secret;

        $code = $user->makeTwoFactorCode();

        $this->assertTrue($user->confirmTwoFactorAuth($code));

        $user->refresh();

        $this->assertTrue($user->isTwoFactorAuthEnabled());
        $this->assertTrue($user->validateTwoFactorCode($code));
        $this->assertEquals($old_now, $user->tfa_enabled_at);
        $this->assertEquals($secret, $user->tfa_shared_secret);

        $event->assertNotDispatched(TwoFactorRecoveryCodesGenerated::class);
    }

    public function test_doesnt_confirm_two_factor_auth_with_old_recovery_code()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        $recovery_code = $user->backupCodes->random();

        $code = $recovery_code['code'];

        $user->createTwoFactorAuth();

        $this->assertFalse($user->confirmTwoFactorAuth($code));
    }

    public function test_old_user_confirms_new_two_factor_successfully()
    {
        $event = Event::fake();

        /** @var User $user */
        $user = factory(User::class)->create();

        Carbon::setTestNow($now = Carbon::create(2020, 01, 01, 18, 30));

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        $old_code = $user->makeTwoFactorCode();

        $this->assertTrue($user->validateTwoFactorCode($old_code));

        $user->createTwoFactorAuth();

        $new_code = $user->makeTwoFactorCode();

        $this->assertFalse($user->confirmTwoFactorAuth($old_code));
        $this->assertFalse($user->isTwoFactorAuthEnabled());

        Cache::getStore()->flush();
        $this->assertTrue($user->confirmTwoFactorAuth($new_code));
        $this->assertTrue($user->isTwoFactorAuthEnabled());

        Cache::getStore()->flush();
        $this->assertFalse($user->validateTwoFactorCode($old_code));
        $this->assertTrue($user->validateTwoFactorCode($new_code));

        $this->assertEquals($now, $user->tfa_enabled_at);

        $event->assertDispatched(TwoFactorRecoveryCodesGenerated::class, function ($event) use ($user) {
            return $user->is($event->user);
        });
    }

    public function test_validates_two_factor_code()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        Carbon::setTestNow($now = Carbon::create(2020, 01, 01, 18, 30));

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        $code = $user->makeTwoFactorCode();

        $this->assertTrue($user->validateTwoFactorCode($code));
    }

    public function test_validates_two_factor_code_with_recovery_code()
    {
        Carbon::setTestNow($now = Carbon::create(2020, 01, 01, 18, 30));

        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        factory(BackupCode::class, 3)->create([
            'user_id' => $user->id
        ]);

        $recovery_code = $user->backupCodes->random()['code'];

        $code = $user->makeTwoFactorCode();

        $this->assertTrue($user->validateTwoFactorCode($code));

        $this->assertTrue($user->validateTwoFactorCode($recovery_code));
        $this->assertFalse($user->validateTwoFactorCode($recovery_code));
    }

    public function test_doesnt_validates_if_two_factor_auth_is_disabled()
    {
        Carbon::setTestNow($now = Carbon::create(2020, 01, 01, 18, 30));

        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        factory(BackupCode::class, 3)->create([
            'user_id' => $user->id
        ]);

        $recovery_code = $user->backupCodes->random()['code'];

        $code = $user->makeTwoFactorCode();

        $this->assertTrue($user->validateTwoFactorCode($code));

        $user->disableTwoFactorAuth();

        $this->assertFalse($user->validateTwoFactorCode($code));
        $this->assertFalse($user->validateTwoFactorCode($recovery_code));
    }

    public function test_fires_recovery_codes_depleted()
    {
        $event = Event::fake();

        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        factory(BackupCode::class, 3)->create([
            'user_id' => $user->id
        ]);

        foreach ($user->backupCodes as $item) {
            $this->assertTrue($user->validateTwoFactorCode($item['code']));
        }

        foreach ($user->backupCodes as $item) {
            $this->assertFalse($user->validateTwoFactorCode($item['code']));
        }

        $event->assertDispatchedTimes(TwoFactorRecoveryCodesDepleted::class, 1);
        $event->assertDispatched(TwoFactorRecoveryCodesDepleted::class, function ($event) use ($user) {
            return $user->is($event->user);
        });
    }

    public function test_safe_device()
    {
        Carbon::setTestNow($now = Carbon::create(2020, 01, 01, 18, 30));

        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        $request = Request::create('/', 'GET', [], [], [], [
            'REMOTE_ADDR' => $ip = $this->faker->ipv4,
        ]);

        $this->assertEmpty($user->safeDevices);

        $user->addSafeDevice($request);

        $user->refresh();

        $this->assertCount(1, $user->safeDevices);
        $this->assertEquals($ip, $user->safeDevices->first()['ip']);
        $this->assertEquals('2020-01-01 18:30:00', $user->safeDevices->first()['added_at']);
    }

    public function test_flushes_safe_devices()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        for ($i = 0; $i < 3; ++$i) {
            Carbon::setTestNow(Carbon::create(2020, 01, 01, 18, 30, $i));

            $user->addSafeDevice(
                Request::create('/', 'GET', [], [], [], [
                    'REMOTE_ADDR' => $this->faker->ipv4,
                ])
            );
        }

        $this->assertCount(3, $user->safeDevices);

        $user->flushSafeDevices();

        $this->assertEmpty($user->fresh()->safeDevices);
    }

    public function test_is_safe_device_and_safe_with_other_ip()
    {
        $max_devices = 3;

        /** @var User $user */
        $user = factory(User::class)->create();

        for ($i = 0; $i < $max_devices; ++$i) {
            Carbon::setTestNow(Carbon::create(2020, 01, 01, 18, 30, $i));

            $user->addSafeDevice(
                Request::create('/', 'GET', [], [], [], [
                    'REMOTE_ADDR' => $this->faker->ipv4,
                ])
            );
        }

        $request = Request::create('/', 'GET', [], [
            '2fa_remember' => $user->safeDevices->random()['token'],
        ], [], [
            'REMOTE_ADDR' => $this->faker->ipv4,
        ]);

        $this->assertTrue($user->isSafeDevice($request));
        $this->assertFalse($user->isNotSafeDevice($request));
    }

    public function test_not_safe_device_if_remember_code_doesnt_match()
    {
        $max_devices = 3;

        /** @var User $user */
        $user = factory(User::class)->create();

        for ($i = 0; $i < $max_devices; ++$i) {
            Carbon::setTestNow($now = Carbon::create(2020, 01, 01, 18, 30, $i));

            $user->addSafeDevice(
                Request::create('/', 'GET', [], [], [], [
                    'REMOTE_ADDR' => $ip = $this->faker->ipv4,
                ])
            );
        }

        $request = Request::create('/', 'GET', [], [
            '2fa_remember' => 'anything',
        ], [], [
            'REMOTE_ADDR' => $ip,
        ]);

        $this->assertFalse($user->isSafeDevice($request));
        $this->assertTrue($user->isNotSafeDevice($request));
    }

    public function test_not_safe_device_if_expired()
    {
        $max_devices = 3;

        Carbon::setTestNow($now = Carbon::create(2020, 01, 01, 18, 30));

        /** @var User $user */
        $user = factory(User::class)->create();

        for ($i = 0; $i < $max_devices; ++$i) {
            $user->addSafeDevice(
                Request::create('/', 'GET', [], [], [], [
                    'REMOTE_ADDR' => $this->faker->ipv4,
                ])
            );
        }

        $request = Request::create('/', 'GET', [], [
            '2fa_remember' => $user->safeDevices->random()['token'],
        ], [], [
            'REMOTE_ADDR' => $this->faker->ipv4,
        ]);

        $this->assertTrue($user->isSafeDevice($request));
        $this->assertFalse($user->isNotSafeDevice($request));

        Carbon::setTestNow($now->clone()->addDays(30)->subSecond());

        $this->assertTrue($user->isSafeDevice($request));
        $this->assertFalse($user->isNotSafeDevice($request));
    }

    public function test_unique_code_works_only_one_time()
    {
        Carbon::setTestNow(Carbon::create(2020, 01, 01, 18, 30, 0));

        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        $code = $user->makeTwoFactorCode();

        $this->assertTrue($user->validateTwoFactorCode($code));
        $this->assertFalse($user->validateTwoFactorCode($code));

        Carbon::setTestNow(Carbon::create(2020, 01, 01, 18, 30, 59));

        $new_code = $user->makeTwoFactorCode();

        $this->assertTrue($user->validateTwoFactorCode($new_code));
        $this->assertFalse($user->validateTwoFactorCode($code));
    }

    public function test_unique_code_works_only_one_time_with_extended_window()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        Carbon::setTestNow(Carbon::create(2020, 01, 01, 18, 30, 0));

        $old = $user->makeTwoFactorCode();

        $this->assertTrue($user->validateTwoFactorCode($old));
        $this->assertFalse($user->validateTwoFactorCode($old));

        Carbon::setTestNow(Carbon::create(2020, 01, 01, 18, 32, 29));

        $new = $user->makeTwoFactorCode();

        $this->assertTrue($user->validateTwoFactorCode($new));
        $this->assertFalse($user->validateTwoFactorCode($new));
    }

    public function test_unique_code_works_only_one_time_in_extended_time()
    {
        Carbon::setTestNow($now = Carbon::create(2020, 01, 01, 18, 30, 20));

        /** @var User $user */
        $user = factory(User::class)->create();

        $user->createTwoFactorAuth();
        $user->enableTwoFactorAuth();

        $code = $user->makeTwoFactorCode();

        Carbon::setTestNow($now = Carbon::create(2020, 01, 01, 18, 30, 59));

        $this->assertTrue($user->validateTwoFactorCode($code));
        $this->assertFalse($user->validateTwoFactorCode($code));
    }
}
