<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\Message;
use Exception;

class MessageController extends Controller
{
    /**
     * Index
     */
    public function index (Request $request) {
        try {
            //recuperer l'utilisateur
            $userId = $request->user()->id;

            $message = Message::where('sender_id', $userId)
                ->orWhere('receiver_id', $userId)
                ->orderBy('create_at', 'asc')
                ->get();

            //retour de reponse
            return response()->json($message, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Send a message
     */
    public function store (MessageRequest $request) {
        try {
            $request->validated();

            $message = Message::create([
                'sender_id' => $request->user()->id,
                'receiver_id' => $request->receiver_id,
                'content' => $request->content,
            ]);

            //retour de reponse
            return response()->json($message, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
