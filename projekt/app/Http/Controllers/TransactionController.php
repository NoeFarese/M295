<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('category')->latest()->take(100)->get();
        return TransactionResource::collection($transactions);
    }

    public function switchTypeOfTransaction(int $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction){
            return response()->json(['errors' => ['general' => 'Transaction does not exist']], 404);
        }

        if ($transaction->type === 'expense') {
            $transaction->type = 'income';
        } else {
            $transaction->type = 'expense';
        }

        $transaction->save();
        return TransactionResource::make($transaction);
    }

    public function destroy(int $id)
    {
        $transaction = Transaction::find($id);
        if(!$transaction){
            return response()->json(['errors' => ['general' => 'Transaction does not exist']], 404);
        }
        $transaction->delete();
        return response()->json(['message' => 'Transaktion wurde erfolgreich entfernt.']);
    }

    public function store(StoreTransactionRequest $request)
    {
        $transaction = new Transaction();
        $transaction->name = $request->name;
        $transaction->type = $request->type;
        $transaction->amount = $request->amount;
        $transaction->category_id = $request->category_id;
        $transaction->created_at = $request->created_at;
        $transaction->comment = $request->comment;
        $transaction->save();
        return TransactionResource::make($transaction);
    }

    public function getTotals()
    {
        $incomeTotal = Transaction::where('type', 'income')->sum('amount');
        $expenseTotal = Transaction::where('type', 'expense')->sum('amount');

        return response()->json(['data' => ['income' => $incomeTotal, 'expense' => $expenseTotal]]);
    }
}
