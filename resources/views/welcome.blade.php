<x-guest-layout>
    <div class="h-screen pb-14 bg-right bg-cover">
        <div class="container pt-10 md:pt-18 px-6 mx-auto flex flex-wrap flex-col md:flex-row items-center bg-yellow-50">
            <!--左側-->
            <div class="mv-left xl:w-1/5 py-6 overflow-y-hidden">
                <img class="" src="{{asset('logo/site-logo.png')}}">
            </div>
            {{-- 右側 --}}
            <div class="mv-right flex flex-col xl:w-2/5 justify-center lg:items-start overflow-y-hidden ">
                <h1 class="my-4 text-3xl md:text-5xl text-green-800 font-bold leading-tight text-center md:text-left slide-in-bottom-h1">ぼやきの広場</h1>
                <p class="leading-normal text-base md:text-2xl mb-8 text-center md:text-left slide-in-bottom-subtitle">
                    仕事や勉強の合間に、なにかをぼやく。<br>プログラマの集い場です。<br>プログラミングの話題もウェルカム♪
                </p>

                <p class="text-red-600  text-l md:text-xl font-bold pb-3 lg:pb-2 text-center md:text-left fade-in">
                    会員募集中。
                </p>

                <p class="pb-8 lg:pb-6 text-center md:text-left fade-in">
                   お気軽にひょっこりきてください。
                </p>
                <div class="flex w-full justify-center md:justify-start pb-24 lg:pb-0 fade-in ">
                    {{-- ボタン設定 --}}
                    <x-primary-button class="btnsetg"><a href="{{ route('contact.create') }}">お問い合わせ</a></x-primary-button>
                    <a href="{{ route('register') }}"><x-primary-button class="btnsetr">ご登録はこちら</x-primary-button></a>

                </div>
            </div>
        </div>
        <div class="container pt-10 md:pt-18 px-6 mx-auto flex flex-wrap flex-col md:flex-row items-center">
            <div class="w-full text-sm text-center md:text-left fade-in border-2 p-4 text-red-800 leading-8 mb-8">
                <P> ここは色々いれてください。</p>
            </div>
            <!--フッタ-->
            <div class="w-full pt-10 pb-6 text-sm md:text-left fade-in">
                <p class="text-gray-500 text-center">@2024 独り言広場</p>
            </div>
        </div>
    </div>
    </x-guest-layout>
