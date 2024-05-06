<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="garden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        {{ config('app.name') }}
    </title>

    <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/js/all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/fontawesome.min.css">

    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css"/>
    <script src="https://cdn.tailwindcss.com"></script>

    @if (app()->isLocal())
        @vite('')
    @endif

</head>
<body class="h-screen">

<div class="container mx-auto h-full">
    <div class="flex flex-col justify-center items-center h-full">

        <h1 class="text-3xl mb-4">
            {{ config('app.name') }}
        </h1>

        <div class="flex flex-col w-1/2 space-y-4 text-slate-600">
            <div>
                1. Prepare word document with template strings that can be put anywhere in the document like this:
                <br>
                <span class="font-mono text-blue-800">
                    @{{name}} @{{surname}} @{{date}}
                </span>
                etc.
            </div>
            <div>
                2. Upload this document.
            </div>
            <div>
                3. Prepare json structure and paste it in the textarea field below.
                If you need table rows you must use <i>"table_rows"</i> array.
                An example is given below.
            </div>
            <div>
                4. If you want to include an image you should use <span class="font-mono text-blue-800">@{{image}}</span> tag.
            </div>
            <div>
                5. Press the generate button. Enjoy!
            </div>
        </div>

        <form action="/run" method="POST" enctype="multipart/form-data" class="space-y-2 mt-4 w-1/2">
            @csrf
            <label class="form-control w-full">
                <div class="label">
                    <span class="label-text">Choose a template</span>
                </div>
                <input type="file" name="template" class="file-input file-input-bordered" accept=".docx"/>
                @error('template')
                <span class="text-red-500">{{ $message }}</span>
                @enderror
            </label>
            <label class="form-control w-full">
                <div class="label">
                    <span class="label-text">Choose an image (optional)</span>
                </div>
                <input type="file" name="image" class="file-input file-input-bordered" accept="image/*"/>
                @error('image')
                <span class="text-red-500">{{ $message }}</span>
                @enderror
            </label>
            <textarea name="values" rows="18" class="textarea textarea-bordered w-full text-xs font-mono">{
    "name": "John",
    "surname": "Doe",
    "table_rows": [
        [
            {"no": 1, "date": "01.01.2000"},
            {"no": 2, "date": "02.01.2000"},
            {"no": 3, "date": "03.01.2000"}
        ],
        [
            {"abc": 10, "price": 500},
            {"abc": 20, "price": 25},
            {"abc": 80, "price": 100}
        ]
    ]
}
            </textarea>

            <button class="btn btn-success w-full">
                <i class="fa-solid fa-file-word"></i>
                Generate DOCX!
            </button>

            <button class="btn btn-secondary w-full" name="convert-to-pdf">
                <i class="fa-solid fa-file-pdf"></i>
                Generate PDF!
            </button>

            <div class="text-center text-gray-500">
                <i>
                    PDF generation takes a little bit longer.
                </i>
            </div>

        </form>
    </div>
</div>

</body>
</html>
