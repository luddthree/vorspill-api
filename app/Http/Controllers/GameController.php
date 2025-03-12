<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Game API",
 *      description="API documentation for the game app",
 * )
 */
class GameController extends Controller
{
    /**
     * @OA\Post(
     *     path="/questions/create",
     *     summary="Create a new game with questions",
     *     tags={"Game"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"join_code", "questions"},
     *             @OA\Property(property="join_code", type="string", example="ABC123"),
     *             @OA\Property(property="questions", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="text", type="string", example="What's your favorite drink?"),
     *                     @OA\Property(property="category", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Game Created Successfully"),
     *     @OA\Response(response=400, description="Invalid data provided")
     * )
     */
    public function create(Request $request)
    {

        $turnstileToken = $request->input('turnstileToken');

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => env('TURNSTILE_SECRET_KEY'),
            'response' => $turnstileToken,
        ]);
    
        if (!$response->json()['success']) {
            return response()->json(['error' => 'Robot verification failed'], 403);
        }

        $validated = $request->validate([
            'join_code' => 'required|string',
            'questions' => 'required|array',
            'questions.*.text' => 'required|string',
            'questions.*.category' => 'required|integer|in:1,2,3,4'
        ]);

        $game = Game::create(['join_code' => $validated['join_code']]);

        foreach ($validated['questions'] as $question) {
            Question::create([
                'game_id' => $game->id,
                'text' => $question['text'],
                'category' => $question['category'],
            ]);
        }

        return response()->json(['message' => 'Game Created Successfully!'], 201);
    }

    /**
     * @OA\Get(
     *     path="/questions/{joinCode}",
     *     summary="Fetch questions by join code",
     *     tags={"Game"},
     *     @OA\Parameter(
     *         name="joinCode",
     *         in="path",
     *         required=true,
     *         description="Join code to retrieve the game",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="List of questions"),
     *     @OA\Response(response=404, description="Game not found")
     * )
     */
    public function fetch($joinCode)
    {
        $game = Game::where('join_code', $joinCode)->firstOrFail();
        $game->increment('plays');

        $questions = Question::where('game_id', $game->id)->get()->map(function ($question) {
            return [
                'text' => $question->text,
                'category' => $question->category
            ];
        });

        return response()->json($questions);
    }

    /**
     * @OA\Get(
     *     path="/games/most-played",
     *     summary="Fetch the most played games",
     *     tags={"Game"},
     *     @OA\Response(response=200, description="List of most played games")
     * )
     */
    public function mostPlayed()
    {
        $mostPlayedGames = Game::orderBy('plays', 'desc')->take(10)->get();
        return response()->json($mostPlayedGames);
    }
}
