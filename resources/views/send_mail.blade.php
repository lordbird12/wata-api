<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- lib -->
    <script src={{ asset('lib/bootstrap-4.3.1-dist/js/bootstrap.min.js') }}></script>
    <link rel="stylesheet" href="{{ URL::asset('lib/bootstrap-4.3.1-dist/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ URL::asset('lib/fontawesome-free-5.13.0-web/css/all.css') }}" />

    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #010e14;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links>a {
            color: black;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .font {
            color: black;
        }
    </style>

</head>

<body>

    @if($type == 'Meeting Zoom With Docter')
    <table width="950" align="center" class="tb_s2">
        <tbody>
            <tr>
                <td>{{$title}}</td>
            </tr>
            <tr>
                <td>{{$data}}</td>
            </tr>

        </tbody>
    </table>
    @endif





</body>

</html>
