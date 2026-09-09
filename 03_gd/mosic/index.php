<?php
// POSTリクエストが送信されたかつファイルがアップロードされた場合
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    // アップロードされたファイルの一時パスを取得
    $file = $_FILES['image']['tmp_name'];
    // 画像の荒さを取得（デフォルトは20ピクセル）
    $pixelSize = intval($_POST['pixel']) ?: 20;

    if (!file_exists($file)) {
        die('ファイルが見つかりません。');
    }

    // 画像を読み込み、ピクセル化処理を行う
    $upload_file = file_get_contents($file);
    // 文字列から画像リソースを作成
    $src = imagecreatefromstring($upload_file);
    // 画像の幅と高さを取得
    $width = imagesx($src);
    $height = imagesy($src);
    // ピクセル化のための小さい画像の幅と高さを計算
    $smallW = intval($width / $pixelSize);
    $smallH = intval($height / $pixelSize);
    // 小さい画像を作成し、元の画像をリサイズしてコピー
    $small = imagecreatetruecolor($smallW, $smallH);
    // 元の画像を小さい画像にリサイズしてコピー
    imagecopyresampled($small, $src, 0, 0, 0, 0, $smallW, $smallH, $width, $height);
    // 小さい画像を元のサイズに戻してピクセル化
    $pixelated = imagecreatetruecolor($width, $height);
    imagecopyresized($pixelated, $small, 0, 0, 0, 0, $width, $height, $smallW, $smallH);

    // image/png形式で出力
    header('Content-Type: image/png');
    // 出力バッファをクリアしてから画像を出力
    imagepng($pixelated);

    // 画像を保存する場合は以下のようにする
    // imagepng($pixelated, '../images/mosic.png');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pixel Art Generator</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <main class="w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-sky-600 px-6 py-5">
                <h1 class="text-2xl font-bold text-white">Pixel Art Generator</h1>
                <p class="text-sky-100 text-sm mt-1">GD で画像をピクセル風に変換</p>
            </div>

            <div class="p-6 space-y-6">
                <form method="post" enctype="multipart/form-data" class="space-y-6">
                    <!-- 画像選択 -->
                    <div>
                        <label class="block mb-1 font-medium text-slate-700">画像を選択</label>
                        <input type="file" name="image" accept="image/*" required
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <!-- ピクセルの粗さ -->
                    <div>
                        <label class="block mb-1 font-medium text-slate-700">ピクセルの粗さ（推奨: 10〜50）</label>
                        <div class="flex items-center gap-3">
                            <input type="range" id="pixelRange" name="pixel" value="20" min="2" max="100"
                                class="flex-1 accent-sky-600">
                            <span class="font-mono text-sm text-slate-800 bg-slate-100 rounded px-2 py-1">
                                <span id="pixelValue">20</span> px
                            </span>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full rounded-xl bg-sky-600 px-5 py-2 font-semibold text-white hover:bg-sky-700 transition">
                        ピクセル化する
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        const range = document.getElementById('pixelRange');
        const valueDisplay = document.getElementById('pixelValue');
        range.addEventListener('input', () => {
            valueDisplay.textContent = range.value;
        });
    </script>
</body>

</html>
