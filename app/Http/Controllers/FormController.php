<?php

namespace App\Http\Controllers;

use App\Models\AllowedDomain;
use App\Models\Form;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator as ValidationValidator;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $form = Form::query()->get();
        return response()->json([
            'message' => 'Get all forms success',
            'forms' => $form
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'name' => ['required'],
            'slug' => ['required', 'unique:forms,slug', 'regex:/^[a-zA-Z0-9.-]+$/'],
            'allowed_domains' => ['array']
        ]);

        try {
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = Auth::user();
            $requestData['creator_id'] = $user->id;
            unset($requestData["allowed_domains"]);

            $form = Form::create($requestData);
            AllowedDomain::create([
                'form_id' => $form->id,
                'domain' => $request->allowed_domains ?? []
            ]);

            return response()->json([
                'message' => 'Create form success',
                'forms' => $form
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
    public function show(string $slug)
    {
        $form = Form::query()->with("AllowedDomain")->with("Questions")->where("slug", $slug)->first();
        if (!$form) {
            throw new HttpResponseException(response()->json([
                'message' => 'Form not found'
            ]));
        }
        $user = Auth::user();
        $domain = explode('@', $user->email)[1];
        $domainNotNull = $form->AllowedDomain->domain ?? null;

        if (is_array($domainNotNull)) {
            $allowedDomain = in_array($domain, $domainNotNull);
        } else {
            $allowedDomain = ($domain === $domainNotNull);
        }

        if (!$allowedDomain) {
            return response()->json([
                'message' => "Forbidden access"
            ], 403);
        } else {
            return response()->json([
                'message' => 'Get form success',
                'forms' => $form
            ], 200);
        }
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
