<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #333;
            margin-bottom: 30px;
        }

        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #00ff51e6;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        /* button:hover {
            background-color: #0056b3;
        } */

        @media screen and (max-width: 500px) {
            body {
                padding: 20px;
            }

            form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <h2>Enter OTP</h2>

    <form method="post" action="<?= site_url('otp/verify_otp') ?>">
        <input type="text" name="otp" placeholder="Enter OTP" required />
        <button type="submit">Verify</button>
    </form>

</body>
</html>
