<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompanySelectorController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Ellenőrzés szerepkör alapján
        if ($user->hasRole('superadmin')) {
            $companies = Company::all();
        } else {
            $companies = $user->companies; // kapcsolati metódus alapján
        }

        return Inertia::render('Company/CompanySelector', [
            'companies' => $companies
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        session(['active_company_id' => $request->company_id]);

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
