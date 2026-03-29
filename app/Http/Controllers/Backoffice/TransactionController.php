<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Office;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json(
            Transaction::query()
                ->leftJoin('offices as origin_office', 'transactions.origin_office_id', '=', 'origin_office.id')
                ->leftJoin('offices as destination_office', 'transactions.destination_office_id', '=', 'destination_office.id')
                ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
                ->select([
                    'transactions.*',
                    'origin_office.code as origin_office_code',
                    'origin_office.name as origin_office_name',
                    'destination_office.code as destination_office_code',
                    'destination_office.name as destination_office_name',
                    'categories.name as category_name',
                ])
                ->latest('transactions.created_at')
                ->paginate(15)
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin_office_id' => ['required', 'integer', 'exists:offices,id'],
            'destination_office_id' => ['required', 'integer', 'exists:offices,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'value' => ['nullable', 'numeric'],
            'price' => ['nullable', 'numeric'],
            'adjustment' => ['nullable', 'numeric'],
            'price_id' => ['nullable', 'integer'],
            'final_price' => ['nullable', 'numeric'],
            'status' => ['nullable', 'integer', 'min:0', 'max:32767'],
        ]);

        if (isset($validated['category_id'])) {
            $category = Category::query()->find($validated['category_id']);
            if ($category && $category->section !== 'transaction') {
                return response()->json(['message' => 'Invalid category section.'], 422);
            }
        }

        $user = $request->attributes->get('auth_user');
        $creatorId = $user?->id;

        $transaction = DB::transaction(function () use ($validated, $creatorId) {
            $number = $this->generateNumber($validated['origin_office_id'], $validated['destination_office_id']);

            return Transaction::create([
                'number' => $number,
                'origin_office_id' => $validated['origin_office_id'],
                'destination_office_id' => $validated['destination_office_id'],
                'category_id' => $validated['category_id'] ?? null,
                'name' => $validated['name'],
                'note' => $validated['note'] ?? null,
                'value' => $validated['value'] ?? 0,
                'price' => $validated['price'] ?? 0,
                'adjustment' => $validated['adjustment'] ?? 0,
                'price_id' => $validated['price_id'] ?? null,
                'final_price' => $validated['final_price'] ?? 0,
                'creator_id' => $creatorId,
                'status' => $validated['status'] ?? 0,
            ]);
        });

        return response()->json($transaction, 201);
    }

    public function show(Transaction $transaction)
    {
        return response()->json($transaction);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'origin_office_id' => ['sometimes', 'integer', 'exists:offices,id'],
            'destination_office_id' => ['sometimes', 'integer', 'exists:offices,id'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'note' => ['sometimes', 'nullable', 'string'],
            'value' => ['sometimes', 'nullable', 'numeric'],
            'price' => ['sometimes', 'nullable', 'numeric'],
            'adjustment' => ['sometimes', 'nullable', 'numeric'],
            'price_id' => ['sometimes', 'nullable', 'integer'],
            'final_price' => ['sometimes', 'nullable', 'numeric'],
            'status' => ['sometimes', 'integer', 'min:0', 'max:32767'],
        ]);

        if (array_key_exists('category_id', $validated) && $validated['category_id']) {
            $category = Category::query()->find($validated['category_id']);
            if ($category && $category->section !== 'transaction') {
                return response()->json(['message' => 'Invalid category section.'], 422);
            }
        }

        $transaction->update($validated);

        return response()->json($transaction);
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return response()->json(['deleted' => true]);
    }

    private function generateNumber(int $originOfficeId, int $destinationOfficeId): string
    {
        $origin = Office::query()->findOrFail($originOfficeId);
        $destination = Office::query()->findOrFail($destinationOfficeId);

        $originCode = preg_replace('/\s+/', '', (string) $origin->code);
        $destinationCode = preg_replace('/\s+/', '', (string) $destination->code);

        $yy = date('y');
        $mm = date('m');
        $prefix = strtoupper($originCode . $destinationCode . $yy . $mm);

        $last = Transaction::query()
            ->where('number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('number')
            ->first();

        $nextOrder = 1;
        if ($last && is_string($last->number) && str_starts_with($last->number, $prefix)) {
            $suffix = substr($last->number, strlen($prefix));
            $suffix = preg_replace('/\D+/', '', (string) $suffix);
            if ($suffix !== '' && is_numeric($suffix)) {
                $nextOrder = (int) $suffix + 1;
            }
        }

        return $prefix . str_pad((string) $nextOrder, 4, '0', STR_PAD_LEFT);
    }
}

