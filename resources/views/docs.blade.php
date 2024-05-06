<x-layout>

    <h1 class="text-3xl mb-4">
        <a href="/" class="link">
            {{ config('app.name') }}
        </a>
    </h1>

    <h2 class="text-2xl my-5 text-center">
        API examples
    </h2>

    <div role="tablist" class="tabs tabs-bordered pb-10">
        <input type="radio" name="my_tabs_1" role="tab" class="tab" aria-label="curl" checked/>
        <div role="tabpanel" class="tab-content py-4 overflow-auto max-w-screen-lg">
            <div class="mockup-code">
                <pre>
                    <code>
    curl --request POST
    --url {{ url()->current() }}/api/convert \
    --header 'Content-Type: multipart/form-data' \
    --form name=John \
    --form 'template=@path/to/template.docx' \
    --form 'table_rows=[{"no": 2, "date": "01.01.2005"},{"no": 2, "date": "01.01.2010"},{"no": 2, "date": "01.01.2008"},{"no": 2, "date": "01.01.2010"}]' \
    --form return=pdf</code>
                </pre>
            </div>
        </div>

        <input type="radio" name="my_tabs_1" role="tab" class="tab" aria-label="wget"/>
        <div role="tabpanel" class="tab-content py-4 overflow-auto max-w-screen-lg">
            <div class="mockup-code">
                <pre>
                    <code>
    wget --quiet \
    --method POST \
    --header 'Content-Type: multipart/form-data; boundary=---011000010111000001101001' \
    --body-data '-----011000010111000001101001\r\nContent-Disposition: form-data; name="name"\r\n\r\nJohn\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name="template"\r\n\r\n\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name="table_rows"\r\n\r\n[{"no": 2, "date": "01.01.2005"},{"no": 2, "date": "01.01.2010"},{"no": 2, "date": "01.01.2008"},{"no": 2, "date": "01.01.2010"}]\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name="return"\r\n\r\npdf\r\n-----011000010111000001101001--\r\n\r\n' \
    --output-document \
    - {{ url()->current() }}/api/convert</code>
                </pre>
            </div>
        </div>

        <input type="radio" name="my_tabs_1" role="tab" class="tab" aria-label="Javascript/jQuery"/>
        <div role="tabpanel" class="tab-content py-4 overflow-auto max-w-screen-lg">
            <div class="mockup-code">
                <pre>
                    <code>
    const form = new FormData();
    form.append("name", "John");
    form.append("template", "path/to/template.docx");
    form.append("table_rows", "[{\"no\": 2, \"date\": \"01.01.2005\"},{\"no\": 2, \"date\": \"01.01.2010\"},{\"no\": 2, \"date\": \"01.01.2008\"},{\"no\": 2, \"date\": \"01.01.2010\"}]");
    form.append("return", "pdf");

    const settings = {
        "async": true,
        "crossDomain": true,
        "url": "{{ url()->current() }}/api/convert",
        "method": "POST",
        "headers": {},
        "processData": false,
        "contentType": false,
        "mimeType": "multipart/form-data",
        "data": form
    };

    $.ajax(settings).done(function (response) {
        console.log(response);
    });</code>
                </pre>
            </div>
        </div>

        <input type="radio" name="my_tabs_1" role="tab" class="tab" aria-label="Java"/>
        <div role="tabpanel" class="tab-content py-4 overflow-auto max-w-screen-lg">
            <div class="mockup-code">
                <pre>
                    <code>
    OkHttpClient client = new OkHttpClient();

    MediaType mediaType = MediaType.parse("multipart/form-data; boundary=---011000010111000001101001");
    RequestBody body = RequestBody.create(mediaType, "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"name\"\r\n\r\nJohn\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"template\"\r\n\r\n\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"table_rows\"\r\n\r\n[{\"no\": 2, \"date\": \"01.01.2005\"},{\"no\": 2, \"date\": \"01.01.2010\"},{\"no\": 2, \"date\": \"01.01.2008\"},{\"no\": 2, \"date\": \"01.01.2010\"}]\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"return\"\r\n\r\npdf\r\n-----011000010111000001101001--\r\n\r\n");
    Request request = new Request.Builder()
      .url("http://127.0.0.1:8000/api/convert")
      .post(body)
      .addHeader("Content-Type", "multipart/form-data; boundary=---011000010111000001101001")
      .build();

    Response response = client.newCall(request).execute();</code>
                </pre>
            </div>
        </div>

        <input type="radio" name="my_tabs_1" role="tab" class="tab" aria-label="Response"/>
        <div role="tabpanel" class="tab-content py-4 overflow-auto max-w-screen-lg">
            <div class="mockup-code">
                <pre>
                    <code>
    HTTP/1.1 200 OK
    Content-Disposition: attachment; filename=random_string.docx
    Content-Length: 49807
    Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document

    or

    HTTP/1.1 200 OK
    Content-Disposition: attachment; filename=random_string.pdf
    Content-Length: 49807
    Content-Type: application/pdf</code>
                </pre>
            </div>
        </div>
    </div>
</x-layout>
