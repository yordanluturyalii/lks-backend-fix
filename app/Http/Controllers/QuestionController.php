<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use function PHPUnit\Framework\throwException;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'choice_type' => ['required', Rule::in(['short answer', 'paragraph', 'date', 'checkboxes', 'multiple choice', 'dropdown'])],
            'choices' => [Rule::requiredIf(function() use ($request) {
                $selectedChoiceType = ['checkboxes', 'multiple choice', 'dropdown'];
                return in_array($request->choice_type, $selectedChoiceType);
            })]
        ]);

        try {
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            $form = Form::query()->where('slug', $slug)->first();

            $question = new Question();
            $question->name = $request->name;
            $question->choice_type = $request->choice_type;
            $question->choices = $request->choices;
            $question->form_id = $form->id;
            $question->is_required = $request->is_required;
            $question->save();

            return response()->json([
                'message' => 'Add question success',
                'question' => [
                    'name' => $question->name,
                    'choice_type' => $question->choice_type,
                    'is_required' => $question->is_required,
                    'choices' => $question->choices,
                    'form_id' => $question->form_id,
                    'id' => $question->id
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $e->errors()
            ], 422);
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
    public function destroy(string $slug, string $id)
    {
        $form = Form::query()->where('slug', $slug)->first();
        if ($form) {
            $question = Question::query()->where('id', $id)->where('form_id', $form->id)->first();
            if (!$question) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Question not found'
                ]), 404);
            }

        } else if (!$form) {
            throw new HttpResponseException(response()->json([
                'message' => 'Form not found'
            ]), 404);
        }


    }
}
