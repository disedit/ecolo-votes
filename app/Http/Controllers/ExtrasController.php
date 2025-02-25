<?php

namespace App\Http\Controllers;

use App\Models\Extra;
use App\Models\ExtraList;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ExtrasController extends Controller
{
    /**
     * Add an extra
     */
    public function add(Extra $extra, Request $request): RedirectResponse {

        if ($extra->full()) {
            return to_route('badge')->with('message', 'There are no more spaces available to take part in this activity.');
        }

        $row = new ExtraList;
        $row->extra_id = $extra->id;
        $row->user_id = $request->user()->id;
        $row->save();

        return to_route('badge');
    }

    /**
     * Remove an extra
     */
    public function remove(Extra $extra, Request $request): RedirectResponse {
        $extra->row($request->user()->id)->delete();

        return to_route('badge');
    }
}
