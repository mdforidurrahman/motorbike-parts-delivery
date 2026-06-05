<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function dashboard()
    {
        return view('marketing.dashboard');
    }
    
    public function promotions()
    {
        return view('marketing.promotions.index');
    }
    
    public function leads()
    {
        return view('marketing.leads.index');
    }
    
    public function campaigns()
    {
        return view('marketing.campaigns.index');
    }
    
    public function analytics()
    {
        return view('marketing.analytics');
    }
}