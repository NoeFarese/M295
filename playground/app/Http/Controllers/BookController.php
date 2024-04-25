<?php

namespace App\Http\Controllers;

use App\Models\book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        return book::get();
    }

    public function show(int $id)
    {
        return book::find($id);
    }

    public function bySlug(string $slug)
    {
        return book::where('slug', '=', $slug)->get();
    }

    public function byYear(int $year)
    {
        return book::where('year', '=', $year)->get();
    }

    public function pages(int $pages)
    {
        return book::where('pages', '<', $pages)->get();
    }

    public function count()
    {
        return book::count();
    }

    public function avg()
    {
        return book::avg('pages');
    }

    public function search(string $search)
    {
        return Book::where('title', 'like', '%' . $search . '%')
            ->orWhere('author', 'like', '%' . $search . '%')
            ->get();
    }
}
