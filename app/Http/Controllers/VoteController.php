<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\VoteBallot;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\VoteRequest;

class VoteController extends Controller
{
    /**
     * Vote app
     */
    public function vote(Request $request): Response
    {
        $code = $request->user()->code();

        $vote = Vote::select(
            'votes.id', 'votes.name', 'votes.subtitle', 'votes.majority',
            'votes.secret', 'votes.type', 'votes.open', 'votes.debate',
            'votes.closed_at', 'vote_ballots.voted_at', 'vote_ballots.vote_option_id',
            'vote_ballots.votes', 'vote_options.name as voted_for'
        )
            ->leftJoin('vote_ballots', function ($join) use ($code) {
                $join->on('vote_ballots.vote_id', '=', 'votes.id')
                     ->where('vote_ballots.code_id', '=', $code->id);
            })
            ->leftJoin('vote_options', 'vote_options.id', '=', 'vote_ballots.vote_option_id')
            ->where('open', 1)
            ->orWhere('debate', 1)
            ->with(['options' => fn ($query) => $query->select('id', 'vote_id', 'name', 'description', 'gender', 'is_abstain')])
            ->orderBy('open', 'desc')
            ->first();

        return Inertia::render('Vote', [
            'vote' => $vote
        ]);
    }

    /**
     * Cast a vote
     */
    public function cast(VoteRequest $request): JsonResponse
    {
        $ballot = VoteBallot::where('vote_id', $request->input('vote_id'))->where('code_id', $request->user()->code()->id)->firstOrFail();
        $ballot->vote_option_id = $request->input('option_id');
        $ballot->voted_at = now();
        $ballot->save();

        return response()->json(['submitted' => true]);
    }

    /**
     * List of votes
     */
    public function votes(Request $request): Response
    {
        $code = $request->user()->code();

        $votes = Vote::select('id', 'name', 'subtitle', 'secret', 'type', 'open', 'debate', 'closed_at', 'winner_id')
            ->with([
                'winner',
                'ballots' => function ($query) use ($code) {
                    $query
                        ->select('vote_ballots.id', 'vote_ballots.vote_id', 'vote_options.name', 'vote_ballots.votes')
                        ->join('vote_options', 'vote_ballots.vote_option_id', '=', 'vote_options.id')
                        ->where('code_id', $code->id);
                }
            ])
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return Inertia::render('Votes', [
            'code' => $code->only('votes'),
            'votes' => $votes
        ]);
    }

    /**
     * Results
     */
    public function results(Vote $vote) {
        $vote->load('options');

        if (!$vote->open && $vote->closed_at) {            
            $vote->append('abridgedResults');
        }

        return response()->json($vote);
    }
}
