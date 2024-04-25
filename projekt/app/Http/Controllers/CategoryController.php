<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TransactionResource;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return CategoryResource::collection($categories);
    }

    public function show(Category $category)
    {
        return CategoryResource::make($category);
    }

    public function getTransactionsByCategory(Category $category)
    {
        $transactions = Transaction::with('category')->where('category_id', $category->id)->latest()->take(100)->get();
        return TransactionResource::collection($transactions);
    }

    public function editCategory(CategoryRequest $request, int $id)
    {
        $category = Category::find($id);

        if (!$category){
            return response()->json(['errors' => ['general' => 'Category not found']], 404);
        }

        $category->name = $request->name;
        $category->save();
        return CategoryResource::make($category);
    }
}
