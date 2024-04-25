<?php

namespace Tests\Feature;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class C_UserTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**********************
     * Benutzer einloggen *
     **********************/

    /**
     * Die Route `/api/login` mit der entsprechenden Methode soll über die API erreichbar sein.
     *
     * @group user-login
     */
    public function test_j1_endpoint_login_returns_status_code_422(): void
    {
        $response = $this->postJson('/api/login');

        $response->assertStatus(422);
    }

    /**
     * Der Seeder soll die Datenbank mit 60 Test-Benutzer füllen.
     *
     * @group user-login
     */
    public function test_j2_if_seeder_creates_60_users(): void
    {
        $users = User::all();

        $this->assertCount(60, $users);
    }

    /**
     * Die Route `/api/login` soll die gesendeten Daten validieren und bei fehlgeschlagener Validierung die entsprechenden Fehlermeldungen zurückgeben.
     *
     * @group user-login
     */
    public function test_j3_endpoint_login_returns_status_code_422_if_invalid_request_data(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => '',
            'password' => ''
        ]);

        $response->assertStatus(422);
    }

    /**
     * @group user-login
     */
    public function test_j3_endpoint_login_returns_validation_errors(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => '',
            'password' => ''
        ]);

        $response->assertJsonValidationErrors([
            'email',
            'password'
        ]);
    }


    /**
     * Die Route `/api/login` soll den Benutzer anhand der E-Mail-Adresse und des Passworts authentifizieren.
     *
     * @group user-login
     */
    public function test_j4_endpoint_login_authenticates_user_and_return_200(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);


        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200);

        $user->delete();
    }

    /**
     *
     * Die Route `/api/login` soll bei fehlgeschlagener Authentifizierung (Kein gültiger User) die entsprechenden Fehlermeldungen zurückgeben.
     *
     * @group user-login
     */

    public function test_j5_endpoint_login_returns_status_code_422_and_error_message_if_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'foo@bar',
            'password' => 'password'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            "errors" => [
                "general" => [ "Die E-Mail-Adresse oder das Passwort ist falsch." ]
            ]
        ]);
    }

    /**
     *
     * Die Route `/api/login` soll bei erfolgreicher Authentifizierung ein Token zurückgeben.
     *
     * @group user-login
     */
    public function test_j6_endpoint_login_authenticates_user_and_returns_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);


        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token'
                 ]);

        $user->delete();
    }

    /********************************
     * Authentifizierung überprüfen *
     ********************************/

    /**
     * Die Route `/api/auth` mit der entsprechenden Methode soll über die API erreichbar sein.
     *
     * @group user-check
     */
    public function test_k1_endpoint_get_auth_returns_status_code_401_without_valid_user(): void
    {
        $response = $this->getJson('/api/auth');

        $response->assertStatus(401);
    }

    /**
     * @group user-check
     */
    public function test_k1_endpoint_get_auth_returns_status_code_200_with_valid_user(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/auth');

        $response->assertStatus(200);

        $user->delete();
    }

    /**
     * Die Route `/api/auth` soll bei fehlgeschlagener Authentifizierung die entsprechenden Fehlermeldungen zurückgeben.
     *
     * @group user-check
     */
    public function test_k2_endpoint_get_auth_returns_error_message_if_invalid_credentials(): void
    {
        $response = $this->getJson('/api/auth');

        $response->assertStatus(401)->assertJson([
            "message" => "Unauthenticated."
        ]);
    }

    /**
     * Die Route `/api/auth` soll bei erfolgreicher Authentifizierung die Daten des Benutzers zurückgeben.
     *
     * @group user-check
     */
    public function test_k3_endpoint_get_auth_returns_user_data(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/auth');

        $response->assertExactJson([
            'data' => UserResource::make($user)->resolve()
        ]);

        $user->delete();
    }

    /**
     * Die Route `/api/auth` soll nur die definierten Felder aus der Response-Definition zurückgeben.
     *
     * @group user-check
     */
    public function test_k4_endpoint_get_auth_returns_asserted_data_format(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/auth');

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'created_at'
            ]

        ]);

        $response->assertJsonMissingPath('data.password');
        $response->assertJsonMissingPath('data.updated_at');

        $user->delete();
    }

    /**********************
     * Benutzer ausloggen *
     **********************/

    /**
     * Die Route `/api/logout` mit der entsprechenden Methode soll über die API erreichbar sein.
     *
     * @group user-logout
     */
    public function test_l1_endpoint_post_logout_returns_status_code_401_without_valid_user(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    /**
     * @group user-logout
     */
    public function test_l1_endpoint_post_logout_returns_status_code_200_with_valid_user(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->postJson('/api/logout');
        $response->assertStatus(200);
        $user->delete();
    }

    /**
     * Die Route `/api/logout` soll bei fehlgeschlagener Authentifizierung die entsprechenden Fehlermeldungen zurückgeben.
     *
     * @group user-logout
     */
    public function test_l2_endpoint_get_auth_returns_401_and_error_message_with_logged_out_user(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());
        $this->postJson('/api/logout');

        $this->app->get('auth')->forgetGuards();

        $response = $this->getJson('/api/auth');
        $response->assertStatus(401)->assertJson([
            "message" => "Unauthenticated."
        ]);

        $user->delete();
    }

    /**
     * Die Route `/api/logout` soll bei erfolgreicher Authentifizierung den Benutzer ausloggen und die entsprechende Meldung zurückgeben.
     *
     * @group user-logout
     */
    public function test_l3_endpoint_post_logout_returns_success_message_with_logged_out_user(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/logout');
        $response->assertStatus(200)->assertJson([
            "message" => "Logged out."
        ]);

        $user->delete();
    }

    /**********************
     * Account-Übersicht *
     **********************/

    /**
     * Die geschützte Route `/api/users/my-account` gibt die Daten `name`, `email` und `created_at` des angemeldeten Benutzers zurück.
     *
     * @group user-account
     */

    public function test_o1_endpoint_get_account_returns_user_data(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/users/my-account');

        $response->assertJsonPath('data.name', $user->name);
        $response->assertJsonPath('data.email', $user->email);

        $response->assertJsonMissingPath('data.password');
        $response->assertJsonMissingPath('data.updated_at');

        $user->delete();
    }

    /**
     * Die geschützte Route `/api/account` löscht den aktuell angemeldeten Benutzer.
     *
     * @group user-account
     */

    public function test_o2_endpoint_delete_account_deletes_user(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->deleteJson('/api/users/my-account');

        $response->assertSuccessful();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }

}
