<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $dashboardInfo = Cache::get('dashboard_info', 'Selamat datang di Project Dashboard!');

        $query = Project::with('subSystems.sites');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('subSystems', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('subSystems.sites', function($ssq) use ($search) {
                      $ssq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $sortColumn = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        if (!in_array($sortColumn, ['name', 'start_date', 'end_date'])) {
            $sortColumn = 'name';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortColumn, $sortDirection);

        $projects = $query->get();

        return view('dashboard', compact('dashboardInfo', 'projects', 'sortColumn', 'sortDirection'));
    }

    public function updateInfo(Request $request)
    {
        $this->authorize('updateDashboardInfo', Auth::user());

        $request->validate([
            'info' => 'required|string',
        ]);

        Cache::put('dashboard_info', $request->info, now()->addDays(7));

        return redirect()->back()->with('success', 'Informasi dashboard berhasil diperbarui.');
    }
}