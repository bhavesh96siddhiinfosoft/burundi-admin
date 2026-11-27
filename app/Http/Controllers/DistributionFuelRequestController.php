<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Distribution Fuel Request (F7): one tab for the owner's fuel request and the distribution it published.
 * It replaces the Fuel Request and Distributions tabs, whose old URLs redirect here.
 */
class DistributionFuelRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('distribution_fuel_request.index');
    }

    /**
     * $id is a fuel request id, or the id of a distribution (the page opens its request when it has one).
     */
    public function view(Request $request, $id)
    {
        // Admin names for "Updated by" and "Added by" (the panel writes admin ids)
        $adminNames = (object) User::pluck('name', 'id')->all();
        $step = $request->query('step') === 'receive' ? 'receive' : '';

        return view('distribution_fuel_request.view', compact('id', 'adminNames', 'step'));
    }

    // Old URLs: /fuel-request and /distributions
    public function redirectToList()
    {
        return redirect()->route('distribution.fuel.request');
    }

    // Old URLs: /fuel-request/save/{id} and /distributions/view/{id}
    public function redirectToFlow($id = null)
    {
        if (!$id) {
            return redirect()->route('distribution.fuel.request');
        }
        return redirect()->route('distribution.fuel.request.view', $id);
    }

    // Old URL: /fuel-request/receive/{id} opens the detail page at the Add received fuel form
    public function redirectToReceive($id)
    {
        return redirect()->route('distribution.fuel.request.view', ['id' => $id, 'step' => 'receive']);
    }
}
