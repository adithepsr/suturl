<?php
$menu = "index";
?>
<?php include("includes/header.php"); ?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <meta charset="UTF-8">
    <div class="container-fluid">
        <h1><i class="nav-icon fas fa-link"></i> Short-URL and QR Code Generator | SUT</h1>
        <p class="text-muted">Service to shorten URLs and generate QR Codes for easy sharing</p>
    </div>
</section>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<!-- Connect to Noto Sans Thai font from Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-success shadow-lg">
            <div class="card-header">
                <h3 class="card-title">Enter the URL to shorten:</h3>
            </div>
            <div class="card-body">
                <form id="shortenerForm" class="p-3">
                    <div class="mb-4">
                        <label for="urlInput" class="form-label">URL:</label>
                        <div class="input-group">
                            <input id="urlInput" type="text" class="form-control" placeholder="https://example.com">
                            <button type="submit" class="btn btn-success"><i class="fas fa-magic"></i> Generate</button>
                        </div>
                        <small class="form-text text-muted">Please enter the URL, e.g., https://example.com</small>
                    </div>
                </form>

                <div id="result" style="display:none;">
                    <div class="mb-4">
                        <label for="shortUrl" class="form-label">Generated Short URL:</label>
                        <div class="input-group">
                            <input id="shortUrl" type="text" class="form-control text-primary fw-bold" readonly>
                            <button id="copyBtn" class="btn btn-secondary"><i class="fas fa-copy"></i> Copy</button>
                        </div>
                    </div>
                    <div id="qrcodeContainer" class="text-center mt-4">
                        <canvas id="qrcode"></canvas>
                        <br>
                        <a id="downloadLink" href="#" class="btn btn-primary mt-3" download><i class="fas fa-download"></i> Download QR Code</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>

<script>
$(document).ready(function () {
    $('#shortenerForm').on('submit', function (e) {
        e.preventDefault();
        const url = $('#urlInput').val();

        if (url && isValidUrl(url)) {
            const shortUrl = `https://go.sut.ac.th/${generateShortCode()}`; // Change domain 
            const date = new Date().toLocaleDateString();

            const qrCanvas = document.createElement('canvas');
            QRCode.toCanvas(qrCanvas, url, { width: 1000, height: 1000, errorCorrectionLevel: 'H' }, function (error) {
                if (error) {
                    Swal.fire('Error!', 'Unable to generate QR Code', 'error');
                    return;
                }
                const qrImage = qrCanvas.toDataURL();

                $('#shortUrl').val(shortUrl);
                $('#qrcode').remove(); // Clear old QR Code
                $('#qrcodeContainer').append(qrCanvas);
                $('#downloadLink').attr('href', qrImage);
                $('#result').show();

                const urlData = {
                    date: date,
                    original: url,
                    short: shortUrl,
                    qrcode: qrImage
                };

                // Send data to save_url.php to save into the database
                $.post('save_url.php', { urlData: JSON.stringify(urlData) }, function (response) {
                    console.log('Saved URL Data', response);

                    Swal.fire('Success!', 'Short URL and QR Code generated and saved successfully!', 'success');

                    // Clear form after 10 seconds
                    setTimeout(function() {
                        $('#urlInput').val('');
                        $('#result').hide();
                        $('#qrcode').remove();
                        $('#shortUrl').val('');
                    }, 10000); // 10 seconds = 10000 ms
                }).fail(function() {
                    Swal.fire('Error!', 'Unable to save data', 'error');
                });
            });
        } else {
            Swal.fire('Error!', 'Invalid URL', 'error');
        }
    });

    $('#copyBtn').on('click', function () {
        const shortUrl = $('#shortUrl').val();
        navigator.clipboard.writeText(shortUrl).then(() => {
            Swal.fire('Copied!', 'Short URL copied to clipboard', 'success');
        });
    });

    function isValidUrl(url) {
        const pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|'+ // domain name
            '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
            '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
            '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
        return !!pattern.test(url);
    }

    function generateShortCode() {
        const characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let shortCode = '';
        for (let i = 0; i < 6; i++) {
            shortCode += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        return shortCode;
    }
});
</script>

<!-- Include index.css -->
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/style.css">