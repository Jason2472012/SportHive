<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Sport;
use App\Models\SportSkillLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sport::query()
            ->with(['skillLevels'])
            ->withCount(['courts', 'userSportProfiles']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $sports = $query->orderBy('name')->get();

        $summary = [
            'total_sports' => Sport::count(),
            'active_sports' => Sport::where('is_active', true)->count(),
            'inactive_sports' => Sport::where('is_active', false)->count(),
            'total_skill_levels' => SportSkillLevel::count(),
        ];

        return view('superadmin.sports.index', [
            'sports' => $sports,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('sports', 'name')],
            'is_active' => ['nullable', 'boolean'],
            'create_default_skills' => ['nullable', 'boolean'],
        ]);

        $sport = Sport::create([
            'name' => trim($validated['name']),
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
        ]);

        if ($request->boolean('create_default_skills', true)) {
            $defaults = [
                ['name' => 'Beginner', 'level_order' => 1],
                ['name' => 'Intermediate', 'level_order' => 2],
                ['name' => 'Advanced', 'level_order' => 3],
            ];

            foreach ($defaults as $level) {
                SportSkillLevel::firstOrCreate(
                    ['sport_id' => $sport->id, 'name' => $level['name']],
                    ['level_order' => $level['level_order']]
                );
            }
        }

        ActivityLog::recordLog(
            $request->user()->id,
            'SPORT_CREATE',
            "Menambahkan master cabang olahraga: {$sport->name}.",
            $request->ip()
        );

        return redirect()
            ->route('superadmin.sports.index')
            ->with('success', "Cabang olahraga {$sport->name} berhasil ditambahkan.");
    }

    public function update(Request $request, Sport $sport): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('sports', 'name')->ignore($sport->id)],
            'is_active' => ['required', 'boolean'],
        ]);

        $sport->update([
            'name' => trim($validated['name']),
            'is_active' => (bool) $validated['is_active'],
        ]);

        ActivityLog::recordLog(
            $request->user()->id,
            'SPORT_UPDATE',
            "Memperbarui cabang olahraga: {$sport->name} (Aktif: ".($sport->is_active ? 'Ya' : 'Tidak').').',
            $request->ip()
        );

        return redirect()
            ->route('superadmin.sports.index')
            ->with('success', "Cabang olahraga {$sport->name} berhasil diperbarui.");
    }

    public function toggleStatus(Request $request, Sport $sport): RedirectResponse
    {
        $sport->setActiveStatus(! $sport->is_active);

        $statusLabel = $sport->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::recordLog(
            $request->user()->id,
            'SPORT_TOGGLE',
            "Status cabang olahraga {$sport->name} {$statusLabel}.",
            $request->ip()
        );

        return redirect()
            ->route('superadmin.sports.index')
            ->with('success', "Cabang olahraga {$sport->name} berhasil {$statusLabel}.");
    }

    public function destroy(Request $request, Sport $sport): RedirectResponse
    {
        if ($sport->courts()->exists()) {
            return redirect()
                ->route('superadmin.sports.index')
                ->with('error', "Cabang olahraga {$sport->name} tidak dapat dihapus karena sedang digunakan oleh data lapangan (courts). Nonaktifkan saja jika tidak ingin ditampilkan.");
        }

        $name = $sport->name;
        $sport->delete();

        ActivityLog::recordLog(
            $request->user()->id,
            'SPORT_DELETE',
            "Menghapus cabang olahraga: {$name}.",
            $request->ip()
        );

        return redirect()
            ->route('superadmin.sports.index')
            ->with('success', "Cabang olahraga {$name} berhasil dihapus.");
    }

    public function storeSkillLevel(Request $request, Sport $sport): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sport_skill_levels', 'name')->where('sport_id', $sport->id),
            ],
            'level_order' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $skill = SportSkillLevel::create([
            'sport_id' => $sport->id,
            'name' => trim($validated['name']),
            'level_order' => (int) $validated['level_order'],
        ]);

        ActivityLog::recordLog(
            $request->user()->id,
            'SKILL_LEVEL_CREATE',
            "Menambahkan skill level '{$skill->name}' (Urutan {$skill->level_order}) pada olahraga {$sport->name}.",
            $request->ip()
        );

        return redirect()
            ->route('superadmin.sports.index')
            ->with('success', "Skill level '{$skill->name}' untuk {$sport->name} berhasil ditambahkan.");
    }

    public function updateSkillLevel(Request $request, SportSkillLevel $skillLevel): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sport_skill_levels', 'name')
                    ->where('sport_id', $skillLevel->sport_id)
                    ->ignore($skillLevel->id),
            ],
            'level_order' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $skillLevel->update([
            'name' => trim($validated['name']),
            'level_order' => (int) $validated['level_order'],
        ]);

        ActivityLog::recordLog(
            $request->user()->id,
            'SKILL_LEVEL_UPDATE',
            "Memperbarui skill level '{$skillLevel->name}' (Urutan {$skillLevel->level_order}).",
            $request->ip()
        );

        return redirect()
            ->route('superadmin.sports.index')
            ->with('success', "Skill level '{$skillLevel->name}' berhasil diperbarui.");
    }

    public function destroySkillLevel(Request $request, SportSkillLevel $skillLevel): RedirectResponse
    {
        $name = $skillLevel->name;
        $sportName = $skillLevel->sport?->name ?? '-';
        $skillLevel->delete();

        ActivityLog::recordLog(
            $request->user()->id,
            'SKILL_LEVEL_DELETE',
            "Menghapus skill level '{$name}' dari olahraga {$sportName}.",
            $request->ip()
        );

        return redirect()
            ->route('superadmin.sports.index')
            ->with('success', "Skill level '{$name}' berhasil dihapus.");
    }
}
