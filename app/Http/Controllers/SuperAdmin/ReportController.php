<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Report::query()->with(['reporter', 'handler']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhereHas('reporter', function ($rq) use ($search) {
                        $rq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && in_array($request->input('status'), ['OPEN', 'IN_REVIEW', 'RESOLVED', 'DISMISSED'], true)) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('target_type') && in_array($request->input('target_type'), ['USER', 'VENUE', 'ROOM', 'OTHER'], true)) {
            $query->where('target_type', $request->input('target_type'));
        }

        $reports = $query->orderByRaw("FIELD(status, 'OPEN', 'IN_REVIEW', 'RESOLVED', 'DISMISSED')")
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => Report::count(),
            'open' => Report::where('status', 'OPEN')->count(),
            'in_review' => Report::where('status', 'IN_REVIEW')->count(),
            'resolved' => Report::where('status', 'RESOLVED')->count(),
            'dismissed' => Report::where('status', 'DISMISSED')->count(),
        ];

        return view('superadmin.reports.index', [
            'reports' => $reports,
            'summary' => $summary,
        ]);
    }

    public function updateStatus(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['OPEN', 'IN_REVIEW', 'RESOLVED', 'DISMISSED'])],
            'resolution_note' => ['nullable', 'string', 'max:500'],
            'suspend_target_user' => ['nullable', 'boolean'],
        ]);

        $report->updateReportStatus($validated['status'], $request->user()->id);

        $note = trim((string) ($validated['resolution_note'] ?? ''));

        if ($request->boolean('suspend_target_user') && $report->target_type === 'USER' && $report->target_id) {
            $targetUser = User::find($report->target_id);
            if ($targetUser && $targetUser->id !== $request->user()->id) {
                $targetUser->update(['is_active' => false]);
                ActivityLog::recordLog(
                    $request->user()->id,
                    'USER_SUSPEND_FROM_REPORT',
                    "Men-suspend user {$targetUser->name} (ID: {$targetUser->id}) dari tindak lanjut Laporan #{$report->id}.",
                    $request->ip()
                );
            }
        }

        ActivityLog::recordLog(
            $request->user()->id,
            'REPORT_HANDLE',
            "Memperbarui status Laporan #{$report->id} ({$report->target_type}) menjadi {$report->status}." . ($note !== '' ? " Catatan: {$note}" : ''),
            $request->ip()
        );

        if ($report->reporter_id) {
            $message = "Status laporan Anda (#{$report->id} - {$report->target_type}) telah diperbarui menjadi {$report->status} oleh Super Admin.";
            if ($note !== '') {
                $message .= " Tanggapan Admin: {$note}";
            }

            Notification::sendNotification(
                $report->reporter_id,
                'SYSTEM',
                "Update Penanganan Laporan #{$report->id}",
                $message,
                'REPORT',
                $report->id
            );
        }

        return redirect()
            ->back()
            ->with('success', "Status Laporan #{$report->id} berhasil diperbarui menjadi {$report->status}.");
    }

    public function destroy(Request $request, Report $report): RedirectResponse
    {
        $id = $report->id;
        $report->delete();

        ActivityLog::recordLog(
            $request->user()->id,
            'REPORT_DELETE',
            "Menghapus Laporan #{$id}.",
            $request->ip()
        );

        return redirect()
            ->route('superadmin.reports.index')
            ->with('success', "Laporan #{$id} berhasil dihapus.");
    }
}
