<?php

namespace Tests\Feature\Http\Controllers;

use App\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RoleController
 */
class RoleControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_displays_view()
    {
        $roles = factory(Role::class, 3)->create();

        $response = $this->get(route('role.index'));

        $response->assertOk();
        $response->assertViewIs('role.index');
        $response->assertViewHas('roles');
    }


    /**
     * @test
     */
    public function create_displays_view()
    {
        $response = $this->get(route('role.create'));

        $response->assertOk();
        $response->assertViewIs('role.create');
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RoleController::class,
            'store',
            \App\Http\Requests\RoleStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves_and_redirects()
    {
        $role = $this->faker->word;

        $response = $this->post(route('role.store'), [
            'role' => $role,
        ]);

        $roles = Role::query()
            ->where('role', $role)
            ->get();
        $this->assertCount(1, $roles);
        $role = $roles->first();

        $response->assertRedirect(route('role.index'));
        $response->assertSessionHas('role.id', $role->id);
    }


    /**
     * @test
     */
    public function show_displays_view()
    {
        $role = factory(Role::class)->create();

        $response = $this->get(route('role.show', $role));

        $response->assertOk();
        $response->assertViewIs('role.show');
        $response->assertViewHas('role');
    }


    /**
     * @test
     */
    public function edit_displays_view()
    {
        $role = factory(Role::class)->create();

        $response = $this->get(route('role.edit', $role));

        $response->assertOk();
        $response->assertViewIs('role.edit');
        $response->assertViewHas('role');
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RoleController::class,
            'update',
            \App\Http\Requests\RoleUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_redirects()
    {
        $role = factory(Role::class)->create();
        $role = $this->faker->word;

        $response = $this->put(route('role.update', $role), [
            'role' => $role,
        ]);

        $response->assertRedirect(route('role.index'));
        $response->assertSessionHas('role.id', $role->id);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_redirects()
    {
        $role = factory(Role::class)->create();
        $role = factory(Role::class)->create();

        $response = $this->get(route('role.destroy', $role));

        $response->assertRedirect(route('role.index'));

        $this->assertDeleted($role);
    }
}
