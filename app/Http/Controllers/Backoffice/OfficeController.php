<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfficeController extends Controller
{
    public function index()
    {
        return response()->json(Office::query()->latest()->paginate(15));
    }

    public function officeList()
    {
        return response()->json(
            Office::query()
                ->where('status', true)
                ->orderBy('name')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:offices,code'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'image' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'province_id' => ['nullable', 'integer'],
            'city' => ['nullable', 'string', 'max:255'],
        ]);

        $office = Office::create($validated);

        return response()->json($office, 201);
    }

    public function show(Office $office)
    {
        return response()->json($office);
    }

    public function update(Request $request, Office $office)
    {
        $validated = $request->validate([
            'code' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('offices', 'code')->ignore($office->id),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'latitude' => ['sometimes', 'nullable', 'numeric'],
            'longitude' => ['sometimes', 'nullable', 'numeric'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'email' => ['sometimes', 'nullable', 'string', 'email', 'max:255'],
            'province_id' => ['sometimes', 'nullable', 'integer'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $office->update($validated);

        return response()->json($office);
    }

    public function destroy(Office $office)
    {
        $office->delete();

        return response()->json(['deleted' => true]);
    }
}
