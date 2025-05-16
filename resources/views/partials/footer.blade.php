{{-- フッター部分 --}}
{{-- ランディングページで使用したフッターをベースにします --}}
<footer class="bg-gray-800 text-gray-300 py-8 text-sm">
    <div class="container mx-auto px-4 text-center">
        <p class="mb-4">&copy; {{ date('Y') }} Subscru. All rights reserved.</p>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">プライバシーポリシー</a>
            <a href="{{ route('terms') }}" class="hover:text-white transition-colors">利用規約</a>
            <a href="#" class="hover:text-white transition-colors">お問い合わせ</a>
        </div>
    </div>
</footer>
