<div class="container">
    <div class="form-box mt-5 font-weight-bold">
        <?php if (count($errors)): ?>
            <ul class="text-white bg-danger py-2 mw-50">
                <div class="d-flex align-items-stretch error-message">
                    <img src="https://3.bp.blogspot.com/-wf5p8ilIxOQ/U00KH4pCHPI/AAAAAAAAfOk/CcOEZqgGMKY/s400/mark_chuui.png" alt="" class="error">
                    <p>エラー！</p>
                </div>
                <?php foreach ($errors as $error):?>
                    <li><?php echo $error?></li>
                <?php endforeach?>
            </ul>
        <?php endif?>
        <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
        <form action="result" method="POST" class="h-adr mb-5">
            <span class="p-country-name" style="display:none;">Japan</span>
            <div class="form-group">
                <label for="postal-code">郵便番号（例：1000014）</label>
                <input type="text" class="p-postal-code form-control" size="8" maxlength="8" id="postal-code">
            </div>
            <p class="font-weight-normal">■郵便番号を入力すると町名まで<span class="font-weight-bold text-danger">自動的に入力されます。</span></p>
            <p class="font-weight-normal">■郵便番号検索は<a href="http://www.post.japanpost.jp/zipcode/" target="_blank" rel="noopener noreferrer">こちら</a></p>
            <div class="form-group">
                <label for="prefectures">都道府県<span class="text-white bg-danger px-1 rounded">必須</span>（例：東京都）</label>
                <input type="text" class="p-region form-control" name="prefectures" id="prefectures" value="<?php echo $prefectures?>" />
            </div>
            <div class="form-group">
                <label for="municipalities">市区町村<span class="text-white bg-danger px-1 rounded">必須</span>（例：千代田区）</label>
                <input type="text" class="p-locality form-control" name="municipalities" id="municipalities" value="<?php echo $municipalities?>" />
            </div>
            <div class="form-group">
                <label for="street">町名<span class="text-white bg-danger px-1 rounded">必須</span>（例：永田町）</label>
                <input type="text"; class="p-street-address form-control" name="street" id="street" value="<?php echo $street?>" />
            </div>
            <div class="form-group">
                <label for="address">番地（例：1-7-1）</label>
                <input type="text"; class="p-extended-address form-control" name="address" id="address" value="<?php echo $extendAddress?>" />
            </div>
            <div>
                <button type="submit" class="btn btn-primary mb-5 p-4 font-weight-bold h1">解析開始</button>
            </div>
        </form>
    </div>
</div>
<div class="overview text-white font-weight-bold text-center py-5">
    <div class="my-5">
        <h1>1. TOWN SELECTとは？</h1>
        <h2>「TOWN SELECT」は、より良い街を選択するための<span class="d-inline-block">Webサービスです。</span></h2>
    </div>
    <div class="my-5">
        <h1>2. TOWN SELECTでわかること</h1>
        <ul class="list-unstyled">
            <li>水害の可能性</li>
            <li>財政健全化<span class="d-inline-block">判断比率</span></li>
            <li>人口動態の<span class="d-inline-block">傾向</span></li>
            <li>医療施設数</li>
        </ul>
    </div>
    <div class="my-5">
        <h1>3. ツールの使い方は？</h1>
        <h2>住所を入力し、「解析開始」ボタンを押すだけ！</hS2>
    </div>
    <a href="explain" class="btn btn-warning text-light my-5 p-4 font-weight-bold ">詳細はこちら</a>
</div>
