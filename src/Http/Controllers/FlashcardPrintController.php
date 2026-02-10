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
            return back()->withErrors(['selection' => __('study_label_no_flashcards_selected')]);
        }

        $flashcards = Flashcard::whereIn('id', $flashcardIds)
            ->where('tenant_id', Auth::user()->selected_tenant_id)
            ->with(['studyMaterial', 'summary'])
            ->orderByRaw('FIELD(id, ' . implode(',', array_map('intval', $flashcardIds)) . ')')
            ->get();

        if ($flashcards->isEmpty()) {
            return back()->withErrors(['selection' => __('study_label_no_flashcards_selected')]);
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
