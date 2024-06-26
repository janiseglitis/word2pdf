<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDocument;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConvertController extends Controller
{
    public const TABLE_ROWS = 'table_rows';

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     * @throws \Throwable
     */
    public function run(Request $request): JsonResponse|StreamedResponse
    {
        $rules = [
            // application/vnd.openxmlformats-officedocument.wordprocessingml.document
            'template' => 'required|file|extensions:docx',
        ];

        if (str($request->url())->contains('api')) {
            try {
                $request->validate($rules);
            } catch (ValidationException $e) {
                return response()->json(['errors' => $e->errors()], 422);
            }
        } else {
            $request->validate($rules);
        }

        info('MIME:', [$request->file('template')->getMimeType()]);

        $file = $request->file('template')->move(storage_path('app/temp'));
        $tp = new TemplateProcessor($file->getPathname());
        $tp->setMacroOpeningChars((string)$request->get('placeholder_start', ''));
        $tp->setMacroClosingChars((string)$request->get('placeholder_end', ''));

        $imageFile = null;
        if ($request->hasFile('image')) {
            info('hasImage');
            $imageFile = $request->file('image')->move(storage_path('app/temp'));
        }

        $substitutesArray = [];
        $tableRows = null;

        if ($request->has('values')) {
            info(request('values'));
            if ($substitutesArray = json_decode(trim(request('values')), true)) {
                $tableRows = $substitutesArray[self::TABLE_ROWS] ?? null;
                unset($substitutesArray[self::TABLE_ROWS]);
                info('substitutesArray', $substitutesArray);

                // taking care of nested arrays
                foreach ($substitutesArray as $key => $item) {
                    if (is_array($item)) {
                        $substitutesArray[$key] = collect($item)->flatten()->implode(',');
                    }
                }

                $tp->setValues($substitutesArray);

                if ($imageFile) {
                    info('image pathname:', [$imageFile->getPathname()]);
                    $tp->setImageValue('image', [
                        'path' => $imageFile->getPathname(),
                        'width' => $request->get('image_width', 600),
                        'height' => $request->get('image_height', 400),
                    ]);
                } else {
                    info('NO image found');
                }

                // special json key "rows"
                if ($tableRows) {
                    info('tableRows', $tableRows);
                    foreach ($tableRows as $tableRow) {
                        info('$tableRow', $tableRow);
                        info('table key:', [array_key_first($tableRow[0])]);
                        try {
                            $tp->cloneRowAndSetValues(array_key_first($tableRow[0]), $tableRow);
                        } catch (Exception $exception) {
                            Log::error($exception->getMessage());
                        }
                    }
                } else {
                    info('NO tableRows found');
                }
            } else {
                Log::error('Wrong json!');
                abort(403, 'Wrong json!');
            }
        }

        $filename = Str::random(32) . '.docx';
        $path = storage_path('app/' . $filename);
        $tp->saveAs($path);
        info('Save as:', [$path]);

        $shouldWait = false;

        if (request('return') == 'pdf' || $request->has('convert-to-pdf')) {
            ProcessDocument::dispatch($filename);
            $shouldWait = true;
        }

        if ($shouldWait) {
            $filename = str_replace('.docx', '.pdf', $filename);
            retry(100, function () use ($filename) {
                if (Storage::exists($filename)) {
                    return Storage::download($filename);
                } else {
                    Log::error("File $filename not found");
                    throw new FileNotFoundException;
                }
            }, 300);
        }

        return Storage::download($filename);
    }
}
