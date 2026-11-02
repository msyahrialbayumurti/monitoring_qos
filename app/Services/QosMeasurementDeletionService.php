<?php

namespace App\Services;

use App\Models\QosMeasurement;
use Illuminate\Http\Request;

class QosMeasurementDeletionService
{
    public function delete(Request $request): int
    {
        $deleteAll = $request->boolean('delete_all');
        $ids = [];

        if (! $deleteAll) {
            $validated = $request->validate([
                'ids' => ['required', 'array', 'min:1'],
                'ids.*' => ['integer'],
            ], [
                'ids.required' => 'Pilih minimal satu data yang ingin dihapus.',
                'ids.min' => 'Pilih minimal satu data yang ingin dihapus.',
            ]);

            $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        }

        $query = QosMeasurement::query();
        $user = $request->user();

        if ($user?->isSchool()) {
            $query->where('school_id', $user->school_id);
        } elseif ($request->filled('school_id')) {
            $query->where('school_id', $request->integer('school_id'));
        }

        if (! $deleteAll) {
            $query->whereIn('id', $ids);
        }

        return $query->delete();
    }
}
