<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/css/app.css')
        <title>Tangkaraw</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />


    </head>

    <body class="antialiased">

        <div class="h-screen w-screen  flex items-center justify-center">

            <div class="container flex flex-col md:flex-row items-center justify-between px-5 text-gray-700">
                <div class="w-full lg:w-1/2 mx-3">

                    <div class="text-4xl text-blue-500 font-dark font-extrabold mb-5"> Tangkaraw DILG- BOHOL</div>
                    <p class="text-xl  font-light leading-normal mb-8 justify-center">
                        The DILG is the executive department of the Philippine government responsible for promoting peace and order, ensuring public safety and strengthening local government capability aimed towards the effective delivery of basic services to the citizenry.
                    </p>

                    <a href="/login" class="px-5 inline py-3 text-sm font-medium leading-5 shadow-2xl text-white transition-all duration-400 border border-transparent rounded-lg focus:outline-none bg-blue-600 active:bg-blue-700 hover:bg-blue-800">Get Started</a>
                </div>
                <div class="w-full lg:flex lg:justify-end lg:w-1/2  my-12">
                    <img src="images/Tngkrw Icon.png" class="w-[400px] object-fit" alt="Image">
                </div>
            </div>

            <div class="bg"></div>
            <div class="bg bg2"></div>
            <div class="bg bg3"></div>
        </div>
    </body>


</html>

<style scoped>
    .bg {
        animation: slide 3s ease-in-out infinite alternate;
        background-image: linear-gradient(-60deg, rgb(221, 216, 216) 50%, white 50%);
        bottom: 0;
        left: -50%;
        opacity: .5;
        position: fixed;
        right: -50%;
        top: 0;
        z-index: -1;
    }

    .bg2 {
        animation-direction: alternate-reverse;
        animation-duration: 4s;
    }

    .bg3 {
        animation-duration: 5s;
    }

    @keyframes slide {
        0% {
            transform: translateX(-25%);
        }

        100% {
            transform: translateX(25%);
        }
    }

</style>

