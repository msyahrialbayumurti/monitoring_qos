<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $schools = $this->schoolsFor($request);

        return view('schools.index', compact('schools'));
    }

    public function edit(Request $request, School $school)
    {
        $this->authorizeSchool($request, $school);

        return view('schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $this->authorizeSchool($request, $school);

        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'ip_target' => 'nullable|ip',
            'bandwidth' => 'nullable|numeric|min:1',
            'paket_isp' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $school->update($validated);

        return redirect()
            ->route('schools.index')
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function regenerateAgentToken(Request $request, School $school)
    {
        $this->authorizeSchool($request, $school);

        $school->update([
            'agent_token' => Str::random(48),
            'agent_last_seen_at' => null,
            'agent_gateway' => null,
            'agent_public_ip' => null,
            'agent_isp' => null,
            'agent_version' => null,
        ]);

        return redirect()
            ->route('schools.edit', $school)
            ->with('success', 'Token agent berhasil dibuat ulang. Perbarui token pada agent sekolah.');
    }

    private function schoolsFor(Request $request)
    {
        $schoolId = $request->user()?->school_id;

        return $schoolId
            ? School::whereKey($schoolId)->get()
            : collect();
    }

    private function authorizeSchool(Request $request, School $school): void
    {
        abort_unless(
            $request->user()
                && (int) $request->user()->school_id === (int) $school->id,
            403
        );
    }
}
