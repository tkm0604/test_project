<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            投稿の一覧
        </h2>

        <x-message :message="session('message')" />

    </x-slot>

    {{-- 投稿一覧表示用のコード --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (count($comments) === 0)
        <p class="mt-4">投稿がありません。</p>
        @else
            @foreach ($comments->unique('post_id') as $comment)
            @php
                $post = $comment->post;
             @endphp
                <div class="sm:p-8">
                    <div class="mt-4">
                        <div
                        class="bg-white w-full  rounded-2xl px-10 py-8 shadow-lg hover:shadow-2xl transition duration-500 pb-10">
                        <div class="mt-4">
                            <div class="rounded-full w-12 h-12">
                                {{-- アバター表示 --}}
                                <img class="rounded-full" src="{{ asset('storage/avatar/' . ($post->user->avatar ?? 'user_default.jpg')) }}">
                            </div>
                            <h1 class="text-lg text-gray-700 font-semibold hover:underline cursor-pointer">
                                <a href="{{ route('post.show', $post) }}">
                                    {{ $post->title }}
                                </a>
                            </h1>
                            <hr class="w-full">
                            <p class="mt-4 text-gray-600 py-4">{{ Str::limit($post->body, 100, '...') }}</p>
                            <div class="text-sm font-semibold flex flex-row-reverse mb-1">
                                <p>{{ $post->user->name ?? '削除されたユーザー' }} • {{ $post->created_at->diffForHumans() }}</p>
                            </div>
                            <hr class="w-full mb-2">
                            @if ($post->comments->count())
                                <span class="badge">
                                    返信 {{ $post->comments->count() }} 件
                                </span>
                            @else
                                <span class="text-sm lg:text-base">コメントはまだありません</span>
                            @endif
                            @if (auth()->id() === $post->user_id)
                                <div class="w-full mb-2">
                                    <p class="text-sm lg:text-base">{{ $post->views }}人がこの投稿を閲覧しました。</p>
                                </div>
                            @endif
                            <a href="{{ route('post.show', $post) }}">
                                <x-primary-button
                                    class="float-right m-0 text-sm lg:text-base">コメントする</x-primary-button>
                            </a>
                        </div>
                    </div>
                    </div>
                </div>
            @endforeach
        @endif
            <!-- ページネーションリンクを追加 -->
    {{ $comments->links('vendor.pagination.tailwind') }}
    </div>
</x-app-layout>
