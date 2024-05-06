<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
     */
    public function run(Request $request): JsonResponse|StreamedResponse
    {
        $filename = 'result.docx';

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
        $tp->setMacroOpeningChars('{{');
        $tp->setMacroClosingChars('}}');

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
                info($substitutesArray);
                $tableRows = $substitutesArray[self::TABLE_ROWS] ?? null;
                unset($substitutesArray[self::TABLE_ROWS]);
                $tp->setValues($substitutesArray);

                if ($imageFile) {
                    info('image pathname:', [$imageFile->getPathname()]);
                    $tp->setImageValue('image', ['path' => $imageFile->getPathname(), 'width' => 600, 'height' => 400]);
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
                }
            } else {
                Log::error('Wrong json!');
                abort(403, 'Wrong json!');
            }
        }

        $path = storage_path('app/' . $filename);
        $tp->saveAs($path);

        if (request('return') == 'pdf' || $request->has('convert-to-pdf')) {
            $command = "lowriter --convert-to pdf $path --outdir " . storage_path('app');
            exec($command, $output, $result);
            info($command, [$output, $result]);
            $filename = str_replace('.docx', '.pdf', $filename);
        }

        return Storage::download($filename);
    }
}
