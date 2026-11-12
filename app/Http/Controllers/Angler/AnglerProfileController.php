<?php

namespace Fishinglog\Http\Controllers\Angler;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Models\Angler;
use Fishinglog\Services\AnglerTelemetryService;

class AnglerProfileController extends Controller
{
    /**
     * Display the public angler profile dossier and career telemetry.
     *
     * @param Angler $angler
     * @param AnglerTelemetryService $telemetryService
     * @return \Illuminate\View\View
     */
    public function show(Angler $angler, AnglerTelemetryService $telemetryService)
    {
        $telemetry = $telemetryService->getAnglerTelemetry($angler);

        return view('angler.profile', $telemetry);
    }
}
