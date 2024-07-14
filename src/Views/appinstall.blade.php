<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Step Form with AJAX</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .step {
            display: none;
        }

        .step.active {
            display: block;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="alert alert-success d-none" id="successmessage"></div>
        <div class="alert alert-danger d-none" id="errormessage"></div>
        <h2 class="mb-4">Laravel App Installer</h2>
        <div id="step1" class="step active">
            <h3>Database Confriguration</h3>
            <form id="formStep1" submiturl="{{ route('appinstaller.dbsetup') }}">
                <div class="form-group">
                    <labelme">DB Host:</label>
                        <input type="text" class="form-control" id="db_host" name="db_host" required>
                        <span style="color: red" id="db_host_error"></span>
                </div>
                <div class="form-group">
                    <labelme">DB Username:</label>
                        <input type="text" class="form-control" id="db_username" name="db_username" required>
                        <span style="color: red" id="db_username_error"></span>
                </div>
                <div class="form-group">
                    <labelme">DB Password:</label>
                        <input type="text" class="form-control" id="db_password" name="db_password" required>
                        <span style="color: red" id="db_password_error"></span>
                </div>
                <div class="form-group">
                    <labelme">DB Name:</label>
                        <input type="text" class="form-control" id="db_name" name="db_name" required>
                        <span style="color: red" id="db_name_error"></span>
                </div>
                <button type="button" class="btn btn-primary" onclick="nextStep(2, 'formStep1')">Next</button>
            </form>
        </div>
        <div id="step2" class="step">
            <h3>Email Setup</h3>
            <form id="formStep2" submiturl="{{ route('appinstaller.emailsetup') }}">
                <div class="form-group">
                    <label>Email Host :</label>
                    <input type="text" class="form-control" id="email_host" name="email_host" required>
                    <span style="color: red" id="email_host_error"></span>
                </div>
                <div class="form-group">
                    <label>Email Port:</label>
                    <input type="text" class="form-control" id="email_port" name="email_port" required>
                    <span style="color: red" id="email_port_error"></span>
                </div>
                <div class="form-group">
                    <label>Email Encryption:</label>
                    <input type="text" class="form-control" id="email_encryption" name="email_encryption" required>
                    <span style="color: red" id="email_encryption_error"></span>
                </div>
                <div class="form-group">
                    <label>Email Username:</label>
                    <input type="text" class="form-control" id="email_username" name="email_username" required>
                    <span style="color: red" id="email_username_error"></span>
                </div>
                <div class="form-group">
                    <label>Email Password:</label>
                    <input type="text" class="form-control" id="email_password" name="email_password" required>
                    <span style="color: red" id="email_password_error"></span>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="text" class="form-control" id="email" name="email" required>
                    <span style="color: red" id="email"></span>
                </div>
                <div class="form-group">
                    <label>Sender Name:</label>
                    <input type="text" class="form-control" id="sender_name" name="sender_name" required>
                    <span style="color: red" id="sender_name"></span>
                </div>
                <button type="button" class="btn btn-secondary" onclick="nextStep(1, 'formStep2')">Previous</button>
                <button type="button" class="btn btn-primary" onclick="nextStep(3, 'formStep2')">Next</button>
            </form>
        </div>
        <div id="step3" class="step">
            <h3>License Details</h3>
            <form id="formStep3" submiturl="{{ route('appinstaller.install') }}">
                <div class="form-group">
                    <label>Verification URL:</label>
                    <input type="text" class="form-control" id="verify_url" name="verify_url" required>
                    <span style="color: red" id="verify_url_error"></span>
                </div>
                <div class="form-group">
                    <label>Licence Code:</label>
                    <input type="text" class="form-control" id="licence_code" name="licence_code" required>
                    <span style="color: red" id="licence_code_error"></span>
                </div>
                <div class="form-group">
                    <label>Redirect URL:</label>
                    <input type="text" class="form-control" id="redirect_url" name="redirect_url" required>
                    <span style="color: red" id="redirect_url_code"></span>
                </div>
                <button type="button" class="btn btn-secondary" onclick="nextStep(2, 'formStep3')">Previous</button>
                <button type="button" class="btn btn-success" onclick="submitForm()">Install</button>
            </form>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function nextStep(step, formId) {
            $('span').text('');
            $('#successmessage').addClass('d-none');
            $('#errormessage').addClass('d-none');

            var formData = $('#' + formId).serialize();
            var url = $('#' + formId).attr('submiturl')
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('.step').removeClass('active');
                        $('#step' + step).addClass('active');
                        $('#successmessage').text(response.message);
                        $('#successmessage').removeClass('d-none');
                    } else {
                        $('#errormessage').text(response.message);
                        $('#errormessage').removeClass('d-none');
                    }
                },
                error: function(xhr, status, error) {
                    var statusCode = xhr.status;
                    var responseJson = JSON.parse(xhr.responseText);
                    if (statusCode == 422) {
                        var errors = responseJson.errors;
                        $.each(errors, function(index, value) {
                            $('#' + index + '_error').text(value);
                        });
                    }
                }
            });
        }

        function submitForm() {
            $('span').text('');
            $('#successmessage').addClass('d-none');
            $('#errormessage').addClass('d-none');
            var formData = $('#formStep3').serialize();
            var url = $('#formStep3').attr('submiturl')
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('.step').removeClass('active');
                        $('#step' + step).addClass('active');
                        $('#successmessage').text(response.message);
                        $('#successmessage').removeClass('d-none');
                    } else {
                        $('#errormessage').text(response.message);
                        $('#errormessage').removeClass('d-none');
                    }
                },
                error: function(xhr, status, error) {
                    var statusCode = xhr.status;
                    var responseText = xhr.responseText;
                    if (statusCode == 422) {
                        $('#' + index + '_error').text(value);
                    }
                }
            });
        }
    </script>
</body>

</html>
