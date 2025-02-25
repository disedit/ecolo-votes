<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Extra;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExtrasController extends Controller
{
    /**
     * List extras
     */
    public function extras(): Response
    {
        $extras = Extra::with(['list' => fn ($query) => $query->with('user')])->get()->map(function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'capacity' => $row->capacity,
                'list' => $row->list->map(function ($listRow) {
                    return [
                        'id' => $listRow->id,
                        'first_name' => $listRow->user->first_name,
                        'last_name' => $listRow->user->last_name,
                        'email' => $listRow->user->email,
                        'signed_up' => $listRow->created_at
                    ];
                })
            ];
        });

        return Inertia::render('Admin/Extras', [
            'extras' => $extras
        ]);
    }
}
