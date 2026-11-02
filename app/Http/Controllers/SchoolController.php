<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::latest()->get();

        return view('schools.index', compact('schools'));
    }

    public function create()
    {
        return view('schools.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'ip_target' => 'required|ip',
            'bandwidth' => 'required|numeric|min:1',
            'paket_isp' => 'nullable|string|max:255',
        ]);

        $validated['status'] = true;

        School::create($validated);

        return redirect()
            ->route('schools.index')
            ->with('success', 'Data sekolah berhasil ditambahkan.');
    }

    public function edit(School $school)
    {
        return view('schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'ip_target' => 'required|ip',
            'bandwidth' => 'required|numeric|min:1',
            'paket_isp' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $school->update($validated);

        return redirect()
            ->route('schools.index')
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function regenerateAgentToken(School $school)
    {
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

    public function destroy(School $school)
    {
        $school->delete();

        return redirect()
            ->route('schools.index')
            ->with('success', 'Data sekolah berhasil dihapus.');
    }
}
