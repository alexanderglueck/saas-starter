<?php

namespace Tests\Feature\SessionHandling;

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

class SessionHandlingTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_logging_in_creates_a_session()
    {
        $password = "secret";

        /** @var User $user */
        $user = factory(User::class)->create([
            'password' => \Hash::make($password)
        ]);

        $this->assertDatabaseMissing('sessions', [
            'user_id' => $user->id
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => $password,
        ])->assertSessionHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertDatabaseHas('sessions', [
            'user_id' => $user->id
        ]);
    }

    public function test_sessions_are_listed()
    {
        $user = factory(User::class)->create();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasNoErrors()
            ->assertRedirect(route('home'));

        $this->actingAs($user)->get(route('session.index'))
            ->assertOk()
            ->assertSee('Symfony');
    }

}
