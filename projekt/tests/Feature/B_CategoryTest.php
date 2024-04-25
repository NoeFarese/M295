<?php

namespace Tests\Feature;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class B_CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /*****************************
     * Kategorien-Liste anzeigen *
     *****************************/

    /**
     * Die Route `/api/transactions` mit der entsprechenden Methode soll über die API erreichbar sein.
     *
     * @group categories-list
     */
    public function test_d1_endpoint_get_categories_returns_status_code_200(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);
    }

    /**
     * Der Seeder soll die Datenbank mit mindestens 10 Kategorien füllen.
     *
     * @group categories-list
     */
    public function test_d2_if_seeder_creates_at_minimum_10_categories(): void
    {
        $this->assertGreaterThanOrEqual(10, Category::count());
    }

    /**
     * @group categories-list
     */
    public function test_d3_endpoint_get_categories_returns_asserted_data_format(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name'
                ]
            ]
        ]);
    }

    /**
     * Die Route `/api/categories` soll nur die definierten Felder aus der Response-Definition zurückgeben.
     *
     * @group categories-list
     */
    public function test_d4_endpoint_get_categories_not_returns_unnecessary_data(): void
    {
        $response = $this->getJson('/api/categories');
        $response->assertSuccessful();

        $response->assertJsonMissingPath('data.0.created_at');
        $response->assertJsonMissingPath('data.0.updated_at');
    }


    /**
     * Die Route `/api/categories` soll die Kategorien in der Response alphabetisch nach dem Namen sortiert zurückgeben.
     *
     * @group categories-list
     */
    public function test_d5_endpoint_get_categories_return_categories_sort_by_name(): void
    {
        $response = $this->getJson('/api/categories');

        $this->assertEquals(
            Category::orderBy('name')->pluck('id')->toArray(),
            array_column($response['data'], 'id')
        );
    }

    /*******************
     * Kategorie laden *
     *******************/

    /**
     * Die Route `/api/categories/{id}` mit der entsprechenden Methode soll über die API erreichbar sein.
     *
     * @group category
     */
    public function test_e1_endpoint_get_category_returns_status_code_200(): void
    {
        $category = Category::first();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200);
    }

    /**
     * Der Endpunkt weisst Anfragen ohne existierende ID mit einem 404 zurück.
     *
     * @group category
     */
    public function test_e2_endpoint_get_categories_id_returns_status_code_404_if_id_is_not_found(): void
    {
        $invalidId = Category::max('id') + 1;

        $response = $this->getJson("/api/categories/{$invalidId}");

        $response->assertStatus(404);
    }

    /**
     * Die Route `/api/categories/{id}` soll die entsprechende Kategorie zurückgeben.
     *
     * @group category
     */
    public function test_e3_endpoint_get_category_returns_requested_category(): void
    {
        $category = Category::first();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertJsonFragment([
            'id' => $category->id,
        ]);
    }


    /**
     * Die Route `/api/categories/{id}` soll nur die definierten Felder aus der Response-Definition zurückgeben.
     *
     * @group category
     */
    public function test_e4_endpoint_get_category_returns_asserted_data_format(): void
    {
        $category = Category::first();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ]
        ]);
    }

    /**
     * Die Route `/api/categories/{id}` soll nur die definierten Felder aus der Response-Definition zurückgeben.
     *
     * @group category
     */
    public function test_e4_endpoint_get_category_not_returns_unnecessary_data(): void
    {
        $category = Category::first();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertJsonMissingPath('data.created_at');
        $response->assertJsonMissingPath('data.updated_at');
    }

    /************************
     * Beziehung definieren *
     ************************/

    /**
     *
     * Transaktionen und Kategorien sollen über eine Beziehung miteinander verbunden werden.
     *
     * @group relationship
     */
    public function test_f1_if_category_has_transactions(): void
    {
        $category = Category::first();

        $this->assertInstanceOf(Transaction::class, $category->transactions->first());
    }

    /**
     * @group relationship
     */
    public function test_f1_if_transactions_have_category(): void
    {
        $transaction = Transaction::first();

        $this->assertInstanceOf(Category::class, $transaction->category);
    }

    /**
     * Die beiden Models haben eine One-To-Many-Beziehung.
     *
     * @group relationship
     */
    public function test_f3_relationship_is_one_to_many(): void
    {
        $category = new Category();
        $transaction = new Transaction();

        $this->assertTrue(method_exists($category, 'transactions'), 'Missing transactions method on Category');
        $this->assertTrue(method_exists($transaction, 'category'), 'Missing category method on Transaction');

        $this->assertInstanceOf(HasMany::class, $category->transactions());
        $this->assertInstanceOf(BelongsTo::class, $transaction->category());
    }

    /**
     *
     * Jede im Seeder erstellte Transaktion soll neu zufälligerweise einer Kategorie zugeordnet werden.
     *
     * @group relationship
     */
    public function test_f2_if_seeder_creates_at_minimum_10_categories_with_25_transactions_per_type(): void
    {
        $this->assertGreaterThanOrEqual(10, Category::count());

        $this->assertEquals(null, Transaction::doesntHave('category')->first());
    }

    /*************************************
     * Transaktionen mit Kategorie laden *
     *************************************/

    /**
     * Die Route `/api/transactions` soll eine Liste aller Transaktionen mit dazugehöriger Kategorie zurück.
     *
     * @group relation-transactions-category
     */
    public function test_g1_endpoint_get_transactions_returns_asserted_data_format(): void
    {
        $response = $this->getJson('/api/transactions');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'amount',
                    'created_at',
                    'comment',
                    'category' => [
                        'id',
                        'name'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Die Route `/api/transactions` soll nur die definierten Felder aus der Response-Definition zurückgeben.
     *
     * @group relation-transactions-category
     */
    public function test_g2_endpoint_get_transactions_not_returns_unnecessary_data(): void
    {
        $response = $this->getJson('/api/transactions');
        $response->assertSuccessful();

        $response->assertJsonMissingPath('data.0.updated_at');
        $response->assertJsonMissingPath('data.0.category.created_at');
        $response->assertJsonMissingPath('data.0.category.updated_at');
    }


    /***************************************
     * Transaktionen einer Kategorie laden *
     ***************************************/

    /**
     *
     * Die Route `/api/categories/{id}/transactions` mit der entsprechenden Methode soll über die API erreichbar sein.
     *
     * @group relation-category-transactions
     */
    public function test_h1_endpoint_get_category_transactions_returns_status_code_200(): void
    {
        $category = Category::first();

        $response = $this->getJson("/api/categories/{$category->id}/transactions");

        $response->assertStatus(200);
    }

    /**
     * Der Endpunkt weisst Anfragen ohne existierende ID mit einem 404 zurück.
     *
     * @group relation-category-transactions
     */
    public function test_h2_endpoint_get_categories_id_returns_status_code_404_if_id_is_not_found(): void
    {
        $invalidId = Category::max('id') + 1;

        $response = $this->getJson("/api/categories/{$invalidId}/transactions");

        $response->assertStatus(404);
    }

    /**
     *
     * Die Route `/api/categories/{id}/transactions` soll die Transaktionen der entsprechenden Kategorie zurückgeben.
     *
     * @group relation-category-transactions
     */
    public function test_h3_endpoint_get_category_transactions_returns_transactions_of_category(): void
    {
        $category = Category::first();

        $response = $this->getJson("/api/categories/{$category->id}/transactions");

        $response->assertJson([
            'data' => [
                [
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name
                    ]
                ]
            ]
        ]);
    }

    /**
     *
     * Die Route `/api/categories/{id}/transactions` soll nur die definierten Felder aus der Response-Definition zurückgeben.
     *
     * @group relation-category-transactions
     */
    public function test_h4_endpoint_get_category_transactions_returns_asserted_data_format(): void
    {
        $category = Category::first();

        $response = $this->getJson("/api/categories/{$category->id}/transactions");

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'amount',
                    'created_at',
                    'comment',
                    'category' => [
                        'id',
                        'name'
                    ]
                ]
            ]
        ]);
    }

    /**
     * @group relation-category-transactions
     */
    public function test_h4_endpoint_get_category_transactions_not_returns_unnecessary_data(): void
    {
        $category = Category::first();

        $response = $this->getJson("/api/categories/{$category->id}/transactions");

        $response->assertJsonMissingPath('data.0.updated_at');
        $response->assertJsonMissingPath('data.0.category.created_at');
        $response->assertJsonMissingPath('data.0.category.updated_at');
    }

    /**
     *
     * Die Route `/api/categories/{id}/transactions` soll die Transaktionen in der Response gemäss den gleichen
     * Sortierkriterien wie unter "Transaktionen laden" definiert zurückgeben (max. 100; neuste zuoberst).
     *
     * @group relation-category-transactions
     */
    public function test_h5_endpoint_get_category_transactions_return_100_transactions_sort_by_created_at_desc(): void
    {
        $category = Category::latest()->first();

        $response = $this->getJson("/api/categories/{$category->id}/transactions");

        $latest = $category->transactions()->latest()->first();
        $first = $category->transactions()->latest()->skip(count($response['data']) - 1)->first();

        $this->assertNotCount(0, $response['data']);
        $this->assertEquals($latest->id, $response['data'][0]['id']);
        $this->assertEquals($first->id, $response['data'][count($response['data']) - 1]['id']);
    }

    /************************
     * Kategorie bearbeiten *
     ************************/

    /**
     * Die Route `/api/categories/{id}` mit der entsprechenden Methode soll über die API erreichbar sein.
     *
     * @group category-edit
     */
    public function test_i1_put_endpoint_categories_id_returns_status_code_200(): void
    {
        $category = Category::first();

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Test',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Der Endpunkt weisst Anfragen ohne existierende ID mit einem 404 zurück.
     *
     * @group category-edit
     */
    public function test_i2_endpoint_get_categories_id_returns_status_code_404_if_id_is_not_found(): void
    {
        $invalidId = Category::max('id') + 1;

        $response = $this->getJson("/api/categories/{$invalidId}/transactions");

        $response->assertStatus(404);
    }

    /**
     * Die Route `/api/categories/{id}` soll die gesendeten Daten validieren.
     *
     * @group category-edit
     */
    public function test_i3_endpoint_put_categories_id_returns_status_code_422_if_data_is_not_valid(): void
    {
        $response = $this->putJson('/api/categories/1', [
            'name' => '',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Die Route `/api/categories/{id}` soll bei fehlgeschlagener Validierung die entsprechenden Fehlermeldungen zurückgeben.
     *
     * @group category-edit
     */
    public function test_i4_endpoint_put_categories_id_returns_asserted_validation_errors(): void
    {
        $response = $this->putJson('/api/categories/1', [
            'name' => '',
        ]);

        $response->assertJsonValidationErrors([
            'name'
        ]);
    }

    /**
     * Die Route `/api/categories/{id}` soll die entsprechende Kategorie bearbeiten.
     *
     * @group category-edit
     */
    public function test_i5_endpoint_put_categories_id_returns_updated_category(): void
    {
        $category = Category::first();

        $response = $this->putJson('/api/categories/'.$category->id, [
            'name' => 'Test',
        ]);

        $category->name = 'Test';

        $response->assertExactJson([
            'data' => CategoryResource::make($category)->resolve()
        ]);
    }

    /**
     * Die Route `/api/categories/{id}` soll die aktualisierte Kategorie im definierten Format zurückgeben.
     *
     * @group category-edit
     */
    public function test_i6_endpoint_put_categories_id_returns_asserted_data_format(): void
    {
        $category = Category::first();

        $response = $this->putJson('/api/categories/'.$category->id, [
            'name' => 'Test',
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name'
            ]
        ]);
    }

}
