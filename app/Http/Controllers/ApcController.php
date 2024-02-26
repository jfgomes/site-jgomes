<?php

namespace App\Http\Controllers;
class ApcController extends Controller
{
    public function index()
    {
        // Get the values that came from apcu request
        $scope = request('SCOPE', 'A');
        $sort1 = request('SORT1', 'H');
        $sort2 = request('SORT2', 'D');
        $count = request('COUNT', 20);
        $ob    = request('OB', 1);

        // Inject the values to view
        return view('apc.index', [
            'SCOPE' => $scope,
            'SORT1' => $sort1,
            'SORT2' => $sort2,
            'COUNT' => $count,
            'OB'    => $ob
        ]);
    }
}
