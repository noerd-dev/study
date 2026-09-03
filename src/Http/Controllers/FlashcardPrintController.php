<?php

namespace Nywerk\Study\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Nywerk\Study\Models\Flashcard;
use PDF;

class FlashcardPrintController extends Controller
{
    public function print(Request $request)
    {
        $flashcardIds = $request->input('flashcard_ids', []);

        if (empty($flashcardIds)) {
            return back()->withErrors(['selection' => __('Please select at least one flashcard.')]);
        }

        $orderedIds = array_map('intval', $flashcardIds);

        // FIELD() is MySQL-only, so the selection order is restored in PHP to
        // keep the query portable (the test suite runs on SQLite).
        $flashcards = Flashcard::whereIn('id', $orderedIds)
            ->where('tenant_id', Auth::user()->selected_tenant_id)
            ->with(['studyMaterial', 'summary'])
            ->get()
            ->sortBy(fn (Flashcard $flashcard): int => (int) array_search($flashcard->id, $orderedIds, true))
            ->values();

        if ($flashcards->isEmpty()) {
            return back()->withErrors(['selection' => __('Please select at least one flashcard.')]);
        }

        $pdf = PDF::loadView('study::pdf.flashcards', ['flashcards' => $flashcards])
            ->setPaper('a4');

        $filename = Str::uuid() . '.pdf';
        Storage::disk('local')->put($filename, $pdf->output());

        return response()->make(file_get_contents(Storage::disk('local')->path($filename)), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="karteikarten.pdf"',
        ]);
    }
}
