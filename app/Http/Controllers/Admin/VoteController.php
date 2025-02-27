<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Edition;
use App\Models\Attendee;
use App\Models\Vote;
use App\Models\VoteOption;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class VoteController extends Controller
{
    /**
     * Votes
     */
    public function votes(): Response
    {
        $ongoingVote = Vote::currentOrRecentlyClosed();
        return Inertia::render('Admin/Votes', [
            'votes' => Vote::with('winner')->orderBy('order', 'asc')->orderBy('id', 'asc')->get(),
            'ongoing' => ($ongoingVote) ? $ongoingVote->append(['results', 'recentlyClosed']) : null
        ]);
    }

    /**
     * Get a vote
     */
    public function vote(Vote $vote): JsonResponse
    {
        $vote->load('options');
        $vote->append(['results', 'recentlyClosed']);
        return response()->json($vote);
    }

    /**
     * Create vote
     */
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'subtitle' => '',
            'majority' => 'in:simple,50_with_abs,50_without_abs,2/3_with_abs,2/3_without_abs',
            'type' => 'in:yesno,options',
            'secret' => 'boolean',
            'abstain' => 'boolean',
            'open_immediately' => 'boolean',
            'options.*.name' => 'required_if:type,options',
            'options.*.description' => 'nullable',
            'options.*.gender' => 'nullable|in:M,F,O',
            'options.*.region' => 'nullable',
            'options.*.enabled' => 'nullable|boolean'
        ]);

        $lastVote = Vote::orderBy('order', 'desc')->orderBy('id', 'desc')->first();

        DB::transaction(function () use ($request, $lastVote) {
            $vote = new Vote;
            $vote->edition_id = Edition::current()->first()->id;
            $vote->name = $request->input('name');
            $vote->subtitle = $request->input('subtitle');
            $vote->majority = $request->input('majority');
            $vote->type = $request->input('type');
            $vote->secret = $request->input('secret');
            $vote->order = ($lastVote) ? $lastVote->order + 1 : 1;
            $vote->save();

            if ($request->input('open_immediately')) {
                $vote->openVote();
            }

            if ($request->input('type') === 'yesno') {
                $options = [
                    ['name' => 'Yes', 'description' => null, 'gender' => null, 'region' => null, 'enabled' => true],
                    ['name' => 'No', 'description' => null, 'gender' => null, 'region' => null, 'enabled' => true]
                ];
            } else {
                $options = $request->input('options');
            }

            if ($request->input('abstain') || $request->input('type') === 'yesno') {
                $options[] = ['name' => 'Abstain', 'description' => null, 'gender' => null, 'region' => null, 'is_abstain' => true, 'enabled' => true];
            }

            // Insert options
            foreach($options as $option) {
                if (!$option['enabled'] || !isset($option['enabled'])) continue;
                $newOption = new VoteOption;
                $newOption->vote_id = $vote->id;
                $newOption->name = $option['name'];
                $newOption->description = $option['description'];
                $newOption->gender = $option['gender'];
                $newOption->region = $option['region'];
                $newOption->is_abstain = isset($option['is_abstain']) ? $option['is_abstain'] : 0;
                $newOption->save();
            }
        });

        return response()->json(['created' => true]);
    }

    /**
     * Open a vote
     */
    public function open(Vote $vote): JsonResponse
    {
        $vote->openVote();
        return response()->json($vote);
    }

    /**
     * Close a vote
     */
    public function close(Vote $vote): JsonResponse
    {
        $vote->closeVote();
        return response()->json($vote);
    }

    /**
     * Open debate on a vote
     */
    public function openDebate(Vote $vote): JsonResponse
    {
        $vote->openDebate();
        return response()->json($vote);
    }

    /**
     * Close debate on a vote
     */
    public function closeDebate(Vote $vote): JsonResponse
    {
        $vote->closeDebate();
        return response()->json($vote);
    }

    /**
     * Delete a vote
     */
    public function delete(Vote $vote): JsonResponse
    {
        $vote->deleteVote();
        return response()->json(['deleted' => true]);
    }

    /**
     * Get vote list for import
     */
    public function votesToImport(): JsonResponse
    {
        $votes = Vote::with('options')->where('type', 'options')->get();
        return response()->json(['votes' => $votes]);
    }

    /**
     * Get list of delegates
     */
    public function voters(): Response
    {
        $voters = Attendee::delegates()
            ->orWhere('votes', '>', 0)
            ->with(['user' => fn ($query) => $query->with('group')])->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'attendee' => $row->user->fullName(),
                    'organisation' => $row->user->group_other ?? $row->group->name,
                    'votes' => $row->votes,
                    'checked_in' => $row->checked_in
                ];
            })
            ->sortBy('organisation')
            ->values()
            ->all();

        return Inertia::render('Admin/Voters', [
            'voters' => $voters,
        ]);
    }

    /* Reorder vote */
    public function reorder(Vote $vote, Request $request): RedirectResponse
    {
        $move = $request->input('move');

        if ($move === 'up') {
            $previousVote = Vote::where('order', $vote->order - 1)->first();
            if ($previousVote) {
                $previousVote->order = $vote->order;
                $previousVote->save();
            }
            $vote->order = $vote->order - 1;
            $vote->save();
        } elseif ($move === 'down') {
            $nextVote = Vote::where('order', $vote->order + 1)->first();
            if ($nextVote) {
                $nextVote->order = $vote->order;
                $nextVote->save();
            }
            $vote->order = $vote->order + 1;
            $vote->save();
        }

        return to_route('admin_votes');
    }
}
