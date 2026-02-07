<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
            size: A4;
        }

        @font-face {
            font-family: 'Nunito Sans';
            font-style: normal;
            font-weight: 300;
            src: url('{{ public_path('vendor/noerd/fonts/NunitoSans-Light.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'Nunito Sans';
            font-style: normal;
            font-weight: 400;
            src: url('{{ public_path('vendor/noerd/fonts/NunitoSans-Regular.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'Nunito Sans';
            font-style: normal;
            font-weight: 600;
            src: url('{{ public_path('vendor/noerd/fonts/NunitoSans-SemiBold.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'Nunito Sans';
            font-style: normal;
            font-weight: 700;
            src: url('{{ public_path('vendor/noerd/fonts/NunitoSans-Bold.ttf') }}') format('truetype');
        }

        body {
            font-weight: normal;
            line-height: 1.4;
            font-size: 10px;
            font-family: 'Nunito Sans', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Table-based layout for DomPDF compatibility */
        .card-table {
            width: 210mm;
            table-layout: fixed;
            border-collapse: collapse;
            border-spacing: 0;
            margin: 0;
            padding: 0;
        }

        /* A7 format: 105mm x 70mm to fit 4 rows on A4 */
        /* 4 rows: 4 x 70mm = 280mm (safe within 297mm) */
        .card {
            width: 105mm;
            height:65.75mm;
            border: 1px dashed #999;
            padding: 4mm;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            box-sizing: border-box;
        }

        .card-question {
            width: 105mm;
        }

        .card-answer {
            width: 105mm;
        }

        .card-header {
            margin-bottom: 2mm;
        }

        .card-number {
            float: right;
            font-size: 8px;
            color: #999;
        }

        .card-label {
            font-size: 8px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-content {
            font-size: 10px;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .card-content p {
            margin: 0 0 4px 0;
        }

        .card-content ul,
        .card-content ol {
            margin: 4px 0;
            padding-left: 14px;
        }

        .card-content li {
            margin-bottom: 2px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
    <title>{{ __('study_label_flashcards') }}</title>
</head>

<body>
    @php
        // Ensure we always have multiples of 4 cards for proper A7 layout
        // Each page has exactly 4 rows (8 A7 areas: 4 questions + 4 answers)
        $paddedFlashcards = $flashcards->all(); // Keep as Eloquent models, not arrays
        $remainder = count($paddedFlashcards) % 4;
        if ($remainder > 0) {
            $paddingNeeded = 4 - $remainder;
            for ($i = 0; $i < $paddingNeeded; $i++) {
                $paddedFlashcards[] = null; // Add empty placeholders
            }
        }
        $paddedFlashcards = collect($paddedFlashcards);
    @endphp

    @foreach($paddedFlashcards->chunk(4) as $chunkIndex => $chunkCards)
        <table class="card-table">
            <colgroup>
                <col style="width: 105mm;">
                <col style="width: 105mm;">
            </colgroup>
            @foreach($chunkCards as $cardIndex => $flashcard)
                <tr>
                    {{-- Question (left cell) --}}
                    <td class="card card-question">
                        @if($flashcard)
                            <div class="card-header">
                                <span class="card-number">
                                    {{ $flashcard->studyMaterial?->title ?? '-' }}@if($flashcard->summary) | {{ $flashcard->summary->title }}@endif
                                </span>
                                <span class="card-label">{{ __('study_label_question') }}</span>
                            </div>
                            <div class="card-content">
                                {{ $flashcard->question }}
                            </div>
                        @endif
                    </td>
                    {{-- Answer (right cell) --}}
                    <td class="card card-answer">
                        @if($flashcard)
                            <div class="card-header">
                                <span class="card-number">
                                    {{ $flashcard->studyMaterial?->title ?? '-' }}@if($flashcard->summary) | {{ $flashcard->summary->title }}@endif
                                </span>
                                <span class="card-label">{{ __('study_label_answer') }}</span>
                            </div>
                            <div class="card-content">
                                @if($flashcard->answer)
                                    {!! \Illuminate\Support\Str::markdown($flashcard->answer) !!}
                                @else
                                    <em>-</em>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
