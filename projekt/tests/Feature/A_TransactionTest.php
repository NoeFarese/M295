<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class A_TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /********************************
     * Transaktionen-Liste anzeigen *
     ********************************/

    /***
     * Die Route `/api/transactions` mit der entsprechenden Methode soll über die API erreichbar sein.
     *
     * @group transactions-list
     */
    public function test_a1_endpoint_get_transactions_returns_status_code_200(): void
    {
        $response = $this->getJson('/api/transactions');

        $response->assertStatus(200);
    }

    /**
     * Der Seeder soll die Datenbank mit je 250 Transaktionen pro Typ (`income` und `expense`) füllen.
     *
     * @group transactions-list
     */
    public function test_a2_if_seeder_creates_at_minimum_250_transactions_of_each_type(): void
    {
        $expenses = Transaction::where('type', 'expense')->count();
        $incomes = Transaction::where('type', 'income')->count();

        $this->assertGreaterThanOrEqual(250, $expenses);
        $this->assertGreaterThanOrEqual(250, $incomes);
    }

    /**
     * Die Testwerte für das Transaktionsdatum sollen zwischen heute und heute vor 60 Tagen liegen.
     *
     * @group transactions-list
     */
    public function test_a3_if_seeder_creates_date_between_60_days_ago_and_now(): void
    {
        $this->assertCount(0,
            Transaction::where('created_at', '<', now()->subDays(60)->startOfDay())->orWhere('created_at', '>',
                now()->endOfDay())->get()
        );
    }

    /**
     * Die Testwerte für die Höhe der Transaktion sollen zwischen 0.00 und 10'000.00 liegen.
     *
     * @group transactions-list
     */
    public function test_a4_if_seeder_creates_amount_between_0_00_and_10000_00(): void
    {
        $this->assertCount(0,
            Transaction::where('amount', '<', 0.00)->orWhere('amount', '>', 10000.00)->get()
        );
    }

    /**
     * Die Route `/api/transactions` soll eine Liste aller Transaktionen zurückgeben.
     *
     * @group transactions-list
     */
    public function test_a5_endpoint_get_transactions_returns_asserted_data_format(): void
    {
        $response = $this->getJson('/api/transactions');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'amount',
                    'comment',
                    'type',
                    'created_at',
                ]
            ]
        ]);
    }

    /**
     * Die Route `/api/transactions` soll nur die definierten Felder aus der Response-Definition zurückgeben.
     *
     * @group transactions-list
     */
    public function test_a6_endpoint_get_transactions_not_returns_unnecessary_data(): void
    {
        $response = $this->getJson('/api/transactions');
        $response->assertSuccessful();

        $response->assertJsonMissingPath('data.0.updated_at');
    }

    /**
     * Die Route `/api/transactions` soll nur die neusten 100 Transaktionen nach Transaktionsdatum zurückgeben.
     *
     * @group transactions-list
     */
    public function test_a7_endpoint_get_transactions_return_only_100_transactions(): void
    {
        $response = $this->getJson('/api/transactions');

        $this->assertCount(100, $response['data']);
    }

    /**
     * Das Feld `amount` soll in der Response in eine Negativzahl umgewandelt werden (`-`), wenn der Transaktionstyp `expense` ist.
     *
     * @group transactions-list
     */
    public function test_a8_endpoint_get_transactions_returns_negative_amount_if_type_is_expense(): void
    {
        $response = $this->getJson('/api/transactions');

        foreach ($response['data'] as $transaction) {
            if ($transaction['type'] === 'expense') {
                $this->assertLessThan(0, $transaction['amount']);
            }
        }
    }

    /**
     * @group transactions-list
     */
    public function test_a8_endpoint_get_transactions_returns_positive_amount_if_type_is_income(): void
    {
        $response = $this->getJson('/api/transactions');

        foreach ($response['data'] as $transaction) {
            if ($transaction['type'] === 'income') {
                $this->assertGreaterThan(0, $transaction['amount']);
            }
        }
    }

    /**
     * Die Datenbank lässt Dezimalzahlen zu.
     *
     * @group transactions-list
     */
    public function test_a9_if_database_field_amount_can_have_digits_after_comma(): void
    {
        $transaction = Transaction::first();
        $transaction->amount = 1.12;
        $transaction->save();
        $transaction->refresh();

        $this->assertEquals(1.12, $transaction->amount);
    }

    /**
     * Die Transaktionen sollen in der Response nach dem Datum der Erstellung absteigend (neuste zuerst) sortiert werden.
     *
     * @group transactions-list
     */
    public function test_a10_endpoint_get_transactions_return_100_transactions_sort_by_created_at_desc(): void
    {
        $response = $this->getJson('/api/transactions');

        $latest = Transaction::latest()->first();
        $first = Transaction::latest()->skip(99)->first();

        $this->assertCount(100, $response['data']);
        $this->assertEquals($latest->id, $response['data'][0]['id']);
        $this->assertEquals($first->id, $response['data'][99]['id']);
    }


    /**
     * Die Factory erstellt Dezimalzahlen beim Feld `amount`.
     *
     * @group transactions-list
     */
    public function test_a11_transaction_factory_generates_float_amounts(): void
    {
        $transaction = Transaction::factory()->make();

        $this->assertIsFloat($transaction->amount);
    }

    /**
     * Typ der Transaktion ändern
     */

    /**
     * Die Route `/api/transactions/{id}/switch-type` mit der entsprechenden Methode soll über die API erreichbar sein.
     *
     * @group transactions-switch-type
     */
    public function test_b1_endpoint_put_transactions_id_returns_status_code_200(): void
    {
        $response = $this->putJson('/api/transactions/1/switch-type');

        $response->assertStatus(200);
    }

    /**
     * Der Endpunkt weisst Anfragen ohne existierende ID mit einem 404 zurück.
     *
     * @group transactions-switch-type
     */
    public function test_b2_endpoint_put_transactions_id_returns_status_code_404_if_id_is_not_found(): void
    {
        $invalidId = Transaction::max('id') + 1;

        $response = $this->putJson("/api/transactions/{$invalidId}/switch-type");

        $response->assertStatus(404);
    }

    /**
     * Der Endpunkt kehrt den Transaktionstyp um: von `expense` zu `income` und umgekehrt.
     *
     * @group transactions-switch-type
     */
    public function test_b3_endpoint_put_transactions_id_returns_type_expense_if_type_was_income(): void
    {
        $transaction = Transaction::where('type', 'income')->first();

        $response = $this->putJson('/api/transactions/' . $transaction->id . '/switch-type');

        $this->assertEquals('expense', $response['data']['type']);
    }

    /**
     *  Der Endpunkt gibt die geänderte Transaktion wie definiert zurück.
     *
     * @group transactions-switch-type
     */
    public function test_b4_endpoint_put_transactions_id_returns_asserted_data_format(): void
    {
        $transaction = Transaction::first();

        $response = $this->putJson('/api/transactions/' . $transaction->id . '/switch-type');

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'amount',
                'comment',
                'type',
                'created_at',
            ]
        ]);
    }

    /**
     * @group transactions-switch-type
     */
    public function test_b4_endpoint_put_transactions_id_returns_updated_transaction(): void
    {
        $transaction = Transaction::where('type', 'income')->first();

        $response = $this->putJson('/api/transactions/' . $transaction->id . '/switch-type');

        $response->assertJsonPath('data.type', 'expense');
    }

    /***********************
     * Transaktion löschen *
     ***********************/

    /**
     * Die dazugehörige Route `/api/transactions/{id}` soll über den API-Endpunkt erreichbar sein.
     *
     * @group transactions-delete
     */
    public function test_c1_endpoint_delete_transactions_id_returns_status_code_200(): void
    {
        $response = $this->deleteJson('/api/transactions/1');

        $response->assertStatus(200);
    }

    /**
     * Der Endpunkt weisst Anfragen ohne existierende ID mit einem 404 zurück.
     *
     * @group transactions-delete
     */
    public function test_c2_endpoint_delete_transactions_id_returns_status_code_404_if_id_is_not_found(): void
    {
        $invalidId = Transaction::max('id') + 1;

        $response = $this->deleteJson("/api/transactions/{$invalidId}");

        $response->assertStatus(404);
    }

    /**
     * Der Endpunkt löscht die entsprechende Transaktion.
     *
     * @group transactions-delete
     */
    public function test_c3_endpoint_delete_transactions_id_deletes_transaction(): void
    {
        $transaction = Transaction::first();

        $this->deleteJson('/api/transactions/' . $transaction->id);

        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id,
        ]);
    }

    /**
     * Der Endpunkt gibt die Erfolgsmeldung gemäss Response-Definition zurück.
     *
     * @group transactions-delete
     */
    public function test_c4_endpoint_delete_transactions_id_returns_asserted_data_format(): void
    {
        $transaction = Transaction::first();

        $response = $this->deleteJson('/api/transactions/' . $transaction->id);

        $response->assertExactJson([
            'message' => 'Transaktion wurde erfolgreich entfernt.'
        ]);
    }

    /*************************
     * Transaktion erstellen *
     *************************/

    /**
     * Die Route `/api/transactions` mit der entsprechenden Methode soll über die API erreichbar sein.
     * Die geschütze Route `/api/transactions` soll vor der Verarbeitung des Requests die Authentifizierung überprüfen.
     *
     * @group transaction-create
     */

    public function getTestTransaction(): Collection
    {
        return collect([
            'name' => 'Test',
            'amount' => 1.23,
            'comment' => 'Test',
            'type' => 'expense',
            'category_id' => 1,
            'created_at' => '2021-01-01',
        ]);
    }

    public function test_m1_endpoint_post_transactions_returns_status_code_201_with_valid_user(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions', $this->getTestTransaction()->toArray());

        $response->assertStatus(201);

        $user->delete();
    }

    /**
     * @group transaction-create
     */
    public function test_m1_endpoint_post_transactions_returns_status_code_201_without_valid_user(): void
    {
        $response = $this->postJson('/api/transactions', $this->getTestTransaction()->toArray());

        $response->assertStatus(401);
    }

    /**
     * Die Route `/api/transactions` soll die gesendeten Daten validieren und bei fehlgeschlagener Validierung die entsprechenden Fehlermeldungen zurückgeben.
     *
     * @group transaction-create
     */

    public function test_m2_endpoint_post_transactions_returns_status_code_422_if_invalid_data(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions', []);
        $response->assertStatus(422);

        $user->delete();
    }

    /**
     * Das Feld `name` ist ein Pflichtfeld und muss eine Zeichenkette sein.
     *
     * @group transaction-create
     */
    public function test_m3_endpoint_post_transactions_returns_status_code_422_if_name_is_absent(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions',
            $this->getTestTransaction()->put('name', null)->toArray()
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');

        $user->delete();
    }

    /**
     * Das Feld `type` ist ein Pflichtfeld und muss entweder `expense` oder `income` sein.
     *
     * @group transaction-create
     */
    public function test_m4_endpoint_post_transactions_returns_status_code_422_if_type_is_absent(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions',
            $this->getTestTransaction()->put('type', null)->toArray()
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('type');

        $user->delete();
    }

    /**
     * @group transaction-create
     */
    public function test_m4_endpoint_post_transactions_returns_status_code_422_if_type_is_not_expense_or_income(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions',
            $this->getTestTransaction()->put('type', 'test')->toArray()
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('type');

        $user->delete();
    }

    /**
     * Das Feld `amount` ist ein Pflichtfeld und muss eine Zahl sein.
     *
     * @group transaction-create
     */
    public function test_m5_endpoint_post_transactions_returns_status_code_422_if_amount_is_absent(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions',
            $this->getTestTransaction()->put('amount', null)->toArray()
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('amount');

        $user->delete();
    }

    /**
     * @group transaction-create
     */
    public function test_m5_endpoint_post_transactions_returns_status_code_422_if_amount_is_not_a_number(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions',
            $this->getTestTransaction()->put('amount', 'abc')->toArray()
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('amount');

        $user->delete();
    }

    /**
     * Das Feld `category_id` ist ein Pflichtfeld und muss eine Zahl sein.
     *
     * @group transaction-create
     */
    public function test_m6_endpoint_post_transactions_returns_status_code_422_if_category_id_is_absent(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions',
            $this->getTestTransaction()->put('category_id', null)->toArray()
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('category_id');

        $user->delete();
    }

    /**
     * Das Feld `category_id` muss eine gültige/existierende Kategorie-ID sein.
     *
     * @group transaction-create
     */
    public function test_m7_endpoint_post_transactions_returns_status_code_422_if_category_id_is_not_a_valid_category_id(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions',
            $this->getTestTransaction()->put('category_id', 9999999)->toArray()
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('category_id');

        $user->delete();
    }

    /**
     * Das Feld `created_at` ist ein Pflichtfeld und muss ein gültiges Datum sein.
     *
     * @group transaction-create
     */
    public function test_m8_endpoint_post_transactions_returns_status_code_422_if_created_at_is_absent(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions',
            $this->getTestTransaction()->put('created_at', null)->toArray()
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('created_at');

        $user->delete();
    }

    /**
     * @group transaction-create
     */
    public function test_m8_endpoint_post_transactions_returns_status_code_422_if_created_at_is_not_a_valid_date(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions',
            $this->getTestTransaction()->put('created_at', 'abc')->toArray()
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('created_at');

        $user->delete();
    }

    /**
     * Das Feld `comment` ist optional und muss eine Zeichenkette sein.
     *
     * @group transaction-create
     */
    public function test_m9_endpoint_post_transactions_returns_status_code_201_if_comment_is_absent(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions',
            $this->getTestTransaction()->put('comment', null)->toArray()
        );

        $response->assertStatus(201);

        $user->delete();
    }

    /**
     * Die Route `/api/transactions` soll bei erfolgreicher Validierung die Daten speichern und die erstellte Transaktion zurückgeben.
     *
     * @group transaction-create
     */
    public function test_m10_endpoint_post_transactions_returns_status_code_201_and_transaction_if_valid_data_is_provided(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions',
            $this->getTestTransaction()->toArray()
        );

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'amount',
                'comment',
                'created_at',
                'category' => [
                    'id',
                    'name',
                ],
            ]
        ]);

        $assertedTransaction = $this->getTestTransaction()->toArray();


        $response->assertJsonFragment(['name' => $assertedTransaction['name']]);
        $response->assertJsonFragment(['type' => $assertedTransaction['type']]);
        $response->assertJsonFragment(['amount' => ($assertedTransaction['amount'] * -1)]);
        $response->assertJsonFragment(['type' => $assertedTransaction['type']]);
        $response->assertJsonFragment(['comment' => $assertedTransaction['comment']]);
        $response->assertJsonFragment(['created_at' => Carbon::make($assertedTransaction['created_at'])->toIso8601String()]);
        $this->assertEquals($response->json('data.category.id'), $assertedTransaction['category_id']);


        $user->delete();
    }



    /*************************************************
     * Transaktionen mit Benutzer verknüpfen (Bonus) *
     *************************************************/

    /**
     * Transaktionen und Benutzer sollen über eine Beziehung miteinander verbunden werden - jede Transaktion gehört
     * zwingend zu einem Benutzer.
     *
     * @group bonus
     */
    public function test_p1_every_transaction_belongs_to_existing_user_id()
    {
        $this->assertEquals(null, Transaction::doesntHave('user')->first());
    }

    /**
     * Wird eine Transaktion im Frontend erstellt, soll diese dem aktuell angemeldeten Benutzer zugeordnet werden.
     *
     * @group bonus
     */
    public function test_p2_endpoint_post_transactions_creates_a_transaction_with_the_current_user_id()
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/transactions',
            $this->getTestTransaction()->toArray()
        );

        $response->assertStatus(201);

        $response->assertJsonPath('data.user.id', $user->id);

        $user->delete();
    }

    /**
     * Wenn ein Benutzer gelöscht wird, sollen die dazugehörigen Transaktionen ebenfalls gelöscht werden.
     *
     * @group bonus
     */
    public function test_p3_endpoint_delete_account_deletes_all_transactions_of_the_user()
    {
        $user = Sanctum::actingAs(User::factory()->create());
        Transaction::factory()->count(5)->create(['user_id' => $user->id, 'category_id' => 1]);

        $response = $this->deleteJson('/api/users/my-account');
        $response->assertSuccessful();

        $this->assertEquals(0, Transaction::where('user_id', $user->id)->count());
    }

    /**
     * Jede im Seeder erstellte Transaktion soll neu zufälligerweise einem User zugeordnet werden.
     *
     * @group bonus
     */
    public function test_p4_transaction_factory_assigns_random_user_id()
    {
        $transactions = Transaction::factory(15)->create(['category_id' => 1]);

        $transactions->each(fn (Transaction $transaction) => $this->assertNotNull($transaction->user_id));

        $this->assertGreaterThan(3, $transactions->unique('user_id')->count(), 'Transaction seeder seems to generate only one user id.');
    }

}
