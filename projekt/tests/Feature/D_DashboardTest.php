<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class D_DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /****************************
     * Kennzahlen für Übersicht *
     ****************************/

    /**
     * Die Route `/api/transations/totals` mit der entsprechenden Methode soll über die API erreichbar sein.
     *
     * @group dashboard
     */
    public function test_n1_endpoint_get_transactions_totals_returns_status_code_200(): void
    {
        $response = $this->getJson('/api/transactions/totals');

        $response->assertStatus(200);
    }


    /**
     * Die Route `/api/transations/totals` soll die Summe der Einnahmen und Ausgaben zurückgeben.
     *
     * @group dashboard
     */
    public function test_n2_endpoint_get_transactions_totals_returns_totals_with_correct_values(): void
    {
        $response = $this->getJson('/api/transactions/totals');

        $income = Transaction::where('type', 'income')->sum('amount');
        $expense = Transaction::where('type', 'expense')->sum('amount');

        $this->assertEquals(round($response->json('data.income'), 2), $income);
        $this->assertEquals(round($response->json('data.expense'), 2), $expense);
    }


    /**
     * Die Route `/api/transations/totals` soll nur die definierten Felder aus der Response-Definition zurückgeben.
     *
     * @group dashboard
     */
    public function test_n3_endpoint_get_transactions_totals_returns_totals(): void
    {
        $response = $this->getJson('/api/transactions/totals');

        $response->assertJsonStructure([
            'data' => [
                'income',
                'expense',
            ]
        ]);
    }


}
