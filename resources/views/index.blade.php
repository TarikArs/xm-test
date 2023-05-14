<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Home</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap.min.css" integrity="sha512-Ez0cGzNzHR1tYAv56860NLspgUGuQw16GiOOp/I2LuTmpSK9xDXlgJz3XN4cnpXWDmkNBKXR/VDMTCnAaEooxA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        .error {
            color: red;
        }

        form div {
            margin-bottom: 10px;

        }

        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            background-color: #f2f2f2;
        }

        .form-container form {
            max-width: 400px;
            margin: 0 auto;
        }

        .form-container label {
            display: block;
            margin-bottom: 10px;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        .form-container div.error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
    <script>
        $(function() {
            $(".date_input").datepicker({
                dateFormat: 'yy-mm-dd'
            });

            function formValidation() {
                let valid = true;
                const company_symbol = $("#company_symbol").val();
                const start_date = $("#start_date").val();
                const end_date = $("#end_date").val();
                const email = $("#email").val();
                let message = '';
                // Reset error messages
                $(".error").text("");

                if (company_symbol === "") {
                    message += "Please enter company symbol<br/>";
                    console.log("company_symbol", company_symbol);
                    valid = false;
                }

                if (start_date === "") {
                    message += "Please enter start date<br/>";
                    valid = false;
                } else if (!isValidDate(start_date)) {
                    message += "Please enter a valid start date<br/>";
                    valid = false;
                }

                if (end_date === "") {
                    alert("Please enter end date<br/>");
                    valid = false;
                } else if (!isValidDate(end_date)) {
                    message += "Please enter a valid end date<br/>";
                    valid = false;
                }

                if (email === "") {
                    message += "Please enter email<br/>";
                    valid = false;
                } else if (!isValidEmail(email)) {
                    message += "Please enter a valid email<br/>";
                    valid = false;
                }

                if (valid) {
                    const startDateObj = new Date(start_date);
                    const endDateObj = new Date(end_date);
                    const currentDateObj = new Date();

                    if (startDateObj > endDateObj) {
                        message += "Start date must be less than or equal to end date<br/>";
                        valid = false;
                    } else if (startDateObj > currentDateObj) {
                        message += "Start date must be less than or equal to the current date<br/>";
                        valid = false;
                    }

                    if (endDateObj < startDateObj) {
                        message += "End date must be greater than or equal to start date<br/>";
                        valid = false;
                    } else if (endDateObj > currentDateObj) {
                        message += "End date must be less than or equal to the current date<br/>";
                        valid = false;
                    }
                }
                $(".error-message").html(message);

                return valid;
            }


            function isValidDate(dateString) {
                var regEx = /^\d{4}-\d{2}-\d{2}$/;
                if (!dateString || typeof dateString !== 'string' || !dateString.match(regEx)) {
                    return false;
                }
                var d = new Date(dateString);
                var dNum = d.getTime();
                if (!dNum && dNum !== 0) {
                    return false;
                }
                return d.toISOString().slice(0, 10) === dateString;
            }

            function isValidEmail($email) {
                var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                return emailReg.test($email);
            }
            $('#fetchApiForm').submit(function(event) {
                event.preventDefault(); // Prevent default form submission

                if (formValidation()) {
                    // Validation passed, submit the form
                    this.submit();
                }
            });

        });
    </script>
</head>

<body class="antialiased">
    <div class="form-container">
        @if($errors->has('error'))
        <div class="error">
            {{ $errors->first('error') }}
        </div>
        @endif
        <form id="fetchApiForm" action="{{ route('fetchApi') }}" method="POST">
            @csrf
            <div class="error-message error"></div>
            <div>
                <label for="company_symbol">Company Symbol</label>
                <input type="text" name="company_symbol" id="company_symbol" value="{{ old('company_symbol') }}" required>
                @error('company_symbol')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="start_date">Start Date</label>
                <input type="text" class="date_input" name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                @error('start_date')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="end_date">End Date</label>
                <input type="text" class="date_input" name="end_date" id="end_date" value="{{ old('end_date') }}" required>
                @error('end_date')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                @error('email')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <button type="submit">Submit</button>
            </div>
        </form>
    </div>
</body>


</html>