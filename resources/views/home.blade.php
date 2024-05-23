<x-layout>

    <h1 class="text-3xl mb-4">
        <a href="/" class="link">
            {{ config('app.name') }}
        </a>
    </h1>

    <div class="py-4">
        <a href="/docs" class="link">
            API docs
        </a>
    </div>

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
            If you need to replace a content in tables you must prepare them in <i>"table_rows"</i> array.
            An example is given below.
        </div>
        <div>
            5. Choose your opening and closing template placeholders. Default: @{{  }}. They can be empty as well.
        </div>
        <div>
            6. <i>Optional.</i> If you have an image replacement in your document you should use <span class="font-mono text-blue-800">@{{image}}</span> tag.
        </div>
        <div>
            7. Press the generate button. Enjoy!
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

        <label class="input input-bordered flex items-center gap-2">
            Placeholder start
            <input type="text" name="placeholder_start" class="grow" value="@{{"/>
        </label>
        <label class="input input-bordered flex items-center gap-2">
            Placeholder end
            <input type="text" name="placeholder_end" class="grow" value="}}"/>
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
        <label class="input input-bordered flex items-center gap-2">
            Image width
            <input type="text" name="image_width" class="grow" placeholder="600"/>
        </label>
        <label class="input input-bordered flex items-center gap-2">
            Image height
            <input type="text" name="image_height" class="grow" placeholder="400"/>
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
</x-layout>
