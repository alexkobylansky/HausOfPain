@php
    $video_id = str_replace("/videos/", "", $content);
@endphp
<div id="iframeContainer">
    @if ($content)
        <iframe class="form-control" style="height: 512px;" src="https://player.vimeo.com/video/{{$video_id}}"
                width="100%" height="512" frameborder="0" allow="autoplay; fullscreen" allowfullscreen
                video-id="{{$video_id}}" id="videoIframe"
        ></iframe>
    @else
        <div style="display: flex; background-color: #eee; justify-content: space-around; align-items: center; height: 512px;">
            <div style="color: black; font-size: 2rem;">No video uploaded yet</div>
        </div>
    @endif
</div>
<label class="control-label" for="file">
    @if ($content)
        Replace video:
    @else
        Upload video:
    @endif
</label>
<input class="form-control" style="height: unset;" type="file" id="file" />
<div class="form-control" id="progressBarWrapper" style="height: unset;">
    <div id="videoProgressBar">
        <div id="videoProgressBarText"></div>
    </div>
</div>

<style>
    #progressBarWrapper {
        padding: 20px;
        display: none;
    }
    #videoProgressBar {
        display: flex;
        justify-content: space-around;
        background-color: #22A7F0;
        height: 25px;
        width: 1px;
    }
    #videoProgressBarText {
        display: none;
        padding-top: 1px;
        color: white;
        font-weight: 600;
    }
</style>

<script src="{{ asset('/js/tus.min.js') }}"></script>
<script src="{{ asset('/js/axios.min.js') }}"></script>
<script>
    let input = document.getElementById('file')
    let iframe = document.getElementById('videoIframe')
    let progressWrapper = document.getElementById('progressBarWrapper')
    let progress = document.getElementById('videoProgressBar')
    let progressText = document.getElementById('videoProgressBarText')
    input.addEventListener("change", function(e) {
        // Get the selected file from the input element
        var file = e.target.files[0]

        setProgress(1);

        // Create a new tus upload
        var upload = new tus.Upload(file, {
            endpoint: "/admin/upload/exercise-video",
            retryDelays: [0, 3000, 5000, 10000, 20000],
            metadata: {
                filename: iframe ? iframe.getAttribute('video-id') : 'new',
                filetype: file.type
            },
            onError: function(error) {
                alert("An error occured: " + error)
                hideProgress()
            },
            onProgress: function(bytesUploaded, bytesTotal) {
                let percentage = (bytesUploaded / bytesTotal * 100).toFixed(0)
                setProgress(percentage);
            },
            onSuccess: function() {
                showUploadingToVimeo()
                let fileHash = upload.url.substring(upload.url.lastIndexOf('/')+1)
                let formData = {};
                if (!iframe) {
                    let elements = [
                        ...document.getElementsByTagName('input'),
                        ...document.getElementsByTagName('select'),
                        ...document.getElementsByTagName('textarea'),
                    ]
                    for (let inputElement of elements) {
                        formData[inputElement.getAttribute('name')] = inputElement.value;
                    }
                }
                axios.post("/admin/videos/push-to-vimeo/" + fileHash, formData).then(function(response) {
                    hideProgress()
                    if(response.data['id']) {
                        window.location = '/admin/videos/' + response.data['id'] + '/edit';
                    } else {
                        let iframeCode = iframe.outerHTML;
                        iframe.remove();
                        iframe = document.getElementById('iframeContainer').appendChild(htmlToElement(iframeCode));
                    }
                })
            }
        })

        // Start the upload
        upload.start()
    })

    function setProgress(value) {
        input.style.display = 'none';
        progressWrapper.style.display = 'block';
        progress.style.width = value + '%';
        progressText.style.display = 'block';
        progressText.innerHTML = value + '%';
    }

    function hideProgress() {
        progressWrapper.style.display = 'none';
        progressText.style.display = 'none';
        progressText.innerHTML = '';
        input.style.display = 'block';
    }

    function showUploadingToVimeo() {
        progressText.innerHTML = 'Uploading to Vimeo...';
    }

    function htmlToElement(html) {
        var template = document.createElement('template');
        html = html.trim();
        template.innerHTML = html;
        return template.content.firstChild;
    }

</script>
