<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Http\Request;

class GameController extends Controller
{
    // Store questions with category
    public function create(Request $request)
    {
        $validated = $request->validate([
            'join_code' => 'required|string',
            'questions' => 'required|array',
            'questions.*.text' => 'required|string',
            'questions.*.category' => 'required|integer|in:1,2,3,4' // Simplified to 1, 2, 3,4
        ]);
    
        // Create a new Game record
        $game = Game::create([
            'join_code' => $validated['join_code'],
        ]);
    
        foreach ($validated['questions'] as $question) {
            Question::create([
                'game_id' => $game->id,
                'text' => $question['text'],
                'category' => $question['category'], // Directly use the category number
            ]);
        }
    
        return response()->json(['message' => 'Game Created Successfully!'], 201);
    }
    

    public function fetch($joinCode)
    {
        // Find the game by join code
        $game = Game::where('join_code', $joinCode)->firstOrFail();

        // Increment the plays count
        $game->increment('plays');

        // Fetch the questions for the game
        $questions = Question::where('game_id', $game->id)->get()->map(function ($question) {
            return [
                'text' => $question->text,
                'category' => $question->category // Directly return the category number
            ];
        });

        return response()->json($questions);
    }

    // Fetch the most played games
    public function mostPlayed()
    {
        $mostPlayedGames = Game::orderBy('plays', 'desc')->take(10)->get(); // Get top 10 most played games
        return response()->json($mostPlayedGames);
    }
}