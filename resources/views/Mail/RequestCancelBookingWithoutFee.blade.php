<head>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        main {
            width: 90%;
            margin: 0 auto;
        }

        .logo {
            width: 116px;
            padding: 12px;
        }

        .fondo-header {
            background-image: url("https://i.ibb.co/s5zNDW2/header.png");
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            height: 401px;
            width: 100%;
        }


        .container-dos {
            margin-top: 52px;
            width: 209px;
        }


        h1, h4 {
            text-align: center;
            margin: 20px 0;
        }

        p {
            text-align: center;
        }

        b {
            color: #2d2064;
            font-size: 2rem;
            display: block;
            text-align: center;
            margin: 20px 0;
        }
        a{
            text-decoration: none !important;
        }

        .redirImg:hover {
            cursor: pointer;
        }
        .imgGoogle{

            height: 70px;
            width: auto;
        }

        .imgAppstore {
            width: 194px;
        }


        footer {
            background-color: #2d2064;
            color: white;
            padding: 20px;
        }

        .container-footer {
            position: relative;
            background-color: #2d2064;

        }



        @media screen and (min-width: 320px) and (max-width: 502px){
            .fondo-header {

                background-position: center;
                height: 161px;
                width: 100%;
                background-size: contain;
            }

            .container-dos {
                margin-top: 52px;
                width: 247px;
            }

            .imgAppstore {
                width: 238px;
            }

            .margenesmovil {
                margin-top: 10px;
            }
            .parrafo-terminos {
                padding: 5px;
            }
        }

        @media screen and  (min-width: 768px) {

            .container-footer {
                height: 203px;
                /*padding: 0 20px;*/
            }

        }
    </style>
</head>
<body>
<main>
    <div>
        <img src="https://i.ibb.co/k5yxSmm/logo-amera.png" alt="logo-amera" class="logo">

        <div class="fondo-header">

        </div>
        <h1>Hi, {{ $NAME }}</h1>
        <div style="padding: 5px">
            <p style="padding: 15px">
                Thank you for giving us more than 24 hours noticed.
                We're sorry your plans didn't work out. When you're ready to book a new reservation with Amera we'll be happy to assist.
            </p>
            <br>
        </div>
        <div style="text-align: center; margin: 30px 0">
            <a class="redirImg" href="">
                <img class="imgGoogle" src="https://i.ibb.co/bK60xSB/get-it-on-google-play.png" alt="logo-google-play">
            </a>
            <a class="redirImg" href="">
                <img class="imgAppstore margenesmovil" src="https://i.ibb.co/kGVYmh4/APP-STORE.png"
                     alt="logo-app-store">

            </a>
        </div>
    </div>
    <footer>
        <div class="container-footer" style="text-align: center">
            <div class="container-uno">
                <span style="display: inline-block; width: 281px;">
                <p class="parrafo-terminos" style="text-align: start">
                        Serving cities in add around
                        Houston, Dallas, San Antonio &
                        Austin, Texas Available in
                        Jacksonville & Tampa, Florida
                    </p>
                    <br><br>

                </span>

                <span class="container-dos" style="display: inline-block;">
                    <p>
                        855-AMERA-15 <br>
                        866-948-7616
                    </p>
                    <br><br><br>
                </span>

                <span style="display: inline-block">
                <a  class="redirImg" style="text-decoration: none" href="">
                    <img style="width: 32px" src="https://i.ibb.co/F57wVTW/facebook-8.png" width="32">
                </a>

                <a  class="redirImg" style="text-decoration: none" href="">
                    <img style="width: 32px" src="https://i.ibb.co/GxwdKrZ/Instagram.png" width="32">
                </a>

                <a  class="redirImg" style="text-decoration: none" href="">
                    <img style="width: 32px" src="https://i.ibb.co/1dc2mgB/Whatsaap-8.png" width="32">
                </a>

                <a  class="redirImg" style="text-decoration: none" href="">
                    <img style="width: 32px" src="https://i.ibb.co/dB0wKjn/twitter-8.png" width="32">
                </a>
                    <br><br><br>
                    <br>
            </span>

            </div>
        </div>
    </footer>
</main>
