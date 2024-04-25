<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClownRequest;
use App\Http\Resources\ClownResource;
use App\Models\Clown;
use Illuminate\Http\Request;

class ClownController extends Controller
{
    public function index()
    {
        $clowns = Clown::get();
        return ClownResource::collection($clowns);
    }

    public function update(ClownRequest $request, int $id)
    {
        $clown = Clown::find($id);
        $clown->fill($request->all());
        $clown->save();

        return ClownResource::make($clown);
    }

    public function store(ClownRequest $request)
    {
        $clown = new Clown();
        $clown->fill($request->all());
        $clown->save();

        return ClownResource::make($clown);
    }

    public function destroy(ClownRequest $request, int $id)
    {
        $clown = Clown::find($id);
        $clown->delete();

        return ClownResource::make($clown);
    }
}
