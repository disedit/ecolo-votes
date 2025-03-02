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
            'votes.id', 'votes.name', 'votes.subtitle', 'votes.majority', 'votes.secret',
            'votes.max_votes', 'votes.type', 'votes.open', 'votes.debate',
            'votes.closed_at', 'vote_ballots.voted_at', 'vote_ballots.vote_option_ids',
            'vote_ballots.votes',
        )
            ->leftJoin('vote_ballots', function ($join) use ($code) {
                $join->on('vote_ballots.vote_id', '=', 'votes.id')
                     ->where('vote_ballots.code_id', '=', $code->id);
            })
            ->where('open', 1)
            ->orWhere('debate', 1)
            ->with(['options' => fn ($query) => $query->select('id', 'vote_id', 'name', 'description', 'gender', 'is_abstain', 'is_no')])
            ->orderBy('open', 'desc')
            ->first();

        if ($vote && $vote->vote_option_ids) {
            $vote->vote_option_ids = json_decode($vote->vote_option_ids);
            $vote->voted_for = collect($vote->vote_option_ids)->map(function ($id) use ($vote) {
                return $vote->options->filter(fn ($option) => $option->id === $id)->first();
            });
        }

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
        $ballot->vote_option_ids = $request->input('option_ids');
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
            ->with('winner')
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
    public function results(Vote $vote, Request $request) {
        $vote->load('options');

        if (!$vote->open && $vote->closed_at) {            
            $vote->append('results');
            $votedFor = $vote->ballots()->where('code_id', $request->user()->code()->id)->first()->vote_option_ids;
            $vote->voted_for = $vote->options->filter(fn ($option) => in_array($option->id, $votedFor));
        }

        return response()->json($vote);
    }
}
