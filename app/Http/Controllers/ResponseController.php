<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Form;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $slug)
    {
        $user = Auth::user();
        $form = Form::query()->where('slug', $slug)->first();
        $responses = DB::table('responses')
            ->join('forms', 'forms.id' ,'=', 'responses.form_id')
            ->join('users', 'users.id', '=', 'responses.user_id')
            ->join('answers', 'answers.response_id', '=', 'responses.id')
            ->get();
        
        if (!$form) {
            return response()->json([
                'message' => 'Form not found'
            ]);
        }

        if ($form->creator_id != $user->id) {
            return response()->json([
                'message' => 'Forbidden access'
            ]);
        }

    $output = [];
    foreach ($responses as $response) {
        $output[] = [
            'date' => $response->date,
            'user' => [
                'id' => $response->user_id,
                'name' => $response->name,
                'email' => $response->email,
                'email_verified_at' => $response->email_verified_at
            ],
            'answers' => [
                'name' => $response->value
            ]
        ];
    }

    return response()->json([
        'message' => 'Get responses success',
        'responses' => $output
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug)
    {
        $validator = Validator::make($request->all(), [
            'answers' => ['required', 'array'],
            'answers.*.value' => [function($att, $val, $fail) {

            }]
        ]);

        try {
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            $user = Auth::user();
            $form = Form::query()->with('AllowedDomain')->where('slug', $slug)->first();
            $userDomain = explode('@', $user->email)[1];
            $formDomain = $form->AllowedDomain->domain ?? null;
            if (!in_array($userDomain, $formDomain)) {
                return response()->json([
                    'message' => 'Forbidden access'
                ]);
            }
            
            $checkResponse = Response::query()->where('form_id', $form->id)->first()->form_id ?? null;
            if ($form->limit_one_response == true && $checkResponse == $user->id) {
                return response()->json([
                    'message' => 'You can not submit form twice'
                ]);
            }

            $response = Response::create([
                'form_id' => $form->id,
                'user_id' => $user->id,
                'date' => now()
            ]);

            $answers = $request->all();
            foreach ($answers as $answer) {
                foreach ($answer as $a) {
                    $a['response_id'] = $response->id;
                    Answer::create($a);
                }
            }

            return response()->json([
                'message' => 'Submit form success'
            ]); 

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $e->errors()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
