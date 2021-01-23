<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width" />
    <base href="{{url('/')}}">
    <link
        href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700italic,700'
        rel='stylesheet' type='text/css'>

    <title>Scratch Card</title>
    <style type="text/css">
        html {
            box-sizing: border-box;
        }

        *,
        *:before,
        *:after {
            box-sizing: inherit;
        }

        .scratchpad {
            width: 1080px;
            height: 1920px;
            border: solid 10px #FFFFFF;
            margin: 0 auto;
        }

        body {
            background: #efefef;
        }

        .scratch-container {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            width: 100%;
        }

        /* Extra Small Devices, Phones */
        @media only screen and (max-width : 480px) {
            .scratchpad {
                width: 1080px;
                height: 1920px;
            }

            .scratch-container {
                width: 1080px !important;
            }
        }

        /* Custom, iPhone Retina */
        @media only screen and (max-width : 320px) {
            .scratchpad {
                width: 1080px;
                height: 1920px;
            }

            .scratch-container {
                width: 1080px !important;
            }
        }

        .promo-container {
            background: #FFF;
            border-radius: 5px;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            width: 1080px;
            padding: 20px;
            margin: 0 auto;
            text-align: center;
            font-family: 'Open Sans', Arial, Sans-serif;
            color: #333;
            font-size: 16px;
            margin-top: 20px;
        }

        .btn {
            background: #56CFD2;
            color: #FFF;
            padding: 10px 25px;
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 3px;
            -moz-border-radius: 3px;
            -webkit-border-radiuss: 3px;
        }

    </style>

</head>

<body>
    <div class="scratch-container">
        <div id="promo" class="scratchpad"></div>
    </div>
    <div class="promo-container" style="display:none;">
        <div class="promo-code"></div>
        <a href="#" target="_blank" class="btn">Register Now</a>
    </div>


    <script src="js/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/wScratchPad.min.js"></script>

    <script type="text/javascript">
        var promoCode = '';
        var bg1 = "{{$image}}";
        var bg2 = 'https://b.imge.to/2019/07/20/5a8i1.jpg';
        var bg3 = 'https://a.imge.to/2019/07/20/5aJT0.jpg';
        var bgArray = [bg1],
            selectBG = bgArray[Math.floor(Math.random() * bgArray.length)];
        if (selectBG == bg1) {
            promoCode = 'ENTRY TICKET';
        } else if (selectBG == bg2) {
            promoCode = 'ENTRY TICKET';
        }
        if (selectBG == bg2) {
            var promoCode = '';
        }
        $('#promo').wScratchPad({
            size: 400,
            bg: selectBG,
            realtime: true,
            fg: 'https://b.imge.to/2019/07/20/5aAXt.jpg',
            'cursor': 'url("images/coin1.png") 5 5, default',

            scratchMove: function (e, percent) {
                if ((percent > 50) && (promoCode != '')) {
                    $('.promo-container').show();
                    $('body').removeClass('not-selectable');
                    $('.promo-code').html('Your code is: ' + promoCode);
                }
            }
        });

    </script>


</body>

</html>
